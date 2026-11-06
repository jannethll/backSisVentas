<?php

namespace App\Http\Requests\Shipment;

use Illuminate\Foundation\Http\FormRequest;

class CreateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el control de rol lo hace la ruta (solo admin)
    }

    public function rules(): array
    {
        return [
            'carrier_agency' => ['required', 'string', 'max:100'],
            'destination_agency' => ['nullable', 'string', 'max:150'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'recipient_phone' => ['nullable', 'string', 'max:20'],
            'estimated_delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}