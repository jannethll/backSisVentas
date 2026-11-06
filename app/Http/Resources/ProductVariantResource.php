<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'variant_name' => $this->variant_name,
            'flavor_or_type' => $this->flavor_or_type,
            'sale_unit' => $this->sale_unit,
            'price' => $this->effective_price,
            'is_active' => $this->is_active,
            'stock_available' => $this->batches()
                ->where('status', 'disponible')
                ->sum('quantity_available'),
            'images' => $this->images->pluck('image_path'),
        ];
    }
}