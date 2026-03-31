<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $expense_categories = ExpenseCategory::all();
        return view('expense_categories.expense_categories', compact('expense_categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Category_name' => 'required',
        ], [
            'Category_name.required' => 'يرجي ادخال اسم التصنيف',
        ]);

        ExpenseCategory::create([
            'Category_name' => $request->Category_name,
            'Description'   => $request->Description,
            'Created_by'    => Auth::user()->name,
        ]);

        session()->flash('Add', 'تم اضافة تصنيف المصروفات بنجاح');
        return redirect()->back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'Category_name' => 'required',
        ], [
            'Category_name.required' => 'يرجي ادخال اسم التصنيف',
        ]);

        $category = ExpenseCategory::findOrFail($request->id);
        $category->update([
            'Category_name' => $request->Category_name,
            'Description'   => $request->Description,
        ]);

        session()->flash('edit', 'تم تعديل تصنيف المصروفات بنجاح');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        ExpenseCategory::find($request->id)->delete();

        session()->flash('delete', 'تم حذف تصنيف المصروفات بنجاح');
        return redirect()->back();
    }
}