@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الموردين</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    تفاصيل المورد</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <div class="pr-1 mb-3 mb-xl-0">
                <a href="{{ route('suppliers.index') }}" class="btn btn-warning btn-sm">
                    <i class="mdi mdi-arrow-right"></i> رجوع
                </a>
            </div>
            <div class="pr-1 mb-3 mb-xl-0">
                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-primary btn-sm">
                    <i class="mdi mdi-pencil"></i> تعديل
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

    <!-- Supplier Info Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mg-b-0">معلومات المورد</h4>
                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal">
                            <i class="mdi mdi-delete"></i> حذف
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">اسم المورد</label>
                                <p class="form-control-static font-weight-bold text-primary">{{ $supplier->Supplier_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">اسم الشركة</label>
                                <p class="form-control-static">{{ $supplier->company_name ?? 'غير محدد' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">شخص الاتصال</label>
                                <p class="form-control-static">{{ $supplier->contact_person ?? 'غير محدد' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">الهاتف</label>
                                <p class="form-control-static">{{ $supplier->phone ?? 'غير محدد' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">البريد الإلكتروني</label>
                                <p class="form-control-static">{{ $supplier->email ?? 'غير محدد' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">العنوان</label>
                                <p class="form-control-static">{{ $supplier->address ?? 'غير محدد' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">ملاحظات</label>
                                <p class="form-control-static">{{ $supplier->notes ?? 'لا توجد ملاحظات' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">الرصيد المستحق</label>
                                <p class="form-control-static font-weight-bold text-danger">
                                    {{ number_format($supplier->balance, 2) }} $
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">تاريخ الانشاء</label>
                                <p class="form-control-static">{{ $supplier->created_at->format('Y-m-d') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">اضيف بواسطة</label>
                                <p class="form-control-static">{{ $supplier->Created_by }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Actions -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">الرصيد والمعاملات</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>الرصيد المستحق</h5>
                                    <h3 class="mb-0">{{ number_format($supplier->balance, 2) }} $</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>اجمالي المشتريات</h5>
                                    <h3 class="mb-0">{{ number_format($supplier->total_purchases, 2) }} $</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>اجمالي المدفوعات</h5>
                                    <h3 class="mb-0">{{ number_format($supplier->total_payments, 2) }} $</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button class="btn btn-success btn-lg btn-block" data-toggle="modal" data-target="#addPaymentModal">
                            <i class="mdi mdi-cash-plus"></i> اضافة دفعة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Payment Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaymentModalLabel">اضافة دفعة للمورد</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('suppliers.payment', $supplier->id) }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="form-group">
                            <label>المبلغ <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="amount" required min="0.01" placeholder="ادخل المبلغ">
                        </div>
                        <div class="form-group">
                            <label>طريقة الدفع <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-control" required>
                                <option value="" selected disabled>-- اختر طريقة الدفع --</option>
                                <option value="نقدي">نقدي</option>
                                <option value="تحويل بنكي">تحويل بنكي</option>
                                <option value="شيك">شيك</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>ملاحظات</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="ملاحظات حول الدفعة"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">حفظ</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">حذف المورد</h5>
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
@endsection
@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
@endsection