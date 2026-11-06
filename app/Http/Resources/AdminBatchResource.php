<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminBatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'batch_code' => $this->batch_code,
            'variant' => [
                'id' => $this->variant->id,
                'variant_name' => $this->variant->variant_name,
                'product_name' => $this->variant->product->name,
                'sale_unit' => $this->variant->sale_unit,
            ],
            // Fechas sin hora: evita que la zona horaria corra el día en el navegador
            'production_date' => $this->production_date?->format('Y-m-d'),
            'expiration_date' => $this->expiration_date?->format('Y-m-d'),
            'quantity_produced' => $this->quantity_produced,
            'quantity_available' => $this->quantity_available,
            'unit_cost' => $this->unit_cost,
            'status' => $this->status,
            'notes' => $this->notes,
        ];
    }
}