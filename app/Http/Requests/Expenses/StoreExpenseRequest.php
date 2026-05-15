<?php

namespace App\Http\Requests\Expenses;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route already behind auth middleware
    }

    public function rules(): array
    {
        return [
            'Expense_name'   => 'required',
            'amount'         => 'required|numeric|min:0',
            'category_id'    => 'required',
            'expense_date'   => 'required|date',
            'payment_method' => 'required|in:cash,card,bank',
            'status'         => 'required|in:paid,pending',
            'attachment'     => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:2048',
            'recurring'      => 'nullable|boolean',
            'recurring_type' => 'required_if:recurring,1|in:daily,weekly,monthly',
        ];
    }

    public function messages(): array
    {
        return [
            'Expense_name.required'      => 'يرجي ادخال اسم المصروف',
            'amount.required'            => 'يرجي ادخال المبلغ',
            'amount.numeric'             => 'يرجي ادخال رقم صحيح',
            'category_id.required'       => 'يرجي اختيار التصنيف',
            'expense_date.required'      => 'يرجي اختيار التاريخ',
            'payment_method.required'    => 'يرجي اختيار طريقة الدفع',
            'status.required'            => 'يرجي اختيار الحالة',
            'recurring_type.required_if' => 'يرجي اختيار نوع التكرار',
            'attachment.max'             => 'حجم الملف يجب ان يكون اقل من 2 ميجابايت',
        ];
    }
}
