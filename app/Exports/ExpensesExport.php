<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpensesExport implements FromCollection, WithHeadings
{
    protected $expenses;

    public function __construct($expenses)
    {
        $this->expenses = $expenses;
    }

    public function collection()
    {
        return $this->expenses->map(function ($expense) {
            return [
                '#' => $expense->id,
                'اسم المصروف' => $expense->Expense_name,
                'رقم المرجع' => $expense->reference_number,
                'المبلغ' => $expense->amount,
                'التصنيف' => $expense->category->Category_name ?? '-',
                'تاريخ المصروف' => $expense->expense_date,
                'طريقة الدفع' => $expense->payment_method_label,
                'الحالة' => $expense->status_label,
                'متكرر' => $expense->recurring ? 'نعم' : 'لا',
                'نوع التكرار' => $expense->recurring_type_label ?? '-',
                'الوصف' => $expense->description,
                'أنشأ بواسطة' => $expense->Created_by,
                'تاريخ الإنشاء' => $expense->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            '#',
            'اسم المصروف',
            'رقم المرجع',
            'المبلغ',
            'التصنيف',
            'تاريخ المصروف',
            'طريقة الدفع',
            'الحالة',
            'متكرر',
            'نوع التكرار',
            'الوصف',
            'أنشأ بواسطة',
            'تاريخ الإنشاء',
        ];
    }
}
