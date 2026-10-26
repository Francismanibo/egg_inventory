<?php

namespace App\Http\Controllers;

use App\Models\EggProduct;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function index(string $status = 'pending'): View
    {
        abort_unless(in_array($status, ['pending', 'outgoing', 'done', 'cancelled'], true), 404);

        $orders = Order::query()
            ->with('customer', 'items.product')
            ->where('status', $status)
            ->latest('order_date')
            ->paginate(12)
            ->withQueryString();

        $counts = [
            'pending' => Order::query()->where('status', 'pending')->count(),
            'outgoing' => Order::query()->where('status', 'outgoing')->count(),
            'done' => Order::query()->where('status', 'done')->count(),
            'cancelled' => Order::query()->where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('status', 'orders', 'counts'));
    }

    /** Accept a pending order only when all requested quantities remain in stock. */
    public function accept(Order $order, InventoryService $inventory): RedirectResponse
    {
        DB::transaction(function () use ($order, $inventory): void {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedOrder->status !== 'pending') {
                throw ValidationException::withMessages([
                    'order' => 'This order has already been processed.',
                ]);
            }

            $lockedOrder->load('items.product');
            $productIds = $lockedOrder->items
                ->pluck('product_id')
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->sort()
                ->values();

            /* Lock the same product rows in sorted order used by customer submissions. */
            $lockedProducts = EggProduct::query()
                ->whereIn('id', $productIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy(fn (EggProduct $product): int => (int) $product->id);

            foreach ($lockedOrder->items as $item) {
                $product = $lockedProducts->get((int) $item->product_id);

                if (! $product || $product->status !== 'active') {
                    throw ValidationException::withMessages([
                        'order' => 'Cannot accept this order because ' . ($product?->product_name ?? 'one product') . ' is no longer active.',
                    ]);
                }

                $available = $inventory->balance((int) $item->product_id, true);

                if ($available < (int) $item->quantity) {
                    throw ValidationException::withMessages([
                        'order' => 'Cannot accept the order. Only ' . $available . ' ' . $product->unit . '(s) of ' . $product->product_name . ' remain in stock, but the order requests ' . (int) $item->quantity . '.',
                    ]);
                }
            }

            foreach ($lockedOrder->items as $item) {
                InventoryTransaction::query()->create([
                    'product_id' => $item->product_id,
                    'transaction_type' => 'deduction',
                    'quantity' => $item->quantity,
                    'reason' => 'Customer Order ' . $lockedOrder->order_number,
                    'transaction_date' => now()->toDateString(),
                    'recorded_by' => auth()->id(),
                    'remarks' => 'Stock deducted once when the administrator accepted the customer order.',
                ]);
            }

            $lockedOrder->update(['status' => 'outgoing']);
        }, 3);

        return back()->with('success', 'Order accepted. Its stock has been deducted once and its status is now Out for Delivery.');
    }

    /** Cancel a pending request and release its reserved stock back to the customer catalog. */
    public function cancel(Order $order): RedirectResponse
    {
        DB::transaction(function () use ($order): void {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedOrder->status !== 'pending') {
                throw ValidationException::withMessages([
                    'order' => 'Only a pending order can be cancelled. Orders already accepted for delivery cannot be cancelled here.',
                ]);
            }

            $lockedOrder->update(['status' => 'cancelled']);
        }, 3);

        return back()->with('success', 'Order cancelled. Its reserved quantities are available for other customers again.');
    }

    /** Complete an order after delivery without deducting stock a second time. */
    public function complete(Order $order): RedirectResponse
    {
        DB::transaction(function () use ($order): void {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedOrder->status !== 'outgoing') {
                throw ValidationException::withMessages([
                    'order' => 'Only an accepted order can be marked as delivered and completed.',
                ]);
            }

            $lockedOrder->update(['status' => 'done']);
        }, 3);

        return back()->with('success', 'Order marked as delivered and completed.');
    }
}
