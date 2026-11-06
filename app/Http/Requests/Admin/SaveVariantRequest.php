<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $variantId = $this->route('variant')?->id;

        return [
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('product_variants', 'sku')->ignore($variantId)],
            'variant_name' => ['required', 'string', 'max:100'],
            'flavor_or_type' => ['nullable', 'string', 'max:50'],
            'sale_unit' => ['required', 'in:unidad,kg'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}