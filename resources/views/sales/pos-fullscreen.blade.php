@extends('layouts.pos-fullscreen')
@section('css')
    <style>
        .pos-container { height: calc(100vh - 80px); }
    </style>
@endsection

@section('content')
    <div class="pos-fullscreen">
        <div class="pos-header">
            <div class="search-box">
                <input type="text" id="productSearch" placeholder="البحث بالاسم او barcode..." autofocus>
            </div>
            <div class="customer-select">
                <select class="form-control" id="customerSelect" style="width: 200px;">
                    <option value="">-- زائر --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->Customer_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button class="btn btn-warning" onclick="suspendSale()">
                    <i class="las la-pause"></i> تعليق (F4)
                </button>
            </div>
        </div>

        <div class="pos-container">
            <div class="products-panel">
                <div class="category-tabs" id="categoryTabs">
                    <div class="category-tab active" data-category="all">الكل</div>
                    @foreach($categories as $category)
                        <div class="category-tab" data-category="{{ $category->id }}">{{ $category->Category_name }}</div>
                    @endforeach
                </div>
                <div class="product-grid" id="productGrid">
                    <div class="loading-spinner">
                        <p>جاري التحميل...</p>
                    </div>
                </div>
            </div>

            <div class="cart-panel">
                <h4>السلة</h4>
                <div class="cart-items" id="cartItems">
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
                        <span>الضريبة (15%):</span>
                        <span id="taxAmount">0.00</span>
                    </div>
                    <div class="total-row">
                        <span>الخصم:</span>
                        <input type="number" id="discountValue" value="0" min="0" step="0.01" style="width: 80px;">
                    </div>
                    <div class="total-row grand-total">
                        <span>الاجمالي:</span>
                        <span id="grandTotal">0.00</span>
                    </div>
                </div>
                <button class="btn btn-success btn-block btn-lg" style="margin-top: 15px;" onclick="openPaymentModal()">
                    دفع (F2)
                </button>
                <button class="btn btn-danger btn-block" style="margin-top: 10px;" onclick="clearCart()">
                    مسح السلة (Esc)
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal" id="paymentModal">
        <div class="modal-content">
            <h4 class="mb-3">الدفع</h4>
            <div class="payment-methods mb-3">
                <button class="btn btn-primary active" data-method="cash" onclick="selectPaymentMethod('cash')">نقدي</button>
                <button class="btn btn-secondary" data-method="card" onclick="selectPaymentMethod('card')">بطاقة</button>
                <button class="btn btn-secondary" data-method="split" onclick="selectPaymentMethod('split')">تقسيم</button>
            </div>
            <div id="cashPayment">
                <div class="form-group">
                    <label>المبلغ المستلم</label>
                    <input type="number" class="form-control" id="cashAmount" placeholder="0.00">
                </div>
                <div class="text-center mb-3">
                    <strong>الباقي: </strong><span id="changeDisplay">0.00</span>
                </div>
            </div>
            <div id="cardPayment" style="display:none;">
                <div class="alert alert-info">سيتم الدفع بالبطاقة</div>
            </div>
            <div id="splitPayment" style="display:none;">
                <div class="row">
                    <div class="col-6">
                        <input type="number" class="form-control" id="splitCashAmount" placeholder="نقدي">
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control" id="splitCardAmount" placeholder="بطاقة">
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <button class="btn btn-secondary" onclick="$('#paymentModal').hide()">الغاء</button>
                <button class="btn btn-success" onclick="processPayment()">تاكيد</button>
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
    <script>
        let cart = [];
        let selectedCategory = 'all';
        let currentSearch = '';
        const TAX_RATE = 0.15;
        let currentPaymentMethod = 'cash';
        let searchTimeout = null;

        $(document).ready(function() {
            loadProducts('all');
            
            $('#discountValue').on('input', updateTotals);
            $('#cashAmount').on('input', calculateChange);
            
            $(document).on('click', '.product-card', function() {
                var productId = $(this).data('id');
                addToCartDirect(productId, $(this).data('name'), $(this).data('price'), $(this).data('stock'));
            });
            
            $(document).on('click', '.qty-btn', function() {
                updateQty($(this).data('product-id'), $(this).data('change'));
            });
            
            $(document).on('click', '.remove-item-btn', function() {
                removeFromCart($(this).data('product-id'));
            });
            
            $(document).on('click', '.category-tab', function() {
                $('.category-tab').removeClass('active');
                $(this).addClass('active');
                selectedCategory = $(this).data('category');
                loadProducts(selectedCategory);
            });
            
            $('#productSearch').on('keyup', function(e) {
                currentSearch = $(this).val();
                if (searchTimeout) clearTimeout(searchTimeout);
                if (e.key === 'Enter' && currentSearch.length > 0) {
                    handleBarcodeScan(currentSearch);
                    return;
                }
                searchTimeout = setTimeout(function() {
                    if (currentSearch.length >= 2) searchProducts(currentSearch);
                    else if (currentSearch.length === 0) loadProducts(selectedCategory);
                }, 300);
            });

            $(document).on('keydown', function(e) {
                if (e.target.tagName === 'INPUT' && e.key !== 'Escape') return;
                if (e.key === 'F2') { e.preventDefault(); openPaymentModal(); }
                else if (e.key === 'F4') { e.preventDefault(); suspendSale(); }
                else if (e.key === 'Escape') { e.preventDefault(); clearCart(); }
            });
        });

        function loadProducts(categoryId) {
            $('#productGrid').html('<div class="loading-spinner"><p>جاري التحميل...</p></div>');
            $.ajax({
                url: '{{ url("pos/products") }}',
                data: { category_id: categoryId },
                success: function(response) {
                    if (response.success) renderProducts(response.products);
                }
            });
        }

        function renderProducts(products) {
            if (!products.length) {
                $('#productGrid').html('<div class="text-center p-4">لا توجد منتجات</div>');
                return;
            }
            let html = '';
            products.forEach(function(p) {
                html += `<div class="product-card" onclick="addToCartDirect(${p.id}, '${p.Product_name}', ${p.sell_price}, ${p.stock_qty})">
                    <div class="product-name">${p.Product_name}</div>
                    <div class="product-price">${parseFloat(p.sell_price).toFixed(2)}</div>
                    <div class="product-stock">المخزون: ${p.stock_qty}</div>
                </div>`;
            });
            $('#productGrid').html(html);
        }

        function searchProducts(query) {
            $('#productGrid').html('<div class="loading-spinner"><p>جاري البحث...</p></div>');
            $.ajax({
                url: '{{ url("pos/products/search") }}',
                data: { q: query },
                success: function(response) {
                    if (response.success) renderProducts(response.products);
                }
            });
        }

        function handleBarcodeScan(barcode) {
            $.ajax({
                url: '{{ url("pos/products/barcode") }}/' + barcode,
                success: function(response) {
                    if (response.success) {
                        addToCartDirect(response.product.id, response.product.Product_name, response.product.sell_price, response.product.stock_qty);
                        $('#productSearch').val('');
                        loadProducts(selectedCategory);
                    } else {
                        alert('المنتج غير موجود!');
                        $('#productSearch').val('');
                    }
                },
                error: function() {
                    alert('المنتج غير موجود!');
                    $('#productSearch').val('');
                }
            });
        }

        function addToCartDirect(productId, name, price, stock) {
            const existing = cart.find(item => item.product_id === productId);
            if (existing) {
                if (existing.qty >= stock) { alert('المخزون غير كافٍ!'); return; }
                existing.qty++;
            } else {
                cart.push({ product_id: productId, name: name, price: price, stock: stock, qty: 1 });
            }
            renderCart();
        }

        function removeFromCart(productId) {
            cart = cart.filter(item => item.product_id !== productId);
            renderCart();
        }

        function updateQty(productId, change) {
            const item = cart.find(item => item.product_id === productId);
            if (!item) return;
            const newQty = item.qty + change;
            if (change > 0 && newQty > item.stock) { alert('المخزون غير كافٍ!'); return; }
            if (newQty <= 0) removeFromCart(productId);
            else { item.qty = newQty; renderCart(); }
        }

        function renderCart() {
            const cartItemsEl = document.getElementById('cartItems');
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
            cart.forEach(item => {
                html += `<div class="cart-item">
                    <div>
                        <div><strong>${item.name}</strong></div>
                        <div>${item.price.toFixed(2)} × ${item.qty} = ${(item.price * item.qty).toFixed(2)}</div>
                    </div>
                    <div class="cart-item-qty">
                        <button class="qty-btn" data-product-id="${item.product_id}" data-change="-1">-</button>
                        <span>${item.qty}</span>
                        <button class="qty-btn" data-product-id="${item.product_id}" data-change="1">+</button>
                        <button class="btn btn-sm btn-danger remove-item-btn" data-product-id="${item.product_id}">×</button>
                    </div>
                </div>`;
            });
            cartItemsEl.innerHTML = html;
            updateTotals();
        }

        function updateTotals() {
            let subtotal = 0;
            cart.forEach(item => subtotal += item.price * item.qty);
            const taxAmount = subtotal * TAX_RATE;
            let discount = parseFloat($('#discountValue').val()) || 0;
            if (discount < 0) discount = 0;
            if (discount > subtotal) discount = subtotal;
            const total = subtotal + taxAmount - discount;
            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('taxAmount').textContent = taxAmount.toFixed(2);
            document.getElementById('grandTotal').textContent = total.toFixed(2);
            window.cartSubtotal = subtotal;
            window.cartTaxAmount = taxAmount;
            window.cartDiscount = discount;
            window.cartTotal = total;
        }

        function clearCart() {
            if (cart.length > 0 && confirm('هل انت متاكد من افراغ السلة؟')) {
                cart = [];
                renderCart();
            }
        }

        function selectPaymentMethod(method) {
            currentPaymentMethod = method;
            $('.payment-methods button').removeClass('active btn-primary').addClass('btn-secondary');
            $(`.payment-methods button[data-method="${method}"]`).removeClass('btn-secondary').addClass('active btn-primary');
            $('#cashPayment, #cardPayment, #splitPayment').hide();
            $('#' + method + 'Payment').show();
        }

        function calculateChange() {
            const cash = parseFloat($('#cashAmount').val()) || 0;
            const change = cash - (window.cartTotal || 0);
            $('#changeDisplay').text(Math.max(0, change).toFixed(2));
        }

        function openPaymentModal() {
            if (cart.length === 0) { alert('السلة فارغة!'); return; }
            currentPaymentMethod = 'cash';
            selectPaymentMethod('cash');
            $('#cashAmount, #splitCashAmount, #splitCardAmount').val('');
            $('#changeDisplay').text('0.00');
            $('#paymentModal').show();
            setTimeout(() => $('#cashAmount').focus(), 100);
        }

        function processPayment() {
            const total = window.cartTotal || 0;
            let cashAmount = 0, cardAmount = 0;
            if (currentPaymentMethod === 'cash') {
                cashAmount = parseFloat($('#cashAmount').val()) || 0;
                if (cashAmount < total) { alert('المبلغ غير كافٍ!'); return; }
            } else if (currentPaymentMethod === 'card') {
                cardAmount = total;
            } else if (currentPaymentMethod === 'split') {
                cashAmount = parseFloat($('#splitCashAmount').val()) || 0;
                cardAmount = parseFloat($('#splitCardAmount').val()) || 0;
                if ((cashAmount + cardAmount) < total) { alert('المبلغ غير كافٍ!'); return; }
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
            $('#saleForm').submit();
        }

        function suspendSale() {
            if (cart.length === 0) { alert('السلة فارغة!'); return; }
            $('#suspendCustomerId').val($('#customerSelect').val());
            $('#suspendItemsJson').val(JSON.stringify(cart));
            $('#suspendForm').submit();
        }

        // Close modal when clicking outside
        $('#paymentModal').on('click', function(e) {
            if (e.target === this) $(this).hide();
        });
    </script>
@endsection