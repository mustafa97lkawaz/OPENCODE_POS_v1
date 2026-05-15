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
