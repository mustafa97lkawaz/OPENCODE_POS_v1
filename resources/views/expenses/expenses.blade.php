@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/chart.js/Chart.min.js') }}" rel="stylesheet">
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المصروفات</h4>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-block">
                <i class="fas fa-plus"></i> اضافة مصروف
            </a>
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

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card bg-primary-gradient text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-3">اجمالي المصروفات</h6>
                            <h3 class="mb-0">{{ number_format($totalExpenses, 2) }}</h3>
                        </div>
                        <div class="mt-2">
                            <i class="fas fa-wallet fa-2x opacity-7"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-success-gradient text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-3">المدفوع</h6>
                            <h3 class="mb-0">{{ number_format($paidExpenses, 2) }}</h3>
                        </div>
                        <div class="mt-2">
                            <i class="fas fa-check-circle fa-2x opacity-7"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-warning-gradient text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-3">المعلق</h6>
                            <h3 class="mb-0">{{ number_format($pendingExpenses, 2) }}</h3>
                        </div>
                        <div class="mt-2">
                            <i class="fas fa-clock fa-2x opacity-7"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h4 class="card-title">المصروفات الشهرية</h4>
                </div>
                <div class="card-body">
                    <canvas id="monthlyExpensesChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h4 class="card-title">التصنيفات</h4>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h4 class="card-title">البحث والفلترة</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.index') }}" method="get" class="form-inline">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>من تاريخ</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>الي تاريخ</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>التصنيف</label>
                                    <select name="category_id" class="form-control">
                                        <option value="">الكل</option>
                                        @foreach ($expense_categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->Category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>طريقة الدفع</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="">الكل</option>
                                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                                        <option value="bank" {{ request('payment_method') == 'bank' ? 'selected' : '' }}>تحويل بنكي</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>الحالة</label>
                                    <select name="status" class="form-control">
                                        <option value="">الكل</option>
                                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>بحث</label>
                                    <input type="text" name="search" class="form-control" placeholder="اسم او رقم المرجع" value="{{ request('search') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> الغاء
                                </a>
                                <a href="{{ route('expenses.export', request()->query()) }}" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> تصدير
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <h4 class="card-title">قائمة المصروفات</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='50'>
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">#</th>
                                    <th class="border-bottom-0">اسم المصروف</th>
                                    <th class="border-bottom-0">رقم المرجع</th>
                                    <th class="border-bottom-0">المبلغ</th>
                                    <th class="border-bottom-0">التصنيف</th>
                                    <th class="border-bottom-0">التاريخ</th>
                                    <th class="border-bottom-0">طريقة الدفع</th>
                                    <th class="border-bottom-0">الحالة</th>
                                    <th class="border-bottom-0">متكرر</th>
                                    <th class="border-bottom-0">المرفق</th>
                                    <th class="border-bottom-0">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($expenses as $expense)
                                    <?php $i++; ?>
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $expense->Expense_name }}</td>
                                        <td>{{ $expense->reference_number ?? '-' }}</td>
                                        <td>{{ number_format($expense->amount, 2) }}</td>
                                        <td>{{ $expense->category->Category_name ?? '-' }}</td>
                                        <td>{{ $expense->expense_date }}</td>
                                        <td>
                                            @if($expense->payment_method == 'cash')
                                                <span class="badge badge-success">نقدي</span>
                                            @elseif($expense->payment_method == 'card')
                                                <span class="badge badge-info">بطاقة</span>
                                            @else
                                                <span class="badge badge-primary">تحويل بنكي</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($expense->status == 'paid')
                                                <span class="badge badge-success">مدفوع</span>
                                            @else
                                                <span class="badge badge-warning">معلق</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($expense->recurring)
                                                <span class="badge badge-info">{{ $expense->recurring_type_label }}</span>
                                            @else
                                                <span class="badge badge-secondary">لا</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($expense->attachment)
                                                <a href="{{ asset('uploads/expenses/' . $expense->attachment) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-paperclip"></i>
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-sm btn-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-success" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger" data-id="{{ $expense->id }}"
                                                data-name="{{ $expense->Expense_name }}"
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
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
    <script>
        // Monthly Expenses Chart
        var monthlyData = @json($monthlyExpenses);
        var months = ['يناير', 'فبراير', 'مارس', 'ابريل', 'مايو', 'يونيو', 'يوليو', 'اغسطس', 'سبتمبر', 'اكتوبر', 'نوفمبر', 'ديسمبر'];
        var data = new Array(12).fill(0);
        
        for (var key in monthlyData) {
            data[key - 1] = monthlyData[key];
        }

        var ctx = document.getElementById('monthlyExpensesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'المصروفات الشهرية',
                    data: data,
                    borderColor: '#5b6cb8',
                    backgroundColor: 'rgba(91, 108, 184, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Category Chart
        var categoryData = @json($categoryExpenses);
        var categoryLabels = categoryData.map(function(item) {
            return item.category ? item.category.Category_name : 'غير مصنف';
        });
        var categoryValues = categoryData.map(function(item) {
            return item.total;
        });

        var ctx2 = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryValues,
                    backgroundColor: [
                        '#5b6cb8',
                        '#f59e0b',
                        '#10b981',
                        '#ef4444',
                        '#8b5cf6',
                        '#ec4899'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Delete Modal
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
