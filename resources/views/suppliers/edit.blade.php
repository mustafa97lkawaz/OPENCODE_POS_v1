@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الموردين</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    تعديل بيانات المورد</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تعديل بيانات المورد: {{ $supplier->Supplier_name }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('suppliers.update', $supplier->id) }}" method="post" autocomplete="off">
                        {{ csrf_field() }}
                        {{ method_field('patch') }}
                        
                        <div class="row">
                            <!-- Basic Info -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>اسم المورد <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="Supplier_name" value="{{ $supplier->Supplier_name }}" required placeholder="ادخل اسم المورد">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>اسم الشركة</label>
                                    <input type="text" class="form-control" name="company_name" value="{{ $supplier->company_name }}" placeholder="ادخل اسم الشركة">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>شخص الاتصال</label>
                                    <input type="text" class="form-control" name="contact_person" value="{{ $supplier->contact_person }}" placeholder="ادخل اسم شخص الاتصال">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>الهاتف</label>
                                    <input type="text" class="form-control" name="phone" value="{{ $supplier->phone }}" placeholder="ادخل رقم الهاتف">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>البريد الإلكتروني</label>
                                    <input type="email" class="form-control" name="email" value="{{ $supplier->email }}" placeholder="ادخل البريد الإلكتروني">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>الرصيد المستحق</label>
                                    <input type="number" step="0.01" class="form-control" name="balance" value="{{ $supplier->balance }}" min="0" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>العنوان</label>
                                    <textarea class="form-control" name="address" rows="3" placeholder="ادخل العنوان">{{ $supplier->address }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>ملاحظات</label>
                                    <textarea class="form-control" name="notes" rows="4" placeholder="ملاحظات اضافية">{{ $supplier->notes }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 20px; margin-bottom: 50px;">
                            <button type="submit" class="btn btn-primary btn-lg">تعديل البيانات</button>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary btn-lg">الغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection