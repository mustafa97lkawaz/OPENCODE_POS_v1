<?php

namespace App\Http\Requests\StockAdjustments;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route already behind auth middleware
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'qty_change' => 'required|integer',
            'type'       => 'required|in:damaged,expired,added,removed',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'يرجي اختيار المنتج',
            'qty_change.required' => 'يرجي ادخال الكمية',
            'type.required'       => 'يرجي اختيار نوع التعديل',
        ];
    }
}
