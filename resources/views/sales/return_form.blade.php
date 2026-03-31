@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المرتجعات</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ اضافة مرتجع</span>
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

    @if (session()->has('delete'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('delete') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Invoice Info Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">معلومات الفاتورة الاصلية</h4>
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>رقم الفاتورة</label>
                                <input type="text" class="form-control" value="{{ $sale->invoice_number }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>العميل</label>
                                <input type="text" class="form-control" value="{{ $sale->customer->Customer_name ?? 'زائر' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>تاريخ البيع</label>
                                <input type="text" class="form-control" value="{{ $sale->created_at }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>المبلغ الاجمالي</label>
                                <input type="text" class="form-control" value="{{ number_format($sale->total, 2) }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Return Form -->
    <form action="{{ route('sales.return.store') }}" method="POST" id="returnForm">
        @csrf
        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
        
        <div class="row">
            <div class="col-md-12">
                <div class="card mg-b-20">
                    <div class="card-header pb-0">
                        <h4 class="card-title">اختر المنتجات للمرتجع</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-md-nowrap">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>المنتج</th>
                                        <th>الكمية المباعة</th>
                                        <th>السعر</th>
                                        <th>الاجمالي</th>
                                        <th>كمية المرتجع</th>
                                        <th>اجمالي المرتجع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->saleItems as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->product->Product_name ?? 'منتج محذوف' }}</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>{{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ number_format($item->total, 2) }}</td>
                                            <td>
                                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                                <input type="hidden" name="items[{{ $index }}][sold_qty]" value="{{ $item->qty }}">
                                                <input type="hidden" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}">
                                                <input type="number" 
                                                       name="items[{{ $index }}][return_qty]" 
                                                       class="form-control return-qty" 
                                                       min="0" 
                                                       max="{{ $item->qty }}" 
                                                       value="0"
                                                       data-price="{{ $item->unit_price }}"
                                                       data-index="{{ $index }}">
                                            </td>
                                            <td>
                                                <span class="row-total" id="row-total-{{ $index }}">0.00</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6" class="text-left">اجمالي المرتجع:</th>
                                        <th><span id="total-return">0.00</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header pb-0">
                        <h4 class="card-title">سبب المرتجع</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>اختر السبب</label>
                            <select name="reason" class="form-control">
                                <option value="">-- اختر السبب --</option>
                                <option value="منتج تالف">منتج تالف</option>
                                <option value="خطأ في الطلب">خطأ في الطلب</option>
                                <option value="عدم الرضا">عدم الرضا</option>
                                <option value="منتج مختلف">منتج مختلف</option>
                                <option value="اخرى">اخرى</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header pb-0">
                        <h4 class="card-title">تاكيد المرتجع</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="las la-info-circle"></i>
                            سيتم اضافة الكميات المرتجعة للمخزون تلقائيا
                        </div>
                        <button type="submit" class="btn btn-warning btn-block" id="submitBtn">
                            <i class="las la-undo"></i> تاكيد المرتجع
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
@section('js')
    <script>
        $(document).ready(function() {
            // Calculate row total when return qty changes
            $('.return-qty').on('input', function() {
                var maxQty = parseInt($(this).attr('max'));
                var enteredQty = parseInt($(this).val()) || 0;
                
                // Validate max quantity
                if (enteredQty > maxQty) {
                    $(this).val(maxQty);
                    enteredQty = maxQty;
                }
                if (enteredQty < 0) {
                    $(this).val(0);
                    enteredQty = 0;
                }

                var price = parseFloat($(this).data('price'));
                var rowTotal = enteredQty * price;
                var index = $(this).data('index');
                
                $('#row-total-' + index).text(rowTotal.toFixed(2));
                
                calculateTotal();
            });

            function calculateTotal() {
                var total = 0;
                $('.return-qty').each(function() {
                    var qty = parseInt($(this).val()) || 0;
                    var price = parseFloat($(this).data('price'));
                    total += qty * price;
                });
                $('#total-return').text(total.toFixed(2));
            }

            // Form validation
            $('#returnForm').on('submit', function(e) {
                var hasReturn = false;
                $('.return-qty').each(function() {
                    if (parseInt($(this).val()) > 0) {
                        hasReturn = true;
                    }
                });

                if (!hasReturn) {
                    e.preventDefault();
                    alert('يرجي تحديد كمية المرتجع');
                    return false;
                }

                var reason = $('select[name="reason"]').val();
                if (!reason) {
                    e.preventDefault();
                    alert('يرجي اختيار سبب المرتجع');
                    return false;
                }
            });
        });
    </script>
@endsection
