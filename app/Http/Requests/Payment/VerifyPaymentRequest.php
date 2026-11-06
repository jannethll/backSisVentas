<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el middleware 'admin' en la ruta ya restringe el acceso
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:verificado,rechazado'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}