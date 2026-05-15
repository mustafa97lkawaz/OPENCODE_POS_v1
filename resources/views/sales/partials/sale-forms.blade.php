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
