@extends('layouts.master')
@section('css')
<!--  Owl-carousel css-->
<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
<style>
    /* Dashboard — uniform stat cards */
    .dash-card {
        height: 100%;
        margin-bottom: 0;
        border: 0;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 1px 6px rgba(0, 0, 0, .06);
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .dash-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, .10);
    }
    .dash-card .card-body {
        padding: 1.5rem;
        min-height: 132px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .dash-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .dash-label {
        font-size: 14px;
        color: #8f9bb3;
        font-weight: 600;
        margin-bottom: 6px;
    }
    .dash-value {
        font-size: 24px;
        font-weight: 700;
        color: #1c273c;
        margin: 0;
        line-height: 1.1;
    }
    .dash-icon {
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .dash-icon i { font-size: 22px; }
    .dash-foot {
        margin-top: 12px;
        font-size: 13px;
        color: #8f9bb3;
        min-height: 18px;
    }
    .dash-foot b { color: #1c273c; }
    .dash-col { margin-bottom: 1.5rem; }
</style>
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

<!-- KPI cards — 4 across, identical size -->
<div class="row row-sm">

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 dash-col">
        <div class="dash-card card">
            <div class="card-body">
                <div class="dash-row">
                    <div>
                        <p class="dash-label">مبيعات اليوم</p>
                        <h4 class="dash-value">{{ number_format($today_sales ?? 0, 2) }}</h4>
                    </div>
                    <div class="dash-icon" style="background-color: rgba(34, 192, 60, .1);">
                        <i class="fas fa-shopping-cart" style="color: #22c03c;"></i>
                    </div>
                </div>
                <div class="dash-foot">عدد المعاملات: <b>{{ $today_sales_count ?? 0 }}</b></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 dash-col">
        <div class="dash-card card">
            <div class="card-body">
                <div class="dash-row">
                    <div>
                        <p class="dash-label">المصروفات الشهرية</p>
                        <h4 class="dash-value">{{ number_format($monthly_expenses ?? 0, 2) }}</h4>
                    </div>
                    <div class="dash-icon" style="background-color: rgba(241, 56, 139, .1);">
                        <i class="fas fa-money-bill-wave" style="color: #f1388b;"></i>
                    </div>
                </div>
                <div class="dash-foot">إجمالي مصروفات الشهر</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 dash-col">
        <div class="dash-card card">
            <div class="card-body">
                <div class="dash-row">
                    <div>
                        <p class="dash-label">صافي الربح</p>
                        <h4 class="dash-value">{{ number_format($net_profit ?? 0, 2) }}</h4>
                    </div>
                    <div class="dash-icon" style="background-color: rgba(1, 98, 232, .1);">
                        <i class="fas fa-chart-line" style="color: #0162e8;"></i>
                    </div>
                </div>
                <div class="dash-foot">المبيعات ناقص المصروفات</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 dash-col">
        <div class="dash-card card">
            <div class="card-body">
                <div class="dash-row">
                    <div>
                        <p class="dash-label">تنبيه المخزون المنخفض</p>
                        <h4 class="dash-value">{{ $low_stock_products ?? 0 }}</h4>
                    </div>
                    <div class="dash-icon" style="background-color: rgba(255, 171, 0, .1);">
                        <i class="fas fa-exclamation-triangle" style="color: #ffab00;"></i>
                    </div>
                </div>
                <div class="dash-foot">منتجات تحتاج إعادة طلب</div>
            </div>
        </div>
    </div>

</div>

<!-- Entity totals — 4 across, same card size as above -->
<div class="row row-sm">

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 dash-col">
        <div class="dash-card card">
            <div class="card-body">
                <div class="dash-row">
                    <div>
                        <p class="dash-label">إجمالي المستخدمين</p>
                        <h4 class="dash-value">{{ App\Models\User::count() }}</h4>
                    </div>
                    <div class="dash-icon" style="background-color: rgba(1, 98, 232, .1);">
                        <i class="fas fa-users" style="color: #0162e8;"></i>
                    </div>
                </div>
                <div class="dash-foot">مستخدمو النظام</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 dash-col">
        <div class="dash-card card">
            <div class="card-body">
                <div class="dash-row">
                    <div>
                        <p class="dash-label">إجمالي المنتجات</p>
                        <h4 class="dash-value">{{ App\Models\Products::count() }}</h4>
                    </div>
                    <div class="dash-icon" style="background-color: rgba(241, 56, 139, .1);">
                        <i class="fas fa-box-open" style="color: #f1388b;"></i>
                    </div>
                </div>
                <div class="dash-foot">أصناف مسجّلة</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 dash-col">
        <div class="dash-card card">
            <div class="card-body">
                <div class="dash-row">
                    <div>
                        <p class="dash-label">إجمالي العملاء</p>
                        <h4 class="dash-value">{{ App\Models\Customer::count() }}</h4>
                    </div>
                    <div class="dash-icon" style="background-color: rgba(34, 192, 60, .1);">
                        <i class="fas fa-user-friends" style="color: #22c03c;"></i>
                    </div>
                </div>
                <div class="dash-foot">عملاء مسجّلون</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 dash-col">
        <div class="dash-card card">
            <div class="card-body">
                <div class="dash-row">
                    <div>
                        <p class="dash-label">إجمالي الأقسام</p>
                        <h4 class="dash-value">{{ App\Models\Category::count() }}</h4>
                    </div>
                    <div class="dash-icon" style="background-color: rgba(155, 81, 224, .1);">
                        <i class="fas fa-tags" style="color: #9b51e0;"></i>
                    </div>
                </div>
                <div class="dash-foot">تصنيفات المنتجات</div>
            </div>
        </div>
    </div>

</div>

<!-- Charts Row -->
<div class="row row-sm">
    <div class="col-lg-6 dash-col">
        <div class="dash-card card h-100">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">المبيعات اليومية</h5>
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

    <div class="col-lg-6 dash-col">
        <div class="dash-card card h-100">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">نظرة عامة</h5>
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
