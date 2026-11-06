<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'variant_name' => $this->variant->variant_name,
            'quantity' => $this->quantity,
            'sale_unit' => $this->variant->sale_unit,
            'unit_price' => $this->unit_price,
            'subtotal' => $this->subtotal,
            'batches_used' => $this->whenLoaded('batchAllocations', fn () =>
                $this->batchAllocations->map(fn ($alloc) => [
                    'batch_code' => $alloc->batch->batch_code,
                    'quantity' => $alloc->quantity,
                ])
            ),
        ];
    }
}