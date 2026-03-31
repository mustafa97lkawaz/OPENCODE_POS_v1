@extends('layouts.master')
@section('css')
    <style>
        .detail-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .detail-label {
            font-weight: bold;
            color: #5b6cb8;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #333;
            font-size: 15px;
        }
        .badge-large {
            font-size: 14px;
            padding: 8px 16px;
        }
    </style>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المصروفات</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تفاصيل المصروف</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-block">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>
@endsection
@section('content')

    <div class="row">
        <!-- Main Details -->
        <div class="col-xl-8">
            <div class="detail-card">
                <h4 class="mb-4" style="color: #5b6cb8; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                    <i class="fas fa-money-bill-wave"></i> معلومات المصروف
                </h4>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-label">اسم المصروف</div>
                        <div class="detail-value">{{ $expense->Expense_name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-label">رقم المرجع</div>
                        <div class="detail-value">{{ $expense->reference_number ?? '-' }}</div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="detail-label">المبلغ</div>
                        <div class="detail-value" style="font-size: 20px; font-weight: bold; color: #ef4444;">
                            {{ number_format($expense->amount, 2) }} ريال
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-label">التصنيف</div>
                        <div class="detail-value">{{ $expense->category->Category_name ?? '-' }}</div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="detail-label">تاريخ المصروف</div>
                        <div class="detail-value">{{ $expense->expense_date }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-label">تاريخ الانشاء</div>
                        <div class="detail-value">{{ $expense->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <h4 class="mb-4" style="color: #5b6cb8; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                    <i class="fas fa-credit-card"></i> معلومات الدفع
                </h4>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-label">طريقة الدفع</div>
                        <div class="detail-value">
                            @if($expense->payment_method == 'cash')
                                <span class="badge badge-success badge-large">نقدي</span>
                            @elseif($expense->payment_method == 'card')
                                <span class="badge badge-info badge-large">بطاقة</span>
                            @else
                                <span class="badge badge-primary badge-large">تحويل بنكي</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-label">الحالة</div>
                        <div class="detail-value">
                            @if($expense->status == 'paid')
                                <span class="badge badge-success badge-large">مدفوع</span>
                            @else
                                <span class="badge badge-warning badge-large">معلق</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($expense->recurring)
            <div class="detail-card">
                <h4 class="mb-4" style="color: #5b6cb8; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                    <i class="fas fa-repeat"></i> المصروف المتكرر
                </h4>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-label">متكرر</div>
                        <div class="detail-value">
                            <span class="badge badge-info badge-large">نعم</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-label">نوع التكرار</div>
                        <div class="detail-value">
                            @if($expense->recurring_type == 'daily')
                                <span class="badge badge-secondary badge-large">يومي</span>
                            @elseif($expense->recurring_type == 'weekly')
                                <span class="badge badge-secondary badge-large">اسبوعي</span>
                            @else
                                <span class="badge badge-secondary badge-large">شهري</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($expense->description)
            <div class="detail-card">
                <h4 class="mb-4" style="color: #5b6cb8; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                    <i class="fas fa-align-left"></i> الوصف والملاحظات
                </h4>
                
                <div class="detail-value">
                    {{ $expense->description }}
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <div class="detail-card">
                <h4 class="mb-4" style="color: #5b6cb8; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                    <i class="fas fa-user"></i> معلومات الانشاء
                </h4>
                
                <div class="detail-label">انشأ بواسطة</div>
                <div class="detail-value">{{ $expense->Created_by }}</div>
                
                <div class="detail-label mt-3">تاريخ الانشاء</div>
                <div class="detail-value">{{ $expense->created_at->format('Y-m-d H:i:s') }}</div>
                
                @if($expense->updated_at)
                <div class="detail-label mt-3">تاريخ التعديل</div>
                <div class="detail-value">{{ $expense->updated_at->format('Y-m-d H:i:s') }}</div>
                @endif
            </div>

            @if($expense->attachment)
            <div class="detail-card">
                <h4 class="mb-4" style="color: #5b6cb8; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                    <i class="fas fa-paperclip"></i> المرفق
                </h4>
                
                <div class="text-center">
                    @php
                        $extension = pathinfo($expense->attachment, PATHINFO_EXTENSION);
                    @endphp
                    
                    @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                        <img src="{{ asset('uploads/expenses/' . $expense->attachment) }}" 
                             alt="Attachment" class="img-fluid mb-3" style="max-width: 100%; border-radius: 8px;">
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-file fa-3x"></i>
                            <br>
                            {{ $expense->attachment }}
                        </div>
                    @endif
                    
                    <a href="{{ asset('uploads/expenses/' . $expense->attachment) }}" 
                       target="_blank" class="btn btn-primary btn-block">
                        <i class="fas fa-download"></i> تحميل المرفق
                    </a>
                </div>
            </div>
            @endif

            <div class="detail-card">
                <h4 class="mb-4" style="color: #5b6cb8; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                    <i class="fas fa-cogs"></i> العمليات
                </h4>
                
                <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-success btn-block mb-2">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                
                <button class="btn btn-danger btn-block" data-id="{{ $expense->id }}"
                    data-name="{{ $expense->Expense_name }}"
                    data-toggle="modal"
                    data-target="#modaldemo9">
                    <i class="fas fa-trash"></i> حذف
                </button>
                
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-block mt-2">
                    <i class="fas fa-arrow-right"></i> رجوع للقائمة
                </a>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="modaldemo9" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">حذف المصروف</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('expenses.destroy') }}" method="post">
                    {{ method_field('delete') }}
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <p>هل انت متاكد من عملية الحذف ؟</p><br>
                        <input type="hidden" name="id" id="id" value="">
                        <input class="form-control" name="Expense_name" id="Expense_name" type="text" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                        <button type="submit" class="btn btn-danger">تاكيد</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('#modaldemo9').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var Expense_name = button.data('name')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #Expense_name').val(Expense_name);
        })
    </script>
@endsection
