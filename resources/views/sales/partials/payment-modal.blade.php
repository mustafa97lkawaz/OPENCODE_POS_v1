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
