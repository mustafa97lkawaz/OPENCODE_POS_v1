@extends('layouts.master')
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <div>
            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">تقرير الارباح - حسب المنتج</h2>
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
                <form action="{{ route('reports.profit.by_product') }}" method="GET" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>من تاريخ</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>إلى تاريخ</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <a href="{{ route('reports.profit.by_product') }}" class="btn btn-secondary btn-block">
                                    <i class="fa fa-times"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Card -->
<div class="row row-sm mt-3">
    <div class="col-xl-12">
        <div class="card bg-success-gradient text-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <div class="tx-right">اجمالي الارباح</div>
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
</div>

<!-- Products Table -->
<div class="row row-sm mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">الارباح حسب المنتج</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم المنتج</th>
                                <th>الكمية المباعة</th>
                                <th>الإيرادات</th>
                                <th>التكلفة</th>
                                <th>الربح</th>
                                <th>نسبة الربح</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productProfits as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $product->Product_name }}</td>
                                    <td><span class="badge badge-primary">{{ $product->total_qty }}</span></td>
                                    <td>{{ number_format($product->total_revenue, 2) }}</td>
                                    <td>{{ number_format($product->total_cost, 2) }}</td>
                                    <td class="font-weight-bold text-success">{{ number_format($product->total_profit, 2) }}</td>
                                    <td>
                                        @if($product->total_revenue > 0)
                                            {{ number_format(($product->total_profit / $product->total_revenue) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد بيانات</td>
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
