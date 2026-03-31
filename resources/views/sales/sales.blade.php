@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المبيعات</h4>
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

    <div class="row">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <a class="modal-effect btn btn-outline-primary btn-block" href="{{ route('pos') }}">شاشة البيع (POS)</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='50'>
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">#</th>
                                    <th class="border-bottom-0">رقم الفاتورة</th>
                                    <th class="border-bottom-0">العميل</th>
                                    <th class="border-bottom-0">المبلغ الاجمالي</th>
                                    <th class="border-bottom-0">الخصم</th>
                                    <th class="border-bottom-0">طريقة الدفع</th>
                                    <th class="border-bottom-0">الحالة</th>
                                    <th class="border-bottom-0">تاريخ البيع</th>
                                    <th class="border-bottom-0">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($sales as $sale)
                                    <?php $i++; ?>
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $sale->customer->Customer_name ?? 'زائر' }}</td>
                                        <td>{{ number_format($sale->total, 2) }}</td>
                                        <td>{{ number_format($sale->discount, 2) }}</td>
                                        <td>
                                            @if($sale->payment_method == 'cash')
                                                نقدي
                                            @elseif($sale->payment_method == 'card')
                                                بطاقة
                                            @elseif($sale->payment_method == 'split')
                                                Split (نقدي: {{ number_format($sale->cash_amount, 2) }} - بطاقة: {{ number_format($sale->card_amount, 2) }})
                                            @endif
                                        </td>
                                        <td>
                                            @if($sale->Status == 'completed')
                                                <span class="badge badge-success">مكتملة</span>
                                            @elseif($sale->Status == 'suspended')
                                                <span class="badge badge-warning">معلقة</span>
                                            @endif
                                        </td>
                                        <td>{{ $sale->created_at }}</td>
                                        <td>
                                            <button class="btn btn-outline-primary btn-sm" data-id="{{ $sale->id }}" data-toggle="modal" data-target="#view_sale">عرض</button>
                                            <a href="{{ route('sales.return', $sale->id) }}" class="btn btn-outline-warning btn-sm">مرتجعات</a>
                                            <button class="btn btn-outline-danger btn-sm"
                                                data-id="{{ $sale->id }}"
                                                data-invoice="{{ $sale->invoice_number }}"
                                                data-toggle="modal"
                                                data-target="#modaldemo9">حذف</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="view_sale" tabindex="-1" role="dialog" aria-labelledby="view_saleLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تفاصيل الفاتورة</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="sale_details">
                        <!-- Sale items will be loaded here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modaldemo9" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف الفاتورة</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('sales.destroy', '') }}" method="post" id="deleteForm">
                        {{ method_field('delete') }}
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <p>هل انت متاكد من عملية الحذف ؟</p><br>
                            <p>سيتم اضافة الكميات المستردة للمخزون</p><br>
                            <input type="hidden" name="id" id="id" value="">
                            <input class="form-control" name="invoice_number" id="invoice_number" type="text" readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                            <button type="submit" class="btn btn-danger">تاكيد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    <script>
        // View sale details modal
        $('#view_sale').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var modal = $(this)
            modal.find('.modal-body').html('<div class="text-center"><i class="las la-spinner la-spin" style="font-size: 24px;"></i> جاري التحميل...</div>');
            
            // Fetch sale details via AJAX
            $.ajax({
                url: '{{ url("sales") }}/' + id,
                type: 'GET',
                success: function(response) {
                    modal.find('.modal-body').html(response);
                },
                error: function() {
                    modal.find('.modal-body').html('<div class="alert alert-danger">حدث خطأ في تحميل التفاصيل</div>');
                }
            });
        })
        
        // Delete sale modal
        $('#modaldemo9').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var invoice = button.data('invoice')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #invoice_number').val(invoice);
            modal.find('#deleteForm').attr('action', '{{ route("sales.index") }}/' + id);
        })
    </script>
@endsection
