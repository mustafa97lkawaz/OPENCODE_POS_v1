<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class SalesReportExport implements FromCollection, WithHeadings
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function collection()
    {
        $data = [];
        
        foreach ($this->sales as $sale) {
            $data[] = [
                'رقم الفاتورة' => $sale->invoice_number,
                'العميل' => $sale->customer->Customer_name ?? 'زائر',
                'التاريخ' => $sale->created_at->format('Y-m-d H:i'),
                'المجموع الفرعي' => number_format($sale->subtotal, 2),
                'الضريبة' => number_format($sale->tax_amount, 2),
                'الخصم' => number_format($sale->discount, 2),
                'الاجمالي' => number_format($sale->total, 2),
                'طريقة الدفع' => $this->getPaymentMethod($sale->payment_method),
                'المنشئ' => $sale->Created_by,
            ];
        }

        return new Collection($data);
    }

    public function headings(): array
    {
        return [
            'رقم الفاتورة',
            'العميل',
            'التاريخ',
            'المجموع الفرعي',
            'الضريبة',
            'الخصم',
            'الاجمالي',
            'طريقة الدفع',
            'المنشئ',
        ];
    }

    private function getPaymentMethod($method)
    {
        switch ($method) {
            case 'cash':
                return 'نقدي';
            case 'card':
                return 'بطاقة';
            case 'split':
                return 'تقسيط';
            default:
                return $method;
        }
    }
}
