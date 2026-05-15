<?php

namespace App\Http\Requests\Customers;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route already behind auth middleware
    }

    public function rules(): array
    {
        return [
            'Customer_name' => 'required|string|max:191',
        ];
    }

    public function messages(): array
    {
        return [
            'Customer_name.required' => 'يرجي ادخال اسم العميل',
        ];
    }
}
