<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Category_name' => 'required',
        ], [
            'Category_name.required' => 'يرجي ادخال اسم التصنيف',
        ]);

        Category::create([
            'Category_name' => $request->Category_name,
            'Description'   => $request->Description,
            'Status'        => $request->Status ?? 'مفعل',
            'Created_by'    => Auth::user()->name,
        ]);

        session()->flash('Add', 'تم اضافة التصنيف بنجاح');
        return redirect()->back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'Category_name' => 'required',
        ], [
            'Category_name.required' => 'يرجي ادخال اسم التصنيف',
        ]);

        $category = Category::findOrFail($request->id);
        $category->update([
            'Category_name' => $request->Category_name,
            'Description'   => $request->Description,
            'Status'        => $request->Status,
        ]);

        session()->flash('edit', 'تم تعديل التصنيف بنجاح');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        Category::find($request->id)->delete();

        session()->flash('delete', 'تم حذف التصنيف بنجاح');
        return redirect()->back();
    }
}