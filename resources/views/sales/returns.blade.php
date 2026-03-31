@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المرتجعات</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة المرتجعات</span>
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
                        <a class="modal-effect btn btn-outline-primary btn-block" href="{{ route('sales.index') }}">جميع المبيعات</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='50'>
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">#</th>
                                    <th class="border-bottom-0">رقم الفاتورة</th>
                                    <th class="border-bottom-0">المنتج</th>
                                    <th class="border-bottom-0">الكمية</th>
                                    <th class="border-bottom-0">السعر</th>
                                    <th class="border-bottom-0">الاجمالي</th>
                                    <th class="border-bottom-0">السبب</th>
                                    <th class="border-bottom-0">تاريخ الارجاع</th>
                                    <th class="border-bottom-0">بواسطه</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($returns as $return)
                                    <?php $i++; ?>
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $return->sale->invoice_number ?? '-' }}</td>
                                        <td>{{ $return->product->Product_name ?? 'منتج محذوف' }}</td>
                                        <td>{{ $return->qty }}</td>
                                        <td>{{ number_format($return->unit_price, 2) }}</td>
                                        <td>{{ number_format($return->total, 2) }}</td>
                                        <td>{{ $return->reason ?? '-' }}</td>
                                        <td>{{ $return->created_at }}</td>
                                        <td>{{ $return->Created_by }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
@endsection
