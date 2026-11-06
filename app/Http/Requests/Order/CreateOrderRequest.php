<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el control de "quién puede pedir" lo hace el middleware de la ruta
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'uuid', 'exists:customers,id'],
            'delivery_type' => ['required', 'in:envio_agencia,recojo_local'],
            'notes' => ['nullable', 'string', 'max:255'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => ['required', 'uuid', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}