@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">تعديلات المخزون</h4>
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
                        <a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-scale"
                            data-toggle="modal" href="#exampleModal">اضافة تعديل مخزون</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='50'>
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">#</th>
                                    <th class="border-bottom-0">المنتج</th>
                                    <th class="border-bottom-0">نوع التعديل</th>
                                    <th class="border-bottom-0">الكمية</th>
                                    <th class="border-bottom-0">السبب</th>
                                    <th class="border-bottom-0">تاريخ التعديل</th>
                                    <th class="border-bottom-0">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($stock_adjustments as $adjustment)
                                    <?php $i++; ?>
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $adjustment->product->Product_name ?? '-' }}</td>
                                        <td>
                                            @if($adjustment->type == 'damaged')
                                                <span class="badge badge-danger">تالف</span>
                                            @elseif($adjustment->type == 'expired')
                                                <span class="badge badge-warning">منتهي الصلاحية</span>
                                            @elseif($adjustment->type == 'added')
                                                <span class="badge badge-success">اضافة</span>
                                            @elseif($adjustment->type == 'removed')
                                                <span class="badge badge-info">ازالة</span>
                                            @endif
                                        </td>
                                        <td>{{ $adjustment->qty_change }}</td>
                                        <td>{{ $adjustment->reason }}</td>
                                        <td>{{ $adjustment->created_at }}</td>
                                        <td>
                                            <button class="btn btn-outline-danger btn-sm"
                                                data-id="{{ $adjustment->id }}"
                                                data-product="{{ $adjustment->product->Product_name ?? '-' }}"
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

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">اضافة تعديل مخزون</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('stock_adjustments.store') }}" method="post">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-group">
                                <label>المنتج</label>
                                <select name="product_id" class="form-control" required>
                                    <option value="" selected disabled>--اختر المنتج--</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->Product_name }} (متوفر: {{ $product->stock_qty }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>نوع التعديل</label>
                                <select name="type" class="form-control" required>
                                    <option value="added">اضافة (+)</option>
                                    <option value="removed">ازالة (-)</option>
                                    <option value="damaged">تالف (-)</option>
                                    <option value="expired">منتهي الصلاحية (-)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>الكمية</label>
                                <input type="number" class="form-control" name="qty_change" required min="1">
                            </div>
                            <div class="form-group">
                                <label>السبب</label>
                                <textarea class="form-control" name="reason" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">تاكيد</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modaldemo9" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف تعديل المخزون</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" method="post" id="deleteForm">
                        {{ method_field('delete') }}
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <p>هل انت متاكد من حذف هذا التعديل؟</p>
                            <p>سيتم ازالة الكمية من المخزون/اضافتها للمخزون حسب التعديل</p><br>
                            <input type="hidden" name="id" id="id" value="">
                            <input class="form-control" name="product_name" id="product_name" type="text" readonly>
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
        $('#modaldemo9').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var product = button.data('product')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #product_name').val(product);
            modal.find('#deleteForm').attr('action', '{{ url("stock_adjustments") }}/' + id);
        })
    </script>
@endsection
