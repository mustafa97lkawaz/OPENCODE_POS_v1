---
name: backend
description: Laravel backend development - Migration, Model, Controller, Routes patterns for modal-based CRUD
compatibility: opencode
---

# SKILL_BACKEND.md — Laravel Backend (General)

---

## Step 1 — Artisan

```bash
php artisan make:model ModelName -mrc
```

---

## Step 2 — Migration

```php
Schema::create('table_name', function (Blueprint $table) {
    $table->bigIncrements('id');

    // Text fields → always 999 for Arabic content
    $table->string('name_field', 999);
    $table->string('code_field', 50);
    $table->text('description')->nullable();
    $table->text('note')->nullable();

    // Numbers
    $table->decimal('amount', 8, 2)->nullable();
    $table->integer('value_status');

    // Dates
    $table->date('date_field')->nullable();

    // Status
    $table->string('Status', 50)->default('مفعل');

    // Audit
    $table->string('Created_by', 999);
    $table->string('user', 300);

    // FK
    $table->unsignedBigInteger('parent_id');
    $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');

    // Only when archiving is needed
    $table->softDeletes();

    $table->timestamps();
});
```

**Column naming:**
- Arabic content → `PascalCase` e.g. `Section_name`, `Due_date`, `Amount_collection`
- FK → `snake_case` e.g. `section_id`, `parent_id`
- Audit → `Created_by`, `user`

---

## Step 3 — Model

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // only if migration has softDeletes()

class ModelName extends Model
{
    use HasFactory;
    use SoftDeletes; // remove if not needed

    protected $fillable = [
        'field_one',
        'field_two',
        'parent_id',
        // ALL writable columns
    ];

    protected $dates = ['deleted_at']; // only with SoftDeletes

    protected $casts = [
        'json_column' => 'array', // only if needed
    ];

    // BelongsTo
    public function parent()
    {
        return $this->belongsTo('App\Models\ParentModel');
    }

    // HasMany
    public function children()
    {
        return $this->hasMany('App\Models\ChildModel');
    }
}
```

---

## Step 5 — Routes (routes/web.php)

```php
use App\Http\Controllers\ModelNameController;

Route::resource('items', ModelNameController::class);

// Custom routes — add only what the module needs
Route::get('/items/ajax/{parent_id}', [ModelNameController::class, 'getChildren']);
Route::get('/items/export',           [ModelNameController::class, 'export']);
Route::get('/Print_item/{id}',        [ModelNameController::class, 'print'])->name('print_item');
Route::post('/items/status/{id}',     [ModelNameController::class, 'updateStatus'])->name('items.status');
```

---

## Step 6 — Controller

```php
<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ModelName;
use App\Models\RelatedModel;

class ModelNameController extends Controller
{
    // Optional Spatie guard:
    // public function __construct()
    // {
    //     $this->middleware('permission:عرض [الوحدة]',   ['only' => ['index']]);
    //     $this->middleware('permission:اضافة [الوحدة]', ['only' => ['store']]);
    //     $this->middleware('permission:تعديل [الوحدة]', ['only' => ['update']]);
    //     $this->middleware('permission:حذف [الوحدة]',   ['only' => ['destroy']]);
    // }

    public function index()
    {
        $items   = ModelName::all();
        $related = RelatedModel::all(); // include if view has a dropdown
        return view('module_folder.module_view', compact('items', 'related'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'field_one' => 'required|max:255',
            'field_two' => 'required|unique:table_name',
        ], [
            'field_one.required' => 'يرجي ادخال [اسم الحقل]',
            'field_two.required' => 'يرجي ادخال [اسم الحقل]',
            'field_two.unique'   => '[اسم الحقل] مسجل مسبقا',
        ]);

        ModelName::create([
            'field_one'  => $request->field_one,
            'field_two'  => $request->field_two,
            'Created_by' => Auth::user()->name,
        ]);

        session()->flash('Add', 'تم اضافة [العنصر] بنجاح');
        return redirect()->back();
    }

    public function update(Request $request)
    {
        // id comes from hidden input in the edit modal
        $item = ModelName::findOrFail($request->id);
        $item->update([
            'field_one' => $request->field_one,
            'field_two' => $request->field_two,
        ]);

        session()->flash('edit', 'تم تعديل [العنصر] بنجاح');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        // id comes from hidden input in the delete modal
        ModelName::find($request->id)->delete();

        session()->flash('delete', 'تم حذف [العنصر] بنجاح');
        return redirect()->back();
    }

    // AJAX — dynamic child dropdown
    public function getChildren($parent_id)
    {
        $children = DB::table('child_table')
            ->where('parent_id', $parent_id)
            ->pluck('display_name', 'id');
        return json_encode($children);
    }

    // Excel export
    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ModelNameExport, 'export.xlsx'
        );
    }
}
```

**Key rules:**
- `store()` → `redirect()->back()` (stays on same page, flash shown)
- `update()` → id from `$request->id` hidden input in modal
- `destroy()` → id from `$request->id` hidden input in modal
- No separate `create()` / `edit()` pages — everything is modal on the index view

---

## Flash Messages

```php
session()->flash('Add',    'تم اضافة [العنصر] بنجاح');
session()->flash('edit',   'تم تعديل [العنصر] بنجاح');
session()->flash('delete', 'تم حذف [العنصر] بنجاح');
```

---

## Artisan Reference

```bash
php artisan make:model Name -mrc
php artisan make:notification Name
php artisan make:export Name
php artisan migrate
php artisan db:seed --class=SeederName
php artisan config:clear && php artisan cache:clear
php artisan route:list
```
