<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route already behind auth middleware
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'required|in:cash,card,split',
            'items_json'     => 'required|json',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'يرجي اختيار طريقة الدفع',
            'items_json.required'     => 'السلة فارغة',
        ];
    }
}
