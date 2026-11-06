<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_code' => $this->order_code,
            'order_date' => $this->order_date,
            'status' => $this->status,
            'delivery_type' => $this->delivery_type,
            'preferred_carrier' => $this->preferred_carrier,
            'customer' => [
                'id' => $this->customer->id,
                'full_name' => $this->customer->full_name,
                'phone' => $this->customer->phone,
                'destination_city' => $this->customer->destination_city,
            ],
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'shipment' => $this->whenLoaded('shipment', fn () => new ShipmentResource($this->shipment)),
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'shipping_cost' => $this->shipping_cost,
            'total' => $this->total,
            'notes' => $this->notes,
        ];
    }
}