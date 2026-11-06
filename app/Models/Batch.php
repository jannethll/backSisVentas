<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasUuid;

    protected $fillable = [
        'product_variant_id', 'batch_code', 'production_date', 'expiration_date',
        'quantity_produced', 'quantity_available', 'unit_cost', 'status', 'notes',
    ];

    protected $casts = [
        'production_date' => 'date',
        'expiration_date' => 'date',
        'quantity_produced' => 'decimal:2',
        'quantity_available' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

       // Antes: public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function batchAllocations()
    {
        return $this->hasMany(OrderItemBatch::class);
    }

    public function hasStockFor(float $quantity): bool
    {
        return $this->quantity_available >= $quantity;
    }
}