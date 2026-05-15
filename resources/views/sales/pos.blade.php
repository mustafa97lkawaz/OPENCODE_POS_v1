@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .pos-container {
            display: flex;
            height: calc(100vh - 150px);
            gap: 15px;
        }
        .products-panel {
            flex: 3;
            overflow-y: auto;
        }
        .cart-panel {
            flex: 1;
            min-width: 350px;
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
            padding: 10px;
        }
        .product-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            user-select: none;
            overflow: hidden;
            pointer-events: auto;
        }
        .product-card .product-image {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 8px;
            background: #f8f9fa;
        }
        .product-card .product-image-placeholder {
            width: 100%;
            height: 80px;
            border-radius: 6px;
            margin-bottom: 8px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #ccc;
        }
        .product-card:hover {
            border-color: #4a9eff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .product-card.adding {
            opacity: 0.5;
            pointer-events: none;
        }
        .product-card .product-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
            min-height: 40px;
        }
        .product-card .product-price {
            color: #28a745;
            font-size: 18px;
            font-weight: bold;
        }
        .product-card .product-stock {
            color: #666;
            font-size: 12px;
        }
        .category-tabs {
            display: flex;
            gap: 5px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
            overflow-x: auto;
            flex-wrap: nowrap;
        }
        .category-tab {
            padding: 8px 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 20px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .category-tab:hover, .category-tab.active {
            background: #4a9eff;
            color: #fff;
            border-color: #4a9eff;
        }
        .cart-items {
            flex: 1;
            overflow-y: auto;
            margin: 10px 0;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .cart-item-info {
            flex: 1;
        }
        .cart-item-name {
            font-weight: bold;
        }
        .cart-item-price {
            color: #666;
        }
        .cart-item-qty {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .qty-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            border-radius: 4px;
            cursor: pointer;
        }
        .qty-btn:hover {
            background: #4a9eff;
            color: #fff;
            border-color: #4a9eff;
        }
        .cart-totals {
            border-top: 2px solid #eee;
            padding-top: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .total-row.grand-total {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .hotkey-hint {
            font-size: 11px;
            color: #999;
            margin-top: 10px;
        }
        .hotkey-hint kbd {
            background: #eee;
            padding: 2px 6px;
            border-radius: 3px;
            border: 1px solid #ddd;
        }
        .pos-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .search-box {
            flex: 1;
            max-width: 400px;
        }
        .search-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .customer-select {
            min-width: 200px;
        }
        /* Modal styles */
        .payment-modal .modal-body {
            padding: 20px;
        }
        .payment-methods {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .payment-method {
            flex: 1;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .payment-method:hover, .payment-method.active {
            border-color: #4a9eff;
            background: #f0f7ff;
        }
        .payment-method.selected {
            border-color: #28a745;
            background: #f0fff4;
        }
        .amount-input {
            font-size: 24px;
            padding: 15px;
            text-align: left;
        }
        .change-display {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            padding: 20px;
        }
        .loading-spinner {
            text-align: center;
            padding: 20px;
            color: #999;
        }
        .loading-spinner i {
            font-size: 24px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">شاشة البيع (POS)</h4>
            </div>
        </div>
    </div>
@endsection
@section('content')

    @if (session()->has('Add'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('Add') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="pos-header">
        <div class="search-box">
            <input type="text" id="productSearch" placeholder="البحث بالاسم او barcode (يدعم الماسح الضوئي)..." autocomplete="off" autofocus>
        </div>
        <div class="customer-select">
            <select class="form-control select2" id="customerSelect">
                <option value="">-- زائر --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->Customer_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button class="btn btn-warning suspend-sale-btn">
                <i class="las la-pause"></i> تعليق (F4)
            </button>
            <a href="{{ route('suspended.index') }}" class="btn btn-info">
                <i class="las la-list"></i> المبيعات المعلقة
            </a>
        </div>
    </div>

    <div class="pos-container">
        @include('sales.partials.products-grid')

        @include('sales.partials.cart')
    </div>

    @include('sales.partials.payment-modal')

    @include('sales.partials.sale-forms')

@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        window.POS_ROUTES = {
            products:     '{{ url("pos/products") }}',
            search:       '{{ url("pos/products/search") }}',
            barcode:      '{{ url("pos/products/barcode") }}/',
            store:        '{{ route("sales.store") }}',
            printReceipt: '{{ url("print/receipt") }}/',
            productImg:   '{{ asset("uploads/products") }}/'
        };
        window.POS_BOOT = {
@if(session('resume_sale'))
            resume: true,
            items: @json(session('suspended_items')),
            customer: @json(session('suspended_customer'))
@else
            resume: false
@endif
        };
    </script>
    <script src="{{ asset('js/pos.js') }}?v={{ filemtime(public_path('js/pos.js')) }}"></script>
@endsection
