@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/jquery-ui/ui/widgets/datepicker.css') }}" rel="stylesheet">
    <style>
        .image-preview {
            width: 150px;
            height: 150px;
            border: 2px dashed #ccc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-color: #f9f9f9;
            cursor: pointer;
            position: relative;
        }
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-preview .placeholder {
            color: #aaa;
            font-size: 14px;
            text-align: center;
        }
        .variation-row {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            position: relative;
        }
        .variation-row .remove-variation {
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .toggle-slider {
            background-color: #4caf50;
        }
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المنتجات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    تعديل بيانات المنتج</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>خطأ</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تعديل بيانات المنتج: {{ $product->Product_name }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.update', $product->id) }}" method="post" enctype="multipart/form-data" autocomplete="off">
                        {{ csrf_field() }}
                        {{ method_field('patch') }}

                        <!-- Basic Information Section -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>اسم المنتج <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="Product_name" value="{{ $product->Product_name }}" required placeholder="ادخل اسم المنتج">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>القسم <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control select2" required>
                                        <option value="" disabled>--حدد القسم--</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
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
                                    <label>رمز المنتج (SKU)</label>
                                    <input type="text" class="form-control" name="sku" value="{{ $product->sku }}" placeholder="ادخل رمز المنتج">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>الباركود</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="barcode" id="barcode" value="{{ $product->barcode }}" placeholder="ادخل الباركود">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" id="generateBarcode">
                                                <i class="fas fa-barcode"></i> توليد
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>تاريخ الانتهاء</label>
                                    <input type="date" class="form-control" name="expire_date" id="expire_date" value="{{ $product->expire_date }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>الوحدة</label>
                                    <select name="unit" class="form-control select2">
                                        <option value="قطعة" {{ $product->unit == 'قطعة' ? 'selected' : '' }}>قطعة</option>
                                        <option value="كجم" {{ $product->unit == 'كجم' ? 'selected' : '' }}>كجم</option>
                                        <option value="كيلو" {{ $product->unit == 'كيلو' ? 'selected' : '' }}>كيلو</option>
                                        <option value="متر" {{ $product->unit == 'متر' ? 'selected' : '' }}>متر</option>
                                        <option value="لتر" {{ $product->unit == 'لتر' ? 'selected' : '' }}>لتر</option>
                                        <option value="صندوق" {{ $product->unit == 'صندوق' ? 'selected' : '' }}>صندوق</option>
                                        <option value="كرتون" {{ $product->unit == 'كرتون' ? 'selected' : '' }}>كرتون</option>
                                        <option value="حبة" {{ $product->unit == 'حبة' ? 'selected' : '' }}>حبة</option>
                                        <option value="باكو" {{ $product->unit == 'باكو' ? 'selected' : '' }}>باكو</option>
                                        <option value="عبوة" {{ $product->unit == 'عبوة' ? 'selected' : '' }}>عبوة</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Section -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>سعر التكلفة</label>
                                    <input type="number" step="0.01" class="form-control" name="cost_price" value="{{ $product->cost_price }}" min="0">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>سعر البيع <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="sell_price" value="{{ $product->sell_price }}" min="0" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>نسبة الضريبة (%)</label>
                                    <input type="number" step="0.01" class="form-control" name="tax_rate" value="{{ $product->tax_rate }}" min="0" max="100">
                                </div>
                            </div>
                        </div>

                        <!-- Stock Section -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>الكمية الحالية</label>
                                    <input type="number" class="form-control" name="stock_qty" value="{{ $product->stock_qty }}" min="0">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>الكمية القصوى</label>
                                    <input type="number" class="form-control" name="max_stock" value="{{ $product->max_stock }}" min="0">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>نقطة اعادة الطلب</label>
                                    <input type="number" class="form-control" name="reorder_point" value="{{ $product->reorder_point }}" min="0">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>كمية التنبيه</label>
                                    <input type="number" class="form-control" name="alert_qty" value="{{ $product->alert_qty }}" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>متوسط السعر المرجح (WAC)</label>
                                    <input type="number" step="0.01" class="form-control" name="wac" value="{{ $product->wac }}" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Variants Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>هل المنتج له متغيرات؟</label>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="is_variant" id="is_variant" {{ $product->is_variant ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="variantSection" style="<?php echo $product->is_variant ? 'display: block;' : 'display: none;'; ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>اسم المتغير</label>
                                        <input type="text" class="form-control" name="variant_name" value="{{ $product->variant_name }}" placeholder="مثل: اللون، المقاس، الحجم">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <label>المتغيرات</label>
                                    <div id="variationsContainer">
                                        <?php if($product->variations && is_array($product->variations)): ?>
                                            <?php foreach($product->variations as $key => $variation): ?>
                                                <div class="variation-row" id="variationRow{{ $key }}">
                                                    <button type="button" class="btn btn-danger btn-sm remove-variation" onclick="removeVariation({{ $key }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>اسم المتغير</label>
                                                                <input type="text" class="form-control" name="variations[{{ $key }}][name]" value="<?php echo $variation['name'] ?? ''; ?>" placeholder="مثل: احمر, ازرق">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>سعر التكلفة</label>
                                                                <input type="number" step="0.01" class="form-control" name="variations[{{ $key }}][cost_price]" value="<?php echo $variation['cost_price'] ?? 0; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>سعر البيع</label>
                                                                <input type="number" step="0.01" class="form-control" name="variations[{{ $key }}][sell_price]" value="<?php echo $variation['sell_price'] ?? 0; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>الكمية</label>
                                                                <input type="number" class="form-control" name="variations[{{ $key }}][stock_qty]" value="<?php echo $variation['stock_qty'] ?? 0; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm" id="addVariation">
                                        <i class="fas fa-plus"></i> اضافة متغير
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Image Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>صورة المنتج</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="image-preview" onclick="document.getElementById('photo').click()">
                                                @if($product->photo)
                                                    <img id="imagePreview" src="{{ asset('uploads/products/' . $product->photo) }}">
                                                    <span class="placeholder" id="imagePlaceholder" style="display: none;">
                                                        <i class="fas fa-camera fa-2x"></i><br>
                                                        اضغط لرفع صورة
                                                    </span>
                                                @else
                                                    <img id="imagePreview" src="" style="display: none;">
                                                    <span class="placeholder" id="imagePlaceholder">
                                                        <i class="fas fa-camera fa-2x"></i><br>
                                                        اضغط لرفع صورة
                                                    </span>
                                                @endif
                                            </div>
                                            <input type="file" name="photo" id="photo" accept="image/*" style="display: none;" onchange="previewImage(event)">
                                            <small class="text-muted">اتركها فارغةاذا لاتريد تغيير الصورة</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>الملاحظات</label>
                                    <textarea class="form-control" name="description" rows="4" placeholder="ملاحظات اضافية">{{ $product->description }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Status Toggles -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>منتج مميز</label>
                                    <br>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="is_featured" id="is_featured" {{ $product->is_featured ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>الحالة</label>
                                    <br>
                                    <label class="toggle-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ $product->is_active ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 20px; margin-bottom: 50px;">
                            <button type="submit" class="btn btn-primary btn-lg">تعديل البيانات</button>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-lg">الغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            // Initialize Datepicker
            $('#expire_date').datepicker({
                rtl: true,
                language: 'ar',
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            // Toggle variant section
            $('#is_variant').change(function() {
                if ($(this).is(':checked')) {
                    $('#variantSection').slideDown();
                } else {
                    $('#variantSection').slideUp();
                }
            });

            // Generate barcode
            $('#generateBarcode').click(function() {
                var barcode = generateRandomBarcode();
                $('#barcode').val(barcode);
            });

            function generateRandomBarcode() {
                return '62' + Math.floor(Math.random() * 10000000000);
            }

            // Add variation row
            var rowCount = $('#variationsContainer .variation-row').length;
            
            $('#addVariation').click(function() {
                var newRowId = rowCount;
                var newRow = `
                    <div class="variation-row" id="variationRow${newRowId}">
                        <button type="button" class="btn btn-danger btn-sm remove-variation" onclick="removeVariation(${newRowId})">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>اسم المتغير</label>
                                    <input type="text" class="form-control" name="variations[${newRowId}][name]" placeholder="مثل: احمر, ازرق">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>سعر التكلفة</label>
                                    <input type="number" step="0.01" class="form-control" name="variations[${newRowId}][cost_price]" value="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>سعر البيع</label>
                                    <input type="number" step="0.01" class="form-control" name="variations[${newRowId}][sell_price]" value="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>الكمية</label>
                                    <input type="number" class="form-control" name="variations[${newRowId}][stock_qty]" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#variationsContainer').append(newRow);
                rowCount++;
            });

            // Make removeVariation function global
            window.removeVariation = function(rowId) {
                $('#variationRow' + rowId).remove();
            };
        });

        // Image preview function
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('imagePreview');
                var placeholder = document.getElementById('imagePlaceholder');
                output.src = reader.result;
                output.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        // Click on image preview to trigger file input
        $('.image-preview').click(function() {
            document.getElementById('photo').click();
        });
    </script>
@endsection
