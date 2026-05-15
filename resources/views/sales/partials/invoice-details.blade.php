@php
    $methodLabel = $sale->payment_method === 'cash' ? 'نقدي'
        : ($sale->payment_method === 'card' ? 'بطاقة' : 'Split');
@endphp

<div class="invoice-details">
    <h5>رقم الفاتورة: {{ $sale->invoice_number }}</h5>
    <p>العميل: {{ $sale->customer->Customer_name ?? 'زائر' }}</p>
    <p>التاريخ: {{ $sale->created_at }}</p>
    <p>طريقة الدفع: {{ $methodLabel }}</p>
</div>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>المنتج</th>
            <th>الكمية</th>
            <th>السعر</th>
            <th>الاجمالي</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sale->saleItems as $item)
            <tr>
                <td>{{ $item->product->Product_name ?? 'منتج محذوف' }}</td>
                <td>{{ $item->qty }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">المجموع</th>
            <th>{{ number_format($sale->subtotal, 2) }}</th>
        </tr>
        <tr>
            <th colspan="3">الضريبة</th>
            <th>{{ number_format($sale->tax_amount, 2) }}</th>
        </tr>
        <tr>
            <th colspan="3">الخصم</th>
            <th>{{ number_format($sale->discount, 2) }}</th>
        </tr>
        <tr>
            <th colspan="3">الاجمالي</th>
            <th>{{ number_format($sale->total, 2) }}</th>
        </tr>
    </tfoot>
</table>
