<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'base_sku' => $this->base_sku,
            'name' => $this->name,
            'description' => $this->description,
            'base_price' => $this->base_price,
            'unit_label' => $this->unit_label,
            'is_active' => (bool) $this->is_active,
            'variants' => AdminVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}