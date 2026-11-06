<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasUuid;

    const UPDATED_AT = null; // esta tabla no tiene updated_at

    protected $fillable = [
        'product_id', 'variant_id', 'image_path', 'is_primary', 'sort_order',
    ];

    protected $casts = ['is_primary' => 'boolean'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}