<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'variant_name' => $this->variant_name,
            'flavor_or_type' => $this->flavor_or_type,
            'sale_unit' => $this->sale_unit,
            'price_override' => $this->price_override,
            'effective_price' => $this->effective_price,
            'is_active' => (bool) $this->is_active,
            'stock' => $this->stock ?? '0.00', // suma de lotes disponibles
        ];
    }
}