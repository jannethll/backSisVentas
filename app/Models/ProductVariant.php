<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasUuid;

    protected $fillable = [
        'product_id', 'sku', 'variant_name', 'flavor_or_type',
        'sale_unit', 'price_override', 'is_active',
    ];

    protected $casts = [
        'price_override' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function volumeDiscounts()
    {
        return $this->hasMany(VolumeDiscount::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'variant_id');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Precio efectivo: usa price_override si existe, si no el base_price del producto
    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->price_override ?? $this->product->base_price);
    }

    // ¿Se vende por unidad entera (molde) o por peso (kg)?
    public function isSoldByUnit(): bool
    {
        return $this->sale_unit === 'unidad';
    }
}