@extends('layouts.master')
@section('css')
<!--  Owl-carousel css-->
<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <div>
            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">مرحباً بك!</h2>
            <p class="mg-b-0">لوحة تحكم نظام نقاط البيع</p>
        </div>
    </div>
</div>
<!-- /breadcrumb -->
@endsection
@section('content')

<!-- row opened -->
<div class="row row-sm">
    
    <!-- 1. مبيعات اليوم -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
        <div class="card shadow-sm border-0 mb-4 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="tx-14 text-muted mb-1 font-weight-bold">مبيعات اليوم</p>
                        <h4 class="tx-22 font-weight-bold text-dark mb-0">
                            {{ number_format($today_sales ?? 0, 2) }}
                        </h4>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background-color: rgba(34, 192, 60, 0.1);">
                        <i class="fas fa-shopping-cart tx-22" style="color: #22c03c;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="tx-13 text-muted">عدد المعاملات: <span class="font-weight-bold text-dark">{{ $today_sales_count ?? 0 }}</span></span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. المصروفات الشهرية -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
        <div class="card shadow-sm border-0 mb-4 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="tx-14 text-muted mb-1 font-weight-bold">المصروفات الشهرية</p>
                        <h4 class="tx-22 font-weight-bold text-dark mb-0">
                            {{ number_format($monthly_expenses ?? 0, 2) }}
                        </h4>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background-color: rgba(241, 56, 139, 0.1);">
                        <i class="fas fa-money-bill-wave tx-22" style="color: #f1388b;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. صافي الربح -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
        <div class="card shadow-sm border-0 mb-4 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="tx-14 text-muted mb-1 font-weight-bold">صافي الربح</p>
                        <h4 class="tx-22 font-weight-bold text-dark mb-0">
                            {{ number_format($net_profit ?? 0, 2) }}
                        </h4>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background-color: rgba(1, 98, 232, 0.1);">
                        <i class="fas fa-chart-line tx-22" style="color: #0162e8;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. تنبيه المخزون -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
        <div class="card shadow-sm border-0 mb-4 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="tx-14 text-muted mb-1 font-weight-bold">تنبيه المخزون المنخفض</p>
                        <h4 class="tx-22 font-weight-bold text-dark mb-0">
                            {{ $low_stock_products ?? 0 }}
                        </h4>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background-color: rgba(255, 171, 0, 0.1);">
                        <i class="fas fa-exclamation-triangle tx-22" style="color: #ffab00;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="tx-13 text-muted">منتجات منخفضة المخزون</span>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- row closed -->

<!-- بداية صف الإحصائيات الإضافية -->
    <div class="row row-sm mt-4">
        
        <!-- 1. بطاقة المستخدمين -->
        <div class="col-xl-4 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden shadow-sm bg-white border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 tx-14 text-muted">إجمالي المستخدمين</h6>
                            <h4 class="tx-24 font-weight-bold mb-0 text-dark">
                                {{ App\Models\User::count() }}
                            </h4>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 60px; height: 60px; background-color: rgba(1, 98, 232, 0.1);">
                            <i class="fas fa-users tx-24" style="color: #0162e8;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. بطاقة المنتجات -->
        <div class="col-xl-4 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden shadow-sm bg-white border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 tx-14 text-muted">إجمالي المنتجات</h6>
                            <h4 class="tx-24 font-weight-bold mb-0 text-dark">
                                {{ App\Models\Products::count() }}
                            </h4>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 60px; height: 60px; background-color: rgba(241, 56, 139, 0.1);">
                            <i class="fas fa-box-open tx-24" style="color: #f1388b;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. بطاقة العملاء -->
        <div class="col-xl-4 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden shadow-sm bg-white border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 tx-14 text-muted">إجمالي العملاء</h6>
                            <h4 class="tx-24 font-weight-bold mb-0 text-dark">
                                {{ App\Models\Customer::count() }}
                            </h4>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 60px; height: 60px; background-color: rgba(34, 192, 60, 0.1);">
                            <i class="fas fa-user-friends tx-24" style="color: #22c03c;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Charts Row -->
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">المبيعات اليومية</h5>
                </div>
                <div class="card-body">
                    @if(isset($chartjs))
                        {!! $chartjs->render() !!}
                    @else
                        <p class="text-muted">لا توجد بيانات</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">نظرة عامة</h5>
                </div>
                <div class="card-body">
                    @if(isset($chartjs_2))
                        {!! $chartjs_2->render() !!}
                    @else
                        <p class="text-muted">لا توجد بيانات</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
            
@endsection
@section('js')
<!--Internal  Chart.bundle js -->
<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
<!-- Moment js -->
<script src="{{URL::asset('assets/plugins/raphael/raphael.min.js')}}"></script>
<!--Internal  index js -->
<script src="{{URL::asset('assets/js/index.js')}}"></script>
@endsection
