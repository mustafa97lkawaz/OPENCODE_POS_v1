@extends('layouts.master')
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <div>
            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">تقرير المخزون</h2>
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
                <form action="{{ route('reports.inventory') }}" method="GET" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>التصنيف</label>
                                <select name="category_id" class="form-control select2">
                                    <option value="">الكل</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->Category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>حالة المخزون</label>
                                <select name="stock_status" class="form-control">
                                    <option value="">الكل</option>
                                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>متوفر</option>
                                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>منخفض</option>
                                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>نفد المخزون</option>
                                </select>
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
                                <a href="{{ route('reports.inventory') }}" class="btn btn-secondary btn-block">
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

<!-- Summary Cards -->
<div class="row row-sm mt-3">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card bg-primary-gradient text-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <div class="tx-right">اجمالي المنتجات</div>
                        <h2 class="tx-bold">{{ $totalProducts }}</h2>
                        <p class="mb-0">منتج</p>
                    </div>
                    <div class="col-4">
                        <i class="fa fa-box tx-40 op-7"></i>
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
                        <div class="tx-right">متوفر</div>
                        <h2 class="tx-bold">{{ $inStock }}</h2>
                        <p class="mb-0">منتج</p>
                    </div>
                    <div class="col-4">
                        <i class="fa fa-check-circle tx-40 op-7"></i>
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
                        <div class="tx-right">منخفض المخزون</div>
                        <h2 class="tx-bold">{{ $lowStock }}</h2>
                        <p class="mb-0">منتج</p>
                    </div>
                    <div class="col-4">
                        <i class="fa fa-exclamation-triangle tx-40 op-7"></i>
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
                        <div class="tx-right">نفد المخزون</div>
                        <h2 class="tx-bold">{{ $outOfStock }}</h2>
                        <p class="mb-0">منتج</p>
                    </div>
                    <div class="col-4">
                        <i class="fa fa-times-circle tx-40 op-7"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-sm mt-3">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <div class="tx-right">قيمة المخزون</div>
                        <h2 class="tx-bold text-primary">{{ number_format($totalStockValue, 2) }}</h2>
                        <p class="mb-0">ريال</p>
                    </div>
                    <div class="col-4">
                        <i class="fa fa-dollar-sign tx-40 text-muted"></i>
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
                <h3 class="card-title">قائمة المنتجات</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>كود المنتج</th>
                                <th>اسم المنتج</th>
                                <th>التصنيف</th>
                                <th>سعر التكلفة</th>
                                <th>سعر البيع</th>
                                <th>المخزون</th>
                                <th>نقطة التنبيه</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr class="{{ $product->stock_qty <= 0 ? 'bg-danger-light' : ($product->stock_qty <= $product->alert_qty ? 'bg-warning-light' : '') }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $product->sku ?? '-' }}</td>
                                    <td>{{ $product->Product_name }}</td>
                                    <td>{{ $product->category->Category_name ?? '-' }}</td>
                                    <td>{{ number_format($product->cost_price, 2) }}</td>
                                    <td>{{ number_format($product->sell_price, 2) }}</td>
                                    <td class="font-weight-bold">{{ $product->stock_qty }}</td>
                                    <td>{{ $product->alert_qty }}</td>
                                    <td>
                                        @if($product->stock_qty <= 0)
                                            <span class="badge badge-danger">نفد المخزون</span>
                                        @elseif($product->stock_qty <= $product->alert_qty)
                                            <span class="badge badge-warning">منخفض</span>
                                        @else
                                            <span class="badge badge-success">متوفر</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">لا توجد بيانات</td>
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
