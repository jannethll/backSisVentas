<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasUuid;

    protected $fillable = [
        'order_id', 'product_variant_id', 'quantity', 'unit_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2', // columna generada, solo lectura
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function batchAllocations()
    {
        return $this->hasMany(OrderItemBatch::class);
    }
}