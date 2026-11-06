<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el middleware 'admin' de la ruta ya restringe el acceso
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'category_id' => ['required', 'uuid', 'exists:categories,id'],
            'base_sku' => ['nullable', 'string', 'max:50', Rule::unique('products', 'base_sku')->ignore($productId)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'unit_label' => ['required', 'string', 'max:30'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}