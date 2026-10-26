<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Models\EggProduct;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function home(InventoryService $inventory): View
    {
        $products = EggProduct::query()
            ->where('status', 'active')
            ->orderBy('product_name')
            ->limit(3)
            ->get();

        $this->attachAvailability($products, $inventory);

        return view('customer.home', compact('products'));
    }

    public function create(Request $request, InventoryService $inventory): View
    {
        $customer = $this->currentCustomer($request);
        $products = EggProduct::query()
            ->where('status', 'active')
            ->orderBy('product_name')
            ->get();

        $this->attachAvailability($products, $inventory);

        return view('customer.order', compact('products', 'customer'));
    }

    public function store(Request $request, InventoryService $inventory): RedirectResponse
    {
        $customer = $this->currentCustomer($request);

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'contact' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:egg_products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:0', 'max:1000'],
        ]);

        $items = collect($data['items'])
            ->filter(fn (array $item): bool => (int) $item['quantity'] > 0)
            ->values();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Choose at least one product that is currently available before submitting the order.',
            ]);
        }

        $order = DB::transaction(function () use ($data, $items, $customer, $inventory): Order {
            /* Lock product rows in a consistent order to serialize overlapping customer requests. */
            $productIds = $items
                ->pluck('product_id')
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->sort()
                ->values();

            $lockedProducts = EggProduct::query()
                ->whereIn('id', $productIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy(fn (EggProduct $product): int => (int) $product->id);

            $preparedItems = [];

            foreach ($items as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];
                $product = $lockedProducts->get($productId);

                if (! $product || $product->status !== 'active') {
                    throw ValidationException::withMessages([
                        'items' => 'One of your selected products is no longer available. Refresh the page and try again.',
                    ]);
                }

                $available = $inventory->availableForOrdering($productId, true);

                if ($quantity > $available) {
                    $message = $available === 0
                        ? $product->product_name . ' is currently unavailable because no stock is available to order.'
                        : 'Only ' . $available . ' ' . $product->unit . '(s) of ' . $product->product_name . ' can currently be ordered. Reduce the quantity and try again.';

                    throw ValidationException::withMessages(['items' => $message]);
                }

                $preparedItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => (float) $product->price,
                ];
            }

            $customer->update([
                'name' => trim($data['customer_name']),
                'contact' => trim($data['contact']),
                'email' => $data['email'] ?? null,
                'address' => trim($data['address']),
            ]);

            $order = Order::query()->create([
                'customer_id' => $customer->id,
                'order_number' => $this->generateOrderNumber(),
                'order_date' => now(),
                'estimated_total' => 0,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            $total = 0.0;

            foreach ($preparedItems as $preparedItem) {
                /** @var EggProduct $product */
                $product = $preparedItem['product'];
                $quantity = $preparedItem['quantity'];
                $unitPrice = $preparedItem['unit_price'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                $total += $quantity * $unitPrice;
            }

            $order->update(['estimated_total' => $total]);

            return $order;
        }, 3);

        return redirect()->route('customer.confirmation', $order)
            ->with('success', 'Your order request was submitted successfully. The requested quantities are reserved while the administrator reviews your order.');
    }

    public function confirmation(Request $request, Order $order): View
    {
        $customer = $this->currentCustomer($request);
        abort_unless((int) $order->customer_id === (int) $customer->id, 403);

        $order->load('items.product', 'customer');

        return view('customer.confirmation', compact('order'));
    }

    /** Show the signed-in customer's complete order history with live database statuses. */
    public function myOrders(Request $request): View
    {
        $customer = $this->currentCustomer($request);

        $customerOrders = Order::query()->where('customer_id', $customer->id);
        $statusTotals = (clone $customerOrders)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $counts = [
            'pending' => (int) ($statusTotals['pending'] ?? 0),
            'outgoing' => (int) ($statusTotals['outgoing'] ?? 0),
            'done' => (int) ($statusTotals['done'] ?? 0),
            'cancelled' => (int) ($statusTotals['cancelled'] ?? 0),
        ];

        $orders = (clone $customerOrders)
            ->with(['items.product', 'customer'])
            ->latest('order_date')
            ->paginate(10)
            ->withQueryString();

        return view('customer.my-orders', compact('customer', 'orders', 'counts'));
    }

    public function trackForm(): View
    {
        return view('customer.track-order');
    }

    public function track(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'order_number' => ['required', 'string', 'max:30'],
        ]);

        $customer = $this->currentCustomer($request);
        $order = Order::query()
            ->with('items.product', 'customer')
            ->where('order_number', strtoupper(trim($data['order_number'])))
            ->where('customer_id', $customer->id)
            ->first();

        if (! $order) {
            return back()->withErrors([
                'order_number' => 'We could not find that order in your customer account.',
            ])->withInput();
        }

        return view('customer.track-order', compact('order'));
    }

    private function attachAvailability($products, InventoryService $inventory): void
    {
        foreach ($products as $product) {
            $product->current_stock = $inventory->balance((int) $product->id);
            $product->available_to_order = $inventory->availableForOrdering((int) $product->id);
        }
    }

    private function currentCustomer(Request $request): Customer
    {
        $account = CustomerAccount::query()
            ->with('customer')
            ->find($request->session()->get('customer_account_id'));

        abort_unless($account && $account->customer, 403);

        return $account->customer;
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
