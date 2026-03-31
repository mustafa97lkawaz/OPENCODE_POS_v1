---
name: frontend
description: Valexa Dashboard Blade template - single file with Table + Add Modal + Edit Modal + Delete Modal
compatibility: opencode
---

# SKILL_FRONTEND.md — Valexa Dashboard Blade (General)

---

## My View Structure Rule

**Every module = ONE Blade file** inside its own folder.  
That single file contains: **Table + Add Modal + Edit Modal + Delete Modal**

```
resources/views/
└── {ModuleName}/
    └── {ModuleName}.blade.php     ← everything here
```

Example for "categories":
```
resources/views/categories/
└── categories.blade.php
```

---

## Complete CRUD View Template

Copy this exactly, replace `[Module]` placeholders:

```blade
@extends('layouts.master')
@section('css')
    <!-- Internal Data table css -->
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@section('title')
[اسم الصفحة بالعربي]
@stop
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">[القسم الرئيسي]</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ [اسم الصفحة]</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')

    {{-- ===== Flash Messages ===== --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session()->has('Add'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('Add') }}</strong>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if(session()->has('edit'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('edit') }}</strong>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if(session()->has('delete'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('delete') }}</strong>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <!-- row -->
    <div class="row">

        {{-- ===== Table Card ===== --}}
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <a class="modal-effect btn btn-outline-primary btn-block"
                           data-effect="effect-scale" data-toggle="modal" href="#addModal">
                           اضافة [اسم العنصر]
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table key-buttons text-md-nowrap">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">#</th>
                                    <th class="border-bottom-0">[عمود 1]</th>
                                    <th class="border-bottom-0">[عمود 2]</th>
                                    <th class="border-bottom-0">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach($items as $item)
                                    <?php $i++; ?>
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $item->field_one }}</td>
                                        <td>{{ $item->field_two }}</td>
                                        <td>
                                            {{-- Edit trigger --}}
                                            <a class="modal-effect btn btn-sm btn-info"
                                               data-effect="effect-scale"
                                               data-id="{{ $item->id }}"
                                               data-field_one="{{ $item->field_one }}"
                                               data-field_two="{{ $item->field_two }}"
                                               data-toggle="modal" href="#editModal"
                                               title="تعديل">
                                               <i class="las la-pen"></i>
                                            </a>

                                            {{-- Delete trigger --}}
                                            <a class="modal-effect btn btn-sm btn-danger"
                                               data-effect="effect-scale"
                                               data-id="{{ $item->id }}"
                                               data-field_one="{{ $item->field_one }}"
                                               data-toggle="modal" href="#deleteModal"
                                               title="حذف">
                                               <i class="las la-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        {{-- ===== ADD MODAL ===== --}}
        <div class="modal" id="addModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">اضافة [اسم العنصر]</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('items.store') }}" method="post">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label>[تسمية الحقل الأول]</label>
                                <input type="text" class="form-control" name="field_one" required>
                            </div>

                            <div class="form-group">
                                <label>[تسمية الحقل الثاني]</label>
                                <textarea class="form-control" name="field_two" rows="3"></textarea>
                            </div>

                            {{-- If there's a parent dropdown: --}}
                            {{-- <div class="form-group">
                                <label>القسم</label>
                                <select name="parent_id" class="form-control" required>
                                    <option value="" disabled selected>-- حدد --</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">تاكيد</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- ===== END ADD MODAL ===== --}}


        {{-- ===== EDIT MODAL ===== --}}
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تعديل [اسم العنصر]</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('items/update') }}" method="post" autocomplete="off">
                        {{ method_field('patch') }}
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <input type="hidden" name="id" id="edit_id">

                            <div class="form-group">
                                <label>[تسمية الحقل الأول]</label>
                                <input type="text" class="form-control" name="field_one" id="edit_field_one">
                            </div>

                            <div class="form-group">
                                <label>[تسمية الحقل الثاني]</label>
                                <textarea class="form-control" name="field_two" id="edit_field_two"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">تاكيد</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- ===== END EDIT MODAL ===== --}}


        {{-- ===== DELETE MODAL ===== --}}
        <div class="modal" id="deleteModal">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">حذف [اسم العنصر]</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('items/destroy') }}" method="post">
                        {{ method_field('delete') }}
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <p>هل انت متاكد من عملية الحذف ؟</p><br>
                            <input type="hidden" name="id" id="delete_id">
                            <input class="form-control" name="field_one" id="delete_field_one" type="text" readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                            <button type="submit" class="btn btn-danger">تاكيد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- ===== END DELETE MODAL ===== --}}

    </div>
    <!-- row closed -->
@endsection

@section('js')
    <!-- Internal Data tables -->
    <script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/jszip.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/pdfmake.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/vfs_fonts.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/buttons.html5.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/buttons.print.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js')}}"></script>
    <!--Internal  Datatable js -->
    <script src="{{URL::asset('assets/js/table-data.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>

    {{-- Edit modal: populate fields from data-* attributes --}}
    <script>
        $('#editModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal  = $(this);
            modal.find('#edit_id').val(button.data('id'));
            modal.find('#edit_field_one').val(button.data('field_one'));
            modal.find('#edit_field_two').val(button.data('field_two'));
            // add more fields as needed
        });
    </script>

    {{-- Delete modal: populate id and name --}}
    <script>
        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal  = $(this);
            modal.find('#delete_id').val(button.data('id'));
            modal.find('#delete_field_one').val(button.data('field_one'));
        });
    </script>

@endsection
```

---

## Rules When Adapting the Template

| What to change | How |
|---------------|-----|
| `[اسم العنصر]` | Replace with actual Arabic name e.g. `تصنيف` |
| `[القسم الرئيسي]` | Section in breadcrumb e.g. `الاعدادات` |
| `[اسم الصفحة]` | Page name e.g. `التصنيفات` |
| `items` in routes | Replace with actual resource name e.g. `categories` |
| `field_one`, `field_two` | Replace with actual column names |
| `data-*` attributes on edit/delete buttons | Must match exactly the `$item->column` values |
| `modal.find('#edit_field_one')` | Must match the `id` of the input inside the modal |
| Add more `<form-group>` blocks | For each additional column |
| Uncomment dropdown block | When module has a parent relationship |

---

## If Module Has Parent Dropdown (e.g. products → sections)

Add in **Add Modal**:
```blade
<div class="form-group">
    <label>القسم</label>
    <select name="parent_id" class="form-control" required>
        <option value="" disabled selected>-- حدد --</option>
        @foreach($parents as $parent)
            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
        @endforeach
    </select>
</div>
```

Add `data-parent_name="{{ $item->parent->name }}"` on edit/delete buttons.

In edit modal select:
```blade
<select name="parent_name" class="form-control" id="edit_parent_name">
    @foreach($parents as $parent)
        <option>{{ $parent->name }}</option>
    @endforeach
</select>
```

In edit JS:
```js
modal.find('#edit_parent_name').val(button.data('parent_name'));
```

---

## If Module Has AJAX Dependent Dropdown

Add after the parent select in Add Modal:
```blade
<select name="child_id" class="form-control" id="child_select">
    <option value="" disabled selected>-- حدد --</option>
</select>
```

Add in `@section('js')` after other scripts:
```js
$('#parent_select').change(function() {
    var parent_id = $(this).val();
    $.ajax({
        url: '/ajax/children/' + parent_id,
        type: 'GET',
        success: function(data) {
            var items = JSON.parse(data);
            $('#child_select').empty();
            $('#child_select').append('<option value="" disabled selected>-- حدد --</option>');
            $.each(items, function(key, value) {
                $('#child_select').append('<option value="' + value + '">' + value + '</option>');
            });
        }
    });
});
```

---

## File Path Output Format

Always state:
```
File: resources/views/{module_name}/{module_name}.blade.php
Controller must pass: compact('items')   ← or compact('items', 'parents') if dropdown exists
```
