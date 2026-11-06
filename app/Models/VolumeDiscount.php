<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class VolumeDiscount extends Model
{
    use HasUuid;

    protected $fillable = [
        'product_variant_id', 'min_quantity', 'discounted_price', 'is_active',
    ];

    protected $casts = [
        'min_quantity' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}