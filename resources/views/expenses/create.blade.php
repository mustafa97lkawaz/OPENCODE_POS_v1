@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .form-section {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .form-section-title {
            font-size: 16px;
            font-weight: bold;
            color: #5b6cb8;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
    </style>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المصروفات</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ اضافة مصروف جديد</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-block">
                <i class="fas fa-arrow-right"></i>رجوع
            </a>
        </div>
    </div>
@endsection
@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
        {{ csrf_field() }}

        <div class="row">
            <!-- Basic Information -->
            <div class="col-xl-8">
                <div class="form-section">
                    <div class="form-section-title">المعلومات الاساسية</div>
                    
                    <div class="form-group">
                        <label for="Expense_name">اسم المصروف <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="Expense_name" name="Expense_name" 
                               value="{{ old('Expense_name') }}" required placeholder="ادخل اسم المصروف">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reference_number">رقم المرجع</label>
                                <input type="text" class="form-control" id="reference_number" name="reference_number" 
                                       value="{{ old('reference_number') }}" placeholder="ادخل رقم المرجع">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category_id">التصنيف <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="category_id" name="category_id" required>
                                    <option value="" selected disabled>--اختر التصنيف--</option>
                                    @foreach ($expense_categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->Category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">المبلغ <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">ريال</span>
                                    </div>
                                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                                           value="{{ old('amount') }}" required placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expense_date">تاريخ المصروف <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="expense_date" name="expense_date" 
                                       value="{{ old('expense_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">معلومات الدفع والحالة</div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_method">طريقة الدفع <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="payment_method" name="payment_method" required>
                                    <option value="" selected disabled>--اختر طريقة الدفع--</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                                    <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>تحويل بنكي</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">الحالة <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="status" name="status" required>
                                    <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">المصروفات المتكررة</div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" id="recurring" name="recurring" value="1" 
                                           {{ old('recurring') ? 'checked' : '' }}>
                                    مصروف متكرر
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6" id="recurringTypeDiv" style="{{ old('recurring') ? '' : 'display:none;' }}">
                            <div class="form-group">
                                <label for="recurring_type">نوع التكرار</label>
                                <select class="form-control select2" id="recurring_type" name="recurring_type">
                                    <option value="daily" {{ old('recurring_type') == 'daily' ? 'selected' : '' }}>يومي</option>
                                    <option value="weekly" {{ old('recurring_type') == 'weekly' ? 'selected' : '' }}>اسبوعي</option>
                                    <option value="monthly" {{ old('recurring_type') == 'monthly' ? 'selected' : '' }}>شهري</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">الوصف والملاحظات</div>
                    
                    <div class="form-group">
                        <label for="description">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="ادخل وصف المصروف">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Attachment Section -->
            <div class="col-xl-4">
                <div class="form-section">
                    <div class="form-section-title">المرفقات</div>
                    
                    <div class="form-group">
                        <label for="attachment">ارفاق ملف</label>
                        <input type="file" class="form-control" id="attachment" name="attachment" 
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                        <small class="text-muted">الملفات المسموحة: jpg, jpeg, png, pdf, doc, docx, xls, xlsx</small>
                        <small class="text-muted">الحجم الاقصى: 2 ميجابايت</small>
                    </div>
                    
                    <div id="preview" class="mt-3" style="display:none;">
                        <img id="previewImage" src="" alt="Preview" style="max-width: 100%;">
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">معلومات الانشاء</div>
                    <p class="text-muted">
                        <i class="fas fa-user"></i> انشأ بواسطة: <strong>{{ Auth::user()->name }}</strong>
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-calendar"></i> التاريخ: <strong>{{ date('Y-m-d') }}</strong>
                    </p>
                </div>

                <div class="form-section">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-save"></i> حفظ المصروف
                    </button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-block mt-2">
                        الغاء
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('js')
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "اختر",
                allowClear: true,
                width: '100%'
            });

            // Toggle recurring type visibility
            $('#recurring').change(function() {
                if ($(this).is(':checked')) {
                    $('#recurringTypeDiv').show();
                } else {
                    $('#recurringTypeDiv').hide();
                    $('#recurring_type').val('');
                }
            });

            // File preview
            $('#attachment').change(function() {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewImage').attr('src', e.target.result);
                        $('#preview').show();
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
