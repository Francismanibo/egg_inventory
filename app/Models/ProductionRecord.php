<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionRecord extends Model
{
    protected $fillable = [
        'product_id',
        'production_date',
        'quantity_produced',
        'saleable_quantity',
        'damaged_quantity',
        'rejected_quantity',
        'recorded_by',
        'notes',
    ];

    protected $casts = [
        'production_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(EggProduct::class, 'product_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'production_id');
    }
}
