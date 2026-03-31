@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الموردين</h4>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <div class="pr-1 mb-3 mb-xl-0">
                <a href="{{ route('suppliers.create') }}" class="btn btn-success btn-sm">
                    <i class="mdi mdi-plus"></i> اضافة مورد
                </a>
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
                        <h4 class="card-title mg-b-0">قائمة الموردين</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='50'>
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">#</th>
                                    <th class="border-bottom-0">اسم المورد</th>
                                    <th class="border-bottom-0">اسم الشركة</th>
                                    <th class="border-bottom-0">شخص الاتصال</th>
                                    <th class="border-bottom-0">الهاتف</th>
                                    <th class="border-bottom-0">البريد</th>
                                    <th class="border-bottom-0">الرصيد</th>
                                    <th class="border-bottom-0">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($suppliers as $supplier)
                                    <?php $i++; ?>
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>
                                            <a href="{{ route('suppliers.show', $supplier->id) }}" class="text-primary font-weight-bold">
                                                {{ $supplier->Supplier_name }}
                                            </a>
                                        </td>
                                        <td>{{ $supplier->company_name ?? '-' }}</td>
                                        <td>{{ $supplier->contact_person ?? '-' }}</td>
                                        <td>{{ $supplier->phone ?? '-' }}</td>
                                        <td>{{ $supplier->email ?? '-' }}</td>
                                        <td>
                                            @if($supplier->balance > 0)
                                                <span class="text-danger font-weight-bold">{{ number_format($supplier->balance, 2) }}</span>
                                            @else
                                                <span class="text-success">0.00</span>
                                            @endif
                                            $
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('suppliers.show', $supplier->id) }}" class="btn btn-info btn-sm" title="تفاصيل">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-success btn-sm" title="تعديل">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button class="btn btn-danger btn-sm" data-toggle="modal" 
                                                    data-target="#deleteModal{{ $supplier->id }}" title="حذف">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $supplier->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $supplier->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $supplier->id }}">حذف المورد</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="post">
                                                    {{ csrf_field() }}
                                                    {{ method_field('delete') }}
                                                    <div class="modal-body">
                                                        <p>هل انت متاكد من حذف المورد: <strong>{{ $supplier->Supplier_name }}</strong>؟</p>
                                                        <p class="text-danger">لا يمكن التراجع عن هذا الاجراء</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                                                        <button type="submit" class="btn btn-danger">تاكيد الحذف</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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