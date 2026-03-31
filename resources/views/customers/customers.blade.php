@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">العملاء</h4>
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

    @if (session()->has('edit'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('edit') }}</strong>
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
                            data-toggle="modal" href="#exampleModal">اضافة عميل</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='50'>
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">#</th>
                                    <th class="border-bottom-0">اسم العميل</th>
                                    <th class="border-bottom-0">الهاتف</th>
                                    <th class="border-bottom-0">النوع</th>
                                    <th class="border-bottom-0">الرصيد</th>
                                    <th class="border-bottom-0">الحالة</th>
                                    <th class="border-bottom-0">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($customers as $customer)
                                    <?php $i++; ?>
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $customer->Customer_name }}</td>
                                        <td>{{ $customer->phone }}</td>
                                        <td>{{ $customer->type == 'walk-in' ? 'عادي' : 'حساب' }}</td>
                                        <td>{{ $customer->account_balance }}</td>
                                        <td>{{ $customer->Status }}</td>
                                        <td>
                                            <button class="btn btn-outline-success btn-sm"
                                                data-id="{{ $customer->id }}"
                                                data-name="{{ $customer->Customer_name }}"
                                                data-phone="{{ $customer->phone }}"
                                                data-email="{{ $customer->email }}"
                                                data-address="{{ $customer->address }}"
                                                data-type="{{ $customer->type }}"
                                                data-balance="{{ $customer->account_balance }}"
                                                data-status="{{ $customer->Status }}"
                                                data-toggle="modal"
                                                data-target="#edit_Product">تعديل</button>

                                            <button class="btn btn-outline-danger btn-sm"
                                                data-id="{{ $customer->id }}"
                                                data-name="{{ $customer->Customer_name }}"
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
                        <h5 class="modal-title">اضافة عميل</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('customers.store') }}" method="post">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-group">
                                <label>اسم العميل</label>
                                <input type="text" class="form-control" name="Customer_name" required>
                            </div>
                            <div class="form-group">
                                <label>الهاتف</label>
                                <input type="text" class="form-control" name="phone">
                            </div>
                            <div class="form-group">
                                <label>البريد</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                            <div class="form-group">
                                <label>العنوان</label>
                                <textarea class="form-control" name="address" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label>النوع</label>
                                <select name="type" class="form-control">
                                    <option value="walk-in">عادي</option>
                                    <option value="account">حساب</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>الرصيد</label>
                                <input type="number" step="0.01" class="form-control" name="account_balance" value="0">
                            </div>
                            <div class="form-group">
                                <label>الحالة</label>
                                <select name="Status" class="form-control">
                                    <option value="مفعل">مفعل</option>
                                    <option value="غير مفعل">غير مفعل</option>
                                </select>
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

        <div class="modal fade" id="edit_Product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تعديل عميل</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('customers.update', '') }}" method="post" id="editForm">
                        {{ method_field('patch') }}
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <input type="hidden" name="id" id="id" value="">
                            <div class="form-group">
                                <label>اسم العميل</label>
                                <input type="text" class="form-control" name="Customer_name" id="Customer_name" required>
                            </div>
                            <div class="form-group">
                                <label>الهاتف</label>
                                <input type="text" class="form-control" name="phone" id="phone">
                            </div>
                            <div class="form-group">
                                <label>البريد</label>
                                <input type="email" class="form-control" name="email" id="email">
                            </div>
                            <div class="form-group">
                                <label>العنوان</label>
                                <textarea name="address" id="address" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label>النوع</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="walk-in">عادي</option>
                                    <option value="account">حساب</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>الرصيد</label>
                                <input type="number" step="0.01" class="form-control" name="account_balance" id="account_balance">
                            </div>
                            <div class="form-group">
                                <label>الحالة</label>
                                <select name="Status" id="Status" class="form-control">
                                    <option value="مفعل">مفعل</option>
                                    <option value="غير مفعل">غير مفعل</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">تعديل البيانات</button>
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
                        <h5 class="modal-title">حذف العميل</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('customers.destroy', '') }}" method="post" id="deleteForm">
                        {{ method_field('delete') }}
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <p>هل انت متاكد من عملية الحذف ؟</p><br>
                            <input type="hidden" name="id" id="id" value="">
                            <input class="form-control" name="Customer_name" id="Customer_name" type="text" readonly>
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
        $('#edit_Product').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var Customer_name = button.data('name')
            var phone = button.data('phone')
            var email = button.data('email')
            var address = button.data('address')
            var type = button.data('type')
            var account_balance = button.data('balance')
            var Status = button.data('status')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #Customer_name').val(Customer_name);
            modal.find('.modal-body #phone').val(phone);
            modal.find('.modal-body #email').val(email);
            modal.find('.modal-body #address').val(address);
            modal.find('.modal-body #type').val(type);
            modal.find('.modal-body #account_balance').val(account_balance);
            modal.find('.modal-body #Status').val(Status);
            modal.find('#editForm').attr('action', '{{ route("customers.index") }}/' + id);
        })

        $('#modaldemo9').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var Customer_name = button.data('name')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #Customer_name').val(Customer_name);
            modal.find('#deleteForm').attr('action', '{{ route("customers.index") }}/' + id);
        })
    </script>
@endsection
