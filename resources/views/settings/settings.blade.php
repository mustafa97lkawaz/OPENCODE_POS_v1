@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الاعدادات</h4>
            </div>
        </div>
    </div>
@endsection
@section('content')

    @if (session()->has('Edit'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('Edit') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('settings.update', $setting->id ?? 1) }}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('patch') }}
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>اسم المحل</label>
                                    <input type="text" class="form-control" name="store_name" value="{{ $setting->store_name ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>نوع الطابعة</label>
                                    <select name="printer_type" class="form-control">
                                        <option value="80mm" {{ ($setting->printer_type ?? '80mm') == '80mm' ? 'selected' : '' }}>80mm</option>
                                        <option value="58mm" {{ ($setting->printer_type ?? '') == '58mm' ? 'selected' : '' }}>58mm</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>اسم الطابعة (USB / Windows)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="printer_name"
                                               id="printerNameInput"
                                               list="printersList"
                                               placeholder="مثال: POS58 أو EPSON TM-T88V"
                                               value="{{ $setting->printer_name ?? '' }}">
                                        <datalist id="printersList"></datalist>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" id="refreshPrintersBtn">
                                                <i class="fe fe-refresh-cw"></i> تحديث القائمة
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">اسم الطابعة كما تظهر في إعدادات Windows &gt; الطابعات</small>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button type="button" class="btn btn-outline-info w-100" id="testPrintBtn">
                                        <i class="fe fe-printer"></i> طباعة تجريبية
                                    </button>
                                    <div id="testPrintResult" class="mt-1"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>رمز العملة</label>
                                    <input type="text" class="form-control" name="currency_symbol" value="{{ $setting->currency_symbol ?? '$' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>رقم الضريبة (VAT)</label>
                                    <input type="text" class="form-control" name="vat_number" value="{{ $setting->vat_number ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>راس الفاتورة (يطبع اعلى الفاتورة)</label>
                            <textarea class="form-control" name="receipt_header" rows="3">{{ $setting->receipt_header ?? '' }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>ذيل الفاتورة (يطبع اسفل الفاتورة)</label>
                            <textarea class="form-control" name="receipt_footer" rows="3">{{ $setting->receipt_footer ?? '' }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">حفظ الاعدادات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script>
$(function () {
    $('#refreshPrintersBtn').on('click', function () {
        var btn = $(this).prop('disabled', true).text('جاري التحميل...');
        $.get('{{ route("print.printers") }}', function (res) {
            var dl = $('#printersList').empty();
            if (res.success && res.printers.length) {
                $.each(res.printers, function (i, name) {
                    dl.append($('<option>').val(name));
                });
            }
            btn.prop('disabled', false).html('<i class="fe fe-refresh-cw"></i> تحديث القائمة');
        }).fail(function () {
            btn.prop('disabled', false).html('<i class="fe fe-refresh-cw"></i> تحديث القائمة');
        });
    });

    $('#testPrintBtn').on('click', function () {
        var result = $('#testPrintResult');
        var self = $(this).prop('disabled', true);
        $.get('{{ route("print.test") }}', function (res) {
            result.html('<span class="text-' + (res.success ? 'success' : 'danger') + '">' + res.message + '</span>');
            self.prop('disabled', false);
        }).fail(function (xhr) {
            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'تعذّر الاتصال بالطابعة';
            result.html('<span class="text-danger">' + msg + '</span>');
            self.prop('disabled', false);
        });
    });
});
</script>
@endsection
