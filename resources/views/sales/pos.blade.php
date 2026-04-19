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
        <!-- Products Panel -->
        <div class="products-panel">
            <div class="category-tabs" id="categoryTabs">
                <div class="category-tab active" data-category="all">الكل</div>
                @foreach($categories as $category)
                    <div class="category-tab" data-category="{{ $category->id }}">{{ $category->Category_name }}</div>
                @endforeach
            </div>
            <div class="product-grid" id="productGrid">
                <div class="loading-spinner">
                    <i class="las la-spinner"></i>
                    <p>جاري التحميل...</p>
                </div>
            </div>
        </div>

        <!-- Cart Panel -->
        <div class="cart-panel">
            <h4>السلة</h4>
            <div class="cart-items scroll-bar" id="cartItems" data-suppress-scroll-x="true">
                <div class="text-center text-muted" id="emptyCart">
                    <p>السلة فارغة</p>
                    <p>اضف منتجات للبيع</p>
                </div>
            </div>
            <div class="cart-totals">
                <div class="total-row">
                    <span>المجموع:</span>
                    <span id="subtotal">0.00</span>
                </div>
                <div class="total-row">
                    <span>الضريبة:</span>
                    <span id="taxAmount">0.00</span>
                </div>
                <div class="total-row">
                    <span>الخصم:</span>
                    <div>
                        <input type="number" id="discountValue" value="0" min="0" step="0.01" style="width: 80px; text-align: left;">
                        <span>ر.س</span>
                    </div>
                </div>
                <div class="total-row grand-total">
                    <span>الاجمالي:</span>
                    <span id="grandTotal">0.00</span>
                </div>
            </div>
            <button class="btn btn-success btn-block btn-lg payment-btn" style="margin-top: 15px;">
                دفع (F2)
            </button>
            <button class="btn btn-outline-danger btn-block clear-cart-btn">
                Clear السلة (Esc)
            </button>
            <div class="hotkey-hint">
                <kbd>F2</kbd> دفع | <kbd>F4</kbd> تعليق | <kbd>Esc</kbd> clear
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">الدفع</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="payment-methods">
                        <div class="payment-method active" data-method="cash">
                            <i class="las la-money-bill-wave" style="font-size: 32px;"></i>
                            <div>نقدي</div>
                        </div>
                        <div class="payment-method" data-method="card">
                            <i class="las la-credit-card" style="font-size: 32px;"></i>
                            <div>بطاقة</div>
                        </div>
                        <div class="payment-method" data-method="split">
                            <i class="las la-split" style="font-size: 32px;"></i>
                            <div>Split</div>
                        </div>
                    </div>
                    
                    <div id="cashPayment" class="payment-section">
                        <div class="form-group">
                            <label>المبلغ المستلم</label>
                            <input type="number" step="0.01" class="form-control amount-input" id="cashAmount" placeholder="0.00">
                        </div>
                        <div class="change-display">
                            <span>الباقي: </span>
                            <span id="changeDisplay">0.00</span>
                        </div>
                    </div>

                    <div id="cardPayment" class="payment-section" style="display: none;">
                        <div class="alert alert-info">سيتم دفع المبلغ via بطاقة</div>
                        <input type="hidden" id="cardAmount" value="0">
                    </div>

                    <div id="splitPayment" class="payment-section" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>المبلغ نقدي</label>
                                    <input type="number" step="0.01" class="form-control" id="splitCashAmount" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>المبلغ بطاقة</label>
                                    <input type="number" step="0.01" class="form-control" id="splitCardAmount" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="change-display">
                            <span>الباقي: </span>
                            <span id="splitChangeDisplay">0.00</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                    <button type="button" class="btn btn-success btn-lg confirm-payment-btn">تاكيد الدفع</button>
                </div>
            </div>
        </div>
    </div>

    <form id="saleForm" method="POST" action="{{ route('sales.store') }}">
        @csrf
        <input type="hidden" name="customer_id" id="customerId">
        <input type="hidden" name="items_json" id="itemsJson">
        <input type="hidden" name="subtotal" id="subtotalInput">
        <input type="hidden" name="tax_amount" id="taxAmountInput">
        <input type="hidden" name="discount" id="discountInput">
        <input type="hidden" name="total" id="totalInput">
        <input type="hidden" name="payment_method" id="paymentMethod">
        <input type="hidden" name="cash_amount" id="cashAmountInput">
        <input type="hidden" name="card_amount" id="cardAmountInput">
    </form>

    <form id="suspendForm" method="POST" action="{{ route('sales.suspend') }}">
        @csrf
        <input type="hidden" name="customer_id" id="suspendCustomerId">
        <input type="hidden" name="items_json" id="suspendItemsJson">
    </form>

@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        // Global cart state
        let cart = [];
        let selectedCategory = 'all';
        let currentSearch = '';
        const TAX_RATE = 0.15;
        let currentPaymentMethod = 'cash';
        let searchTimeout = null;

        // Initialize
        $(document).ready(function() {
            $('.select2').select2();
            
            // Load products on page load
            loadProducts('all');
            
            // Discount input handler
            $('#discountValue').on('input', function() {
                updateTotals();
            });
            
            // Cash amount handler
            $('#cashAmount').on('input', function() {
                calculateChange();
            });
            
            // Split amount handlers
            $('#splitCashAmount, #splitCardAmount').on('input', function() {
                calculateSplitChange();
            });
            
            // Event delegation for quantity buttons (cart items rendered dynamically)
            $(document).on('click', '.qty-btn', function(e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                var change = $(this).data('change');
                updateQty(productId, change);
            });
            
            // Event delegation for remove from cart button
            $(document).on('click', '.remove-item-btn', function(e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                removeFromCart(productId);
            });
            
            // Event delegation for payment button
            $(document).on('click', '.payment-btn', function(e) {
                e.preventDefault();
                openPaymentModal();
            });
            
            // Event delegation for clear cart button
            $(document).on('click', '.clear-cart-btn', function(e) {
                e.preventDefault();
                clearCart();
            });
            
            // Event delegation for suspend sale button
            $(document).on('click', '.suspend-sale-btn', function(e) {
                e.preventDefault();
                suspendSale();
            });
            
            // Event delegation for payment methods
            $(document).on('click', '.payment-method', function(e) {
                e.preventDefault();
                var method = $(this).data('method');
                selectPaymentMethod(method);
            });
            
            // Event delegation for confirm payment button
            $(document).on('click', '.confirm-payment-btn', function(e) {
                e.preventDefault();
                processPayment();
            });
            
            // Category tabs click handler
            $(document).on('click', '.category-tab', function() {
                $('.category-tab').removeClass('active');
                $(this).addClass('active');
                selectedCategory = $(this).data('category');
                loadProducts(selectedCategory);
            });
            
            // Product search with debounce
            $('#productSearch').on('keyup', function(e) {
                currentSearch = $(this).val();
                
                // Clear previous timeout
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                
                // Handle Enter key (barcode scan)
                if (e.key === 'Enter' && currentSearch.length > 0) {
                    handleBarcodeScan(currentSearch);
                    return;
                }
                
                // Debounce search
                searchTimeout = setTimeout(function() {
                    if (currentSearch.length >= 2) {
                        searchProducts(currentSearch);
                    } else if (currentSearch.length === 0) {
                        loadProducts(selectedCategory);
                    }
                }, 300);
            });

            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                if (e.target.tagName === 'INPUT' && e.key !== 'Escape') return;
                if (e.target.tagName === 'SELECT') return;
                
                if (e.key === 'F2') {
                    e.preventDefault();
                    openPaymentModal();
                } else if (e.key === 'F4') {
                    e.preventDefault();
                    suspendSale();
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    clearCart();
                }
            });
            
            // Check for resumed suspended sale
            @if(session('resume_sale'))
                // Restore suspended sale items to cart
                var suspendedItems = @json(session('suspended_items'));
                var suspendedCustomer = @json(session('suspended_customer'));
                
                if (suspendedItems && suspendedItems.length > 0) {
                    cart = suspendedItems.map(function(item) {
                        return {
                            product_id: item.product_id,
                            name: item.name,
                            price: item.price,
                            stock: item.stock,
                            qty: item.qty
                        };
                    });
                    
                    // Set customer if exists
                    if (suspendedCustomer) {
                        $('#customerSelect').val(suspendedCustomer).trigger('change');
                    }
                    
                    renderCart();
                    alert('تم استئناف البيع المعلق');
                }
            @endif
        });

        // Load products via AJAX
        function loadProducts(categoryId) {
            $('#productGrid').html('<div class="loading-spinner"><i class="las la-spinner"></i><p>جاري التحميل...</p></div>');
            
            $.ajax({
                url: '{{ url("pos/products") }}',
                type: 'GET',
                data: { category_id: categoryId },
                success: function(response) {
                    if (response.success) {
                        renderProducts(response.products);
                    }
                },
                error: function() {
                    $('#productGrid').html('<div class="text-center text-danger">حدث خطأ في التحميل</div>');
                }
            });
        }

        // Render products to grid
        function renderProducts(products) {
            if (products.length === 0) {
                $('#productGrid').html('<div class="text-center text-muted p-4">لا توجد منتجات</div>');
                return;
            }
            
            let html = '';
            products.forEach(function(product) {
                let imageHtml = '';
                if (product.photo) {
                    imageHtml = `<img src="{{ asset('uploads/products') }}/${product.photo}" class="product-image" alt="${product.Product_name}">`;
                } else {
                    imageHtml = `<div class="product-image-placeholder"><i class="las la-box"></i></div>`;
                }
                
                html += `
                    <div class="product-card" onclick="addToCartDirect(${product.id}, '${product.Product_name}', ${product.sell_price}, ${product.stock_qty})">
                        ${imageHtml}
                        <div class="product-name">${product.Product_name}</div>
                        <div class="product-price">${parseFloat(product.sell_price).toFixed(2)}</div>
                        <div class="product-stock">المخزون: ${product.stock_qty}</div>
                    </div>
                `;
            });
            $('#productGrid').html(html);
        }

        // Search products via AJAX
        function searchProducts(query) {
            $('#productGrid').html('<div class="loading-spinner"><i class="las la-spinner"></i><p>جاري البحث...</p></div>');
            
            $.ajax({
                url: '{{ url("pos/products/search") }}',
                type: 'GET',
                data: { q: query },
                success: function(response) {
                    if (response.success) {
                        renderProducts(response.products);
                    }
                },
                error: function() {
                    $('#productGrid').html('<div class="text-center text-danger">حدث خطأ في البحث</div>');
                }
            });
        }

        // Handle barcode scan
        function handleBarcodeScan(barcode) {
            $.ajax({
                url: '{{ url("pos/products/barcode") }}/' + barcode.trim(),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var p = response.product;
                        addToCartDirect(p.id, p.Product_name, parseFloat(p.sell_price), parseInt(p.stock_qty) || 0);
                    } else {
                        alert(response.message || 'المنتج غير موجود!');
                    }
                    $('#productSearch').val('').focus();
                },
                error: function() {
                    alert('المنتج غير موجود!');
                    $('#productSearch').val('').focus();
                }
            });
        }

        // Add product to cart (direct call from onclick)
        function addToCartDirect(productId, name, price, stock) {
            console.log('Adding product:', productId, name, price, stock);
            
            const existingItem = cart.find(item => item.product_id === productId);
            const currentQty = existingItem ? existingItem.qty : 0;

            if (currentQty >= stock) {
                alert('المخزون غير كافٍ!');
                return;
            }

            if (existingItem) {
                existingItem.qty++;
            } else {
                cart.push({
                    product_id: productId,
                    name: name,
                    price: price,
                    stock: stock,
                    qty: 1
                });
            }
            
            console.log('Cart:', cart);
            renderCart();
        }

        // Add product to cart (from event delegation)
        function addToCart(productId, $element) {
            if (!productId) {
                console.error('Product ID not found');
                return;
            }
            
            // Show loading state
            if ($element) {
                $element.addClass('adding');
            }
            
            // Check stock - find the product card in the DOM
            const productCard = $(`.product-card[data-id="${productId}"]`);
            const stockQty = parseInt(productCard.data('stock')) || 0;
            const existingItem = cart.find(item => item.product_id === productId);
            const currentQty = existingItem ? existingItem.qty : 0;

            if (currentQty >= stockQty) {
                alert('المخزون غير كافٍ!');
                if ($element) {
                    $element.removeClass('adding');
                }
                return;
            }

            if (existingItem) {
                existingItem.qty++;
            } else {
                cart.push({
                    product_id: productId,
                    name: productCard.data('name'),
                    price: parseFloat(productCard.data('price')),
                    stock: stockQty,
                    qty: 1
                });
            }
            
            // Remove adding class
            if ($element) {
                $element.removeClass('adding');
            }
            
            console.log('Cart:', cart);
            renderCart();
        }

        // Remove from cart
        function removeFromCart(productId) {
            cart = cart.filter(item => item.product_id !== productId);
            renderCart();
        }

        // Update quantity
        function updateQty(productId, change) {
            const item = cart.find(item => item.product_id === productId);
            if (item) {
                const newQty = item.qty + change;
                const stockQty = item.stock || 0;
                
                if (change > 0 && newQty > stockQty) {
                    alert('المخزون غير كافٍ!');
                    return;
                }
                
                if (newQty <= 0) {
                    removeFromCart(productId);
                } else {
                    item.qty = newQty;
                    renderCart();
                }
            }
        }

        // Render cart
        function renderCart() {
            const cartItemsEl = document.getElementById('cartItems');
            
            if (!cartItemsEl) return;
            
            // Check if empty cart element exists, if not create it
            let emptyCart = document.getElementById('emptyCart');
            if (!emptyCart) {
                emptyCart = document.createElement('div');
                emptyCart.id = 'emptyCart';
                emptyCart.className = 'text-center text-muted';
                emptyCart.innerHTML = '<p>السلة فارغة</p><p>اضف منتجات للبيع</p>';
            }
            
            if (cart.length === 0) {
                cartItemsEl.innerHTML = '';
                cartItemsEl.appendChild(emptyCart);
                emptyCart.style.display = 'block';
                updateTotals();
                return;
            }

            emptyCart.style.display = 'none';
            let html = '';
            let subtotal = 0;

            cart.forEach(item => {
                const itemTotal = item.price * item.qty;
                subtotal += itemTotal;
                const stockWarning = item.qty >= item.stock ? ' (الحد الاقصى)' : '';
                html += `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">${item.price.toFixed(2)} × ${item.qty} = ${itemTotal.toFixed(2)}${stockWarning}</div>
                        </div>
                        <div class="cart-item-qty">
                            <button class="qty-btn" data-product-id="${item.product_id}" data-change="-1">-</button>
                            <span>${item.qty}</span>
                            <button class="qty-btn" data-product-id="${item.product_id}" data-change="1">+</button>
                            <button class="btn btn-sm btn-danger remove-item-btn" data-product-id="${item.product_id}">
                                <i class="las la-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            // Preserve empty cart element
            cartItemsEl.innerHTML = html;
            if (cartItemsEl.contains(emptyCart)) {
                cartItemsEl.removeChild(emptyCart);
            }
            updateTotals();
        }

        // Update totals
        function updateTotals() {
            let subtotal = 0;
            cart.forEach(item => {
                subtotal += item.price * item.qty;
            });

            const taxAmount = subtotal * TAX_RATE;
            let discount = parseFloat($('#discountValue').val()) || 0;
            if (discount < 0) discount = 0;
            if (discount > subtotal) discount = subtotal;
            $('#discountValue').val(discount.toFixed(2));
            
            const total = subtotal + taxAmount - discount;

            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('taxAmount').textContent = taxAmount.toFixed(2);
            document.getElementById('grandTotal').textContent = total.toFixed(2);

            window.cartSubtotal = subtotal;
            window.cartTaxAmount = taxAmount;
            window.cartDiscount = discount;
            window.cartTotal = total;
        }

        // Clear cart
        function clearCart() {
            if (cart.length > 0) {
                if (confirm('هل انت متاكد من افراغ السلة؟')) {
                    cart = [];
                    renderCart();
                }
            }
        }

        // Payment methods
        function selectPaymentMethod(method) {
            currentPaymentMethod = method;
            $('.payment-method').removeClass('active');
            $(`.payment-method[data-method="${method}"]`).addClass('active');
            
            $('#cashPayment, #cardPayment, #splitPayment').hide();
            if (method === 'cash') $('#cashPayment').show();
            else if (method === 'card') $('#cardPayment').show();
            else if (method === 'split') $('#splitPayment').show();
        }

        function calculateChange() {
            const cash = parseFloat($('#cashAmount').val()) || 0;
            const total = window.cartTotal || 0;
            const change = cash - total;
            $('#changeDisplay').text(Math.max(0, change).toFixed(2));
        }

        function calculateSplitChange() {
            const cash = parseFloat($('#splitCashAmount').val()) || 0;
            const card = parseFloat($('#splitCardAmount').val()) || 0;
            const total = window.cartTotal || 0;
            const paid = cash + card;
            const change = paid - total;
            $('#splitChangeDisplay').text(Math.max(0, change).toFixed(2));
        }

        // Open payment modal
        function openPaymentModal() {
            if (cart.length === 0) {
                alert('السلة فارغة!');
                return;
            }
            
            currentPaymentMethod = 'cash';
            $('.payment-method').removeClass('active');
            $('.payment-method[data-method="cash"]').addClass('active');
            $('#cashPayment').show();
            $('#cardPayment, #splitPayment').hide();
            $('#cashAmount').val('');
            $('#splitCashAmount').val('');
            $('#splitCardAmount').val('');
            $('#changeDisplay').text('0.00');
            $('#splitChangeDisplay').text('0.00');
            
            $('#paymentModal').modal('show');
            $('#cashAmount').focus();
        }

        // Process payment
        function processPayment() {
            const total = window.cartTotal || 0;
            let cashAmount = 0;
            let cardAmount = 0;
            
            if (currentPaymentMethod === 'cash') {
                cashAmount = parseFloat($('#cashAmount').val()) || 0;
                if (cashAmount < total) {
                    alert('المبلغ غير كافٍ!');
                    return;
                }
            } else if (currentPaymentMethod === 'card') {
                cardAmount = total;
            } else if (currentPaymentMethod === 'split') {
                cashAmount = parseFloat($('#splitCashAmount').val()) || 0;
                cardAmount = parseFloat($('#splitCardAmount').val()) || 0;
                if ((cashAmount + cardAmount) < total) {
                    alert('المبلغ غير كافٍ!');
                    return;
                }
            }

            $('#customerId').val($('#customerSelect').val());
            $('#itemsJson').val(JSON.stringify(cart));
            $('#subtotalInput').val(window.cartSubtotal);
            $('#taxAmountInput').val(window.cartTaxAmount);
            $('#discountInput').val(window.cartDiscount || 0);
            $('#totalInput').val(total);
            $('#paymentMethod').val(currentPaymentMethod);
            $('#cashAmountInput').val(cashAmount);
            $('#cardAmountInput').val(cardAmount);

            $('#paymentModal').modal('hide');

            var formData = $('#saleForm').serialize();
            $.ajax({
                url: '{{ route("sales.store") }}',
                type: 'POST',
                data: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (res) {
                    if (res.success) {
                        // Silent direct print — fire and forget
                        $.get('{{ url("print/receipt") }}/' + res.sale_id)
                            .fail(function (xhr) {
                                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'تعذّرت الطباعة، تحقق من اتصال الطابعة';
                                showPosToast(msg, 'warning');
                            });

                        cart = [];
                        renderCart();
                        $('#customerSelect').val('').trigger('change');
                        showPosToast('تم البيع بنجاح ✓', 'success');
                    } else {
                        showPosToast('خطأ في حفظ البيع', 'danger');
                    }
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'حدث خطأ، حاول مجدداً';
                    showPosToast(msg, 'danger');
                }
            });
        }

        function showPosToast(message, type) {
            var id = 'posToast_' + Date.now();
            var bg = type === 'success' ? 'bg-success' : (type === 'warning' ? 'bg-warning' : 'bg-danger');
            var toast = $('<div id="' + id + '" style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;min-width:260px;" class="alert alert-' + type + ' text-center shadow">' + message + '</div>');
            $('body').append(toast);
            setTimeout(function () { $('#' + id).fadeOut(400, function () { $(this).remove(); }); }, 3000);
        }

        // Suspend sale
        function suspendSale() {
            if (cart.length === 0) {
                alert('السلة فارغة!');
                return;
            }

            $('#suspendCustomerId').val($('#customerSelect').val());
            $('#suspendItemsJson').val(JSON.stringify(cart));
            $('#suspendForm').submit();
        }
    </script>
@endsection