<?php

namespace App\Services;

use App\Models\EggProduct;
use App\Models\InventoryTransaction;
use App\Models\OrderItem;
use Illuminate\Support\Collection;

class InventoryService
{
    public function balance(int $productId, bool $forUpdate = false): int
    {
        $query = InventoryTransaction::query()
            ->where('product_id', $productId);

        if ($forUpdate) {
            // A locking read avoids stale stock snapshots after waiting for a product-row lock.
            $query->lockForUpdate();
        }

        return (int) $query
            ->get(['transaction_type', 'quantity'])
            ->sum(fn ($transaction) => match ($transaction->transaction_type) {
                'addition' => (int) $transaction->quantity,
                'deduction' => -(int) $transaction->quantity,
                'adjustment' => (int) $transaction->quantity,
                default => 0,
            });
    }

    /**
     * Stock that can be requested by customers right now.
     * Pending order quantities reserve stock until they are accepted or otherwise processed.
     */
    public function availableForOrdering(int $productId, bool $forUpdate = false): int
    {
        $onHand = $this->balance($productId, $forUpdate);

        $reservedByPendingOrders = (int) OrderItem::query()
            ->where('product_id', $productId)
            ->whereHas('order', fn ($query) => $query->where('status', 'pending'))
            ->sum('quantity');

        return max(0, $onHand - $reservedByPendingOrders);
    }

    public function allBalances(): Collection
    {
        return EggProduct::query()
            ->where('status', 'active')
            ->orderBy('product_name')
            ->get()
            ->map(function (EggProduct $product): EggProduct {
                $product->current_stock = $this->balance((int) $product->id);
                $product->available_to_order = $this->availableForOrdering((int) $product->id);
                $product->low_stock = $product->current_stock <= $product->reorder_level;

                return $product;
            });
    }
}
