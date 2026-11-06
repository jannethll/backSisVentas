<?php

namespace App\Http\Requests\Shipment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:preparando,enviado_a_agencia,en_transito,entregado,devuelto,incidencia'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'pickup_code' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}