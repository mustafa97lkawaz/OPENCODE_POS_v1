<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route already behind auth middleware
    }

    public function rules(): array
    {
        return [
            'Product_name'  => 'required|string|max:191',
            'category_id'   => 'required|exists:categories,id',
            'sku'           => 'nullable|string|unique:products,sku|max:50',
            'barcode'       => 'nullable|string|unique:products,barcode|max:50',
            'description'   => 'nullable|string',
            'photo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cost_price'    => 'nullable|numeric|min:0',
            'sell_price'    => 'required|numeric|min:0',
            'tax_rate'      => 'nullable|numeric|min:0|max:100',
            'reorder_point' => 'nullable|integer|min:0',
            'wac'           => 'nullable|numeric|min:0',
            'stock_qty'     => 'nullable|integer|min:0',
            'expire_date'   => 'nullable|date',
            'alert_qty'     => 'nullable|integer|min:0',
            'is_variant'    => 'nullable|boolean',
            'variant_name'  => 'nullable|string|max:100',
            'unit'          => 'nullable|string|max:50',
            'variations'    => 'nullable|json',
            'max_stock'     => 'nullable|integer|min:0',
            'is_featured'   => 'nullable|boolean',
            'is_active'     => 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'Product_name'  => 'اسم المنتج',
            'category_id'   => 'القسم',
            'sku'           => 'رمز المنتج',
            'barcode'       => 'الباركود',
            'description'   => 'الملاحظات',
            'photo'         => 'صورة المنتج',
            'cost_price'    => 'سعر التكلفة',
            'sell_price'    => 'سعر البيع',
            'tax_rate'      => 'نسبة الضريبة',
            'reorder_point' => 'نقطة اعادة الطلب',
            'wac'           => 'متوسط السعر المرجح',
            'stock_qty'     => 'الكمية',
            'expire_date'   => 'تاريخ الانتهاء',
            'alert_qty'     => 'كمية التنبيه',
            'is_variant'    => 'له متغيرات',
            'variant_name'  => 'اسم المتغير',
            'unit'          => 'الوحدة',
            'variations'    => 'المتغيرات',
            'max_stock'     => 'الكمية القصوى',
            'is_featured'   => 'منتج مميز',
            'is_active'     => 'الحالة',
        ];
    }
}
