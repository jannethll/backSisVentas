<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_variant_id' => ['required', 'uuid', 'exists:product_variants,id'],
            'production_date' => ['required', 'date', 'before_or_equal:today'],
            'expiration_date' => ['nullable', 'date', 'after:production_date'],
            'quantity_produced' => ['required', 'numeric', 'gt:0'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}