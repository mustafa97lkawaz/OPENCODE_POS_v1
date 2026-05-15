/* POS screen logic — extracted from resources/views/sales/pos.blade.php (M5).
 * Server-injected values arrive via window.POS_ROUTES and window.POS_BOOT
 * (defined inline in the blade before this file loads). */

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

    // Check for resumed suspended sale (server-injected via POS_BOOT)
    if (window.POS_BOOT && POS_BOOT.resume) {
        var suspendedItems = POS_BOOT.items;
        var suspendedCustomer = POS_BOOT.customer;

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
    }
});

// Load products via AJAX
function loadProducts(categoryId) {
    $('#productGrid').html('<div class="loading-spinner"><i class="las la-spinner"></i><p>جاري التحميل...</p></div>');

    $.ajax({
        url: POS_ROUTES.products,
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
            imageHtml = `<img src="${POS_ROUTES.productImg}${product.photo}" class="product-image" alt="${product.Product_name}">`;
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
        url: POS_ROUTES.search,
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
        url: POS_ROUTES.barcode + barcode.trim(),
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
        url: POS_ROUTES.store,
        type: 'POST',
        data: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function (res) {
            if (res.success) {
                // Silent direct print — fire and forget
                $.get(POS_ROUTES.printReceipt + res.sale_id)
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
