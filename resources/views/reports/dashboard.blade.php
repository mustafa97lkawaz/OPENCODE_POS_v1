@extends('layouts.master')
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <div>
            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">لوحة التحكم</h2>
        </div>
    </div>
</div>
@endsection
@section('content')
<!-- Stats Cards -->
<div class="row row-sm">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card bg-primary-gradient text-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <div class="tx-right">مبيعات اليوم</div>
                        <h2 class="tx-bold">{{ number_format($todaySales, 2) }}</h2>
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
                        <div class="tx-right">طلبات اليوم</div>
                        <h2 class="tx-bold">{{ $todayOrders }}</h2>
                        <p class="mb-0">طلب</p>
                    </div>
                    <div class="col-4">
                        <i class="fa fa-receipt tx-40 op-7"></i>
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
                        <div class="tx-right">تنبيهات المخزون</div>
                        <h2 class="tx-bold">{{ $lowStockCount }}</h2>
                        <p class="mb-0">منتج منخفض</p>
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
                        <div class="tx-right">منتجات نفد مخزونها</div>
                        <h2 class="tx-bold">{{ $outOfStockCount }}</h2>
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

<!-- Charts Row -->
<div class="row row-sm mt-3">
    <!-- Sales Chart (Last 7 Days) -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">مبيعات آخر 7 ايام</h3>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="120"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Category Distribution -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">توزيع التصنيفات</h3>
            </div>
            <div class="card-body">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Selling Products -->
<div class="row row-sm mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">الاكثر مبيعاً (آخر 30 يوم)</h3>
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $product->Product_name }}</td>
                                    <td><span class="badge badge-primary">{{ $product->total_qty }}</span></td>
                                    <td>{{ number_format($product->total_revenue, 2) }} ريال</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">لا توجد بيانات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row row-sm mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">التقارير</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <a href="{{ route('reports.sales') }}" class="btn btn-outline-primary btn-block">
                            <i class="fa fa-chart-bar"></i> تقرير المبيعات
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('reports.inventory') }}" class="btn btn-outline-success btn-block">
                            <i class="fa fa-box"></i> تقرير المخزون
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('reports.profit') }}" class="btn btn-outline-warning btn-block">
                            <i class="fa fa-chart-line"></i> تقرير الارباح
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ URL::asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>
<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($salesData['labels']) !!},
            datasets: [{
                label: 'المبيعات',
                data: {!! json_encode($salesData['data']) !!},
                backgroundColor: 'rgba(0, 97, 242, 0.1)',
                borderColor: 'rgba(0, 97, 242, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
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
            }
        }
    });

    // Category Distribution Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryDistribution->pluck('Category_name')->toArray()) !!},
            datasets: [{
                data: {!! json_encode($categoryDistribution->pluck('total_stock')->toArray()) !!},
                backgroundColor: [
                    'rgba(0, 97, 242, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(253, 126, 20, 0.8)',
                    'rgba(253, 29, 29, 0.8)',
                    'rgba(111, 66, 193, 0.8)',
                    'rgba(23, 162, 184, 0.8)',
                    'rgba(255, 193, 7, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection
