@extends('layouts.master')
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <div>
            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">تقرير المبيعات</h2>
        </div>
    </div>
</div>
@endsection
@section('content')
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">فلاتر التقرير</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.sales') }}" method="GET" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>من تاريخ</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>إلى تاريخ</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>طريقة الدفع</label>
                                <select name="payment_method" class="form-control">
                                    <option value="all">الكل</option>
                                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                                    <option value="split" {{ request('payment_method') == 'split' ? 'selected' : '' }}>تقسيط</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>العميل</label>
                                <select name="customer_id" class="form-control select2">
                                    <option value="">الكل</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->Customer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row row-sm mt-3">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card bg-primary-gradient text-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <div class="tx-right">اجمالي المبيعات</div>
                        <h2 class="tx-bold">{{ number_format($totalSales, 2) }}</h2>
                        <p class="mb-0">ريال</p>
                    </div>
                    <div class="col-4">
                        <i class="fa fa-shopping-cart tx-40 op-7"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card bg-success-gradient text-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <div class="tx-right">اجمالي الربح</div>
                        <h2 class="tx-bold">{{ number_format($totalProfit, 2) }}</h2>
                        <p class="mb-0">ريال</p>
                    </div>
                    <div class="col-4">
                        <i class="fa fa-chart-line tx-40 op-7"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card bg-warning-gradient text-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <div class="tx-right">اجمالي الضريبة</div>
                        <h2 class="tx-bold">{{ number_format($totalTax, 2) }}</h2>
                        <p class="mb-0">ريال</p>
                    </div>
                    <div class="col-4">
                        <i class="fa fa-percent tx-40 op-7"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card bg-danger-gradient text-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <div class="tx-right">اجمالي الخصم</div>
                        <h2 class="tx-bold">{{ number_format($totalDiscount, 2) }}</h2>
                        <p class="mb-0">ريال</p>
                    </div>
                    <div class="col-4">
                        <i class="fa fa-tags tx-40 op-7"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Table -->
<div class="row row-sm mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">قائمة المبيعات</h3>
                <a href="{{ route('reports.sales.export', request()->query()) }}" class="btn btn-success">
                    <i class="fa fa-file-excel"></i> تصدير اكسل
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم الفاتورة</th>
                                <th>العميل</th>
                                <th>التاريخ</th>
                                <th>المجموع الفرعي</th>
                                <th>الضريبة</th>
                                <th>الخصم</th>
                                <th>الاجمالي</th>
                                <th>طريقة الدفع</th>
                                <th>المنشئ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $sale->invoice_number }}</td>
                                    <td>{{ $sale->customer->Customer_name ?? 'زائر' }}</td>
                                    <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ number_format($sale->subtotal, 2) }}</td>
                                    <td>{{ number_format($sale->tax_amount, 2) }}</td>
                                    <td>{{ number_format($sale->discount, 2) }}</td>
                                    <td class="font-weight-bold">{{ number_format($sale->total, 2) }}</td>
                                    <td>
                                        @if($sale->payment_method == 'cash')
                                            <span class="badge badge-success">نقدي</span>
                                        @elseif($sale->payment_method == 'card')
                                            <span class="badge badge-info">بطاقة</span>
                                        @else
                                            <span class="badge badge-warning">تقسيط</span>
                                        @endif
                                    </td>
                                    <td>{{ $sale->Created_by }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">لا توجد بيانات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
