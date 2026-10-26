<?php

namespace App\Http\Controllers;

use App\Models\EggProduct;
use App\Models\InventoryTransaction;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(InventoryService $inventory): View
    {
        return view('admin.inventory.index', [
            'products' => $inventory->allBalances(),
            'transactions' => InventoryTransaction::with('product', 'recorder')
                ->latest('transaction_date')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function history(): View
    {
        return view('admin.inventory.history', [
            'transactions' => InventoryTransaction::with('product', 'recorder')
                ->latest('transaction_date')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function store(Request $request, InventoryService $inventory): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:egg_products,id'],
            'transaction_type' => ['required', 'in:addition,deduction,adjustment'],
            'quantity' => ['required', 'integer', 'min:1'],
            'adjustment_sign' => ['nullable', 'in:-1,1'],
            'reason' => ['required', 'string', 'max:150'],
            'remarks' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $request, $inventory): void {
            $product = EggProduct::query()
                ->whereKey((int) $data['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $quantity = (int) $data['quantity'];
            $currentBalance = $inventory->balance((int) $product->id, true);
            $availableToDeduct = $inventory->availableForOrdering((int) $product->id, true);
            $signedAdjustment = (int) ($data['adjustment_sign'] ?? 1) * $quantity;

            if ($data['transaction_type'] === 'deduction' && $quantity > $availableToDeduct) {
                throw ValidationException::withMessages([
                    'quantity' => 'This deduction would use stock reserved for pending customer orders or exceed current stock. Available to deduct: ' . $availableToDeduct . '.',
                ]);
            }

            if ($data['transaction_type'] === 'adjustment' && $signedAdjustment < 0 && $quantity > $availableToDeduct) {
                throw ValidationException::withMessages([
                    'quantity' => 'This adjustment would reduce stock below the quantity reserved for pending customer orders. Available to deduct: ' . $availableToDeduct . '.',
                ]);
            }

            $storedQuantity = $data['transaction_type'] === 'adjustment'
                ? $signedAdjustment
                : $quantity;

            if ($data['transaction_type'] !== 'addition' && $data['transaction_type'] !== 'deduction' && $currentBalance + $storedQuantity < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'An inventory adjustment cannot make stock negative.',
                ]);
            }

            InventoryTransaction::query()->create([
                'product_id' => $product->id,
                'transaction_type' => $data['transaction_type'],
                'quantity' => $storedQuantity,
                'reason' => $data['reason'],
                'transaction_date' => now()->toDateString(),
                'recorded_by' => $request->user()->id,
                'remarks' => $data['remarks'] ?? null,
            ]);
        }, 3);

        return back()->with('success', 'Inventory transaction saved. Reserved customer-order quantities have been protected.');
    }
}
