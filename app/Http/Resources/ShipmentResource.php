<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isAdmin = Auth::guard('api')->user()?->role === 'admin';

        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order_code' => $this->whenLoaded('order', fn () => $this->order->order_code),
            'customer_name' => $this->whenLoaded('order', fn () => $this->order->customer?->full_name),
            'carrier_agency' => $this->carrier_agency,
            'destination_city' => $this->destination_city,
            'destination_agency' => $this->destination_agency,
            'tracking_number' => $this->tracking_number,
            'pickup_code' => $this->pickup_code,
            'recipient_name' => $this->recipient_name,
            'recipient_phone' => $this->recipient_phone,
            'shipping_cost' => $this->shipping_cost,
            'status' => $this->status,
            'next_statuses' => $this->when($isAdmin, fn () => $this->nextStatuses()),
            'dispatched_at' => $this->dispatched_at,
            'estimated_delivery_date' => $this->estimated_delivery_date,
            'delivered_at' => $this->delivered_at,
            'notes' => $this->when($isAdmin, $this->notes),
        ];
    }
}