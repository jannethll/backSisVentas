<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_code' => $this->order->order_code,
            'method' => $this->method,
            'amount' => $this->amount,
            'reference_code' => $this->reference_code,
            'proof_image_url' => $this->proof_image_path
                ? Storage::disk('public')->url($this->proof_image_path)
                : null,
            'status' => $this->status,
            'verified_by' => $this->whenLoaded('verifiedBy', fn () => $this->verifiedBy?->name),
            'verified_at' => $this->verified_at,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}