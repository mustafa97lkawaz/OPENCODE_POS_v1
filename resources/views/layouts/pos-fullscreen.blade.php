<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شاشة البيع (POS)</title>
    <link href="{{ URL::asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/icons.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { 
            margin: 0; 
            padding: 0; 
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f5f5f5;
        }
        .pos-fullscreen {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .pos-header {
            background: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .pos-container {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        .products-panel {
            flex: 3;
            overflow-y: auto;
            padding: 15px;
        }
        .cart-panel {
            flex: 1;
            min-width: 350px;
            background: #fff;
            padding: 15px;
            display: flex;
            flex-direction: column;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
        }
        .product-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .product-card:hover {
            border-color: #4a9eff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .product-card .product-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
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
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .category-tab {
            padding: 8px 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 20px;
            cursor: pointer;
        }
        .category-tab:hover, .category-tab.active {
            background: #4a9eff;
            color: #fff;
        }
        .cart-items {
            flex: 1;
            overflow-y: auto;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #eee;
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
            cursor: pointer;
        }
        .cart-totals {
            border-top: 2px solid #eee;
            padding-top: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .total-row.grand-total {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .search-box input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 300px;
        }
        .customer-select select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .btn-success { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-danger { background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .loading-spinner { text-align: center; padding: 40px; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: #fff; margin: 10% auto; padding: 20px; width: 500px; border-radius: 10px; }
    </style>
    @yield('css')
</head>
<body>
    @yield('content')
    
    <script src="{{ URL::asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    @yield('js')
</body>
</html>