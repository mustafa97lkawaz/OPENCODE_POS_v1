@extends('layouts.master')
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <div>
            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">تقرير الارباح</h2>
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
                <form action="{{ route('reports.profit') }}" method="GET" class="form-horizontal">
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
                                <a href="{{ route('reports.profit') }}" class="btn btn-secondary btn-block">
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
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card bg-primary-gradient text-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <div class="tx-right">اجمالي الإيرادات</div>
                        <h2 class="tx-bold">{{ number_format($totalRevenue, 2) }}</h2>
                        <p class="mb-0">ريال</p>
                    </div>
                    <div class="col-4">
                        <i class="fa fa-money-bill tx-40 op-7"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card bg-warning-gradient text-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <div class="tx-right">اجمالي التكلفة</div>
                        <h2 class="tx-bold">{{ number_format($totalCost, 2) }}</h2>
                        <p class="mb-0">ريال</p>
                    </div>
                    <div class="col-4">
                        <i class="fa fa-minus-circle tx-40 op-7"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card bg-success-gradient text-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <div class="tx-right">صافي الربح</div>
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

<!-- Profit Chart -->
<div class="row row-sm mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">الربح اليومي</h3>
            </div>
            <div class="card-body">
                <canvas id="profitChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Profitable Products -->
<div class="row row-sm mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">اكثر المنتجات ربحية</h3>
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productProfits as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $product->Product_name }}</td>
                                    <td>{{ $product->total_qty }}</td>
                                    <td>{{ number_format($product->total_revenue, 2) }}</td>
                                    <td>{{ number_format($product->total_cost, 2) }}</td>
                                    <td class="font-weight-bold text-success">{{ number_format($product->total_profit, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد بيانات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales with Profit Details -->
<div class="row row-sm mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">المبيعات والارباح</h3>
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
                                <th>الاجمالي</th>
                                <th>الربح</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesWithProfit as $sale)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $sale->invoice_number }}</td>
                                    <td>{{ $sale->customer->Customer_name ?? 'زائر' }}</td>
                                    <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ number_format($sale->total, 2) }}</td>
                                    <td class="font-weight-bold {{ $sale->profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($sale->profit, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد بيانات</td>
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

@section('scripts')
<script src="{{ URL::asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>
<script>
    // Profit Chart
    const profitCtx = document.getElementById('profitChart').getContext('2d');
    new Chart(profitCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($dailyProfit->keys()->toArray()) !!},
            datasets: [{
                label: 'الربح اليومي',
                data: {!! json_encode($dailyProfit->values()->toArray()) !!},
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' ريال';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endsection
