<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'method' => ['required', 'in:yape,plin,efectivo,transferencia'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_code' => ['nullable', 'string', 'max:100'],
            'proof_image' => ['nullable', 'image', 'max:4096'], // máx 4MB
        ];
    }
}