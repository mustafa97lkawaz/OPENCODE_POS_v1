<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use Illuminate\Support\Facades\File;

class ProductsController extends Controller
{
    public function index()
    {
        $categories = Category::where('Status', 'مفعل')->get();
        $products = Products::with('category')->get();
        return view('products.Products', compact('categories', 'products'));
    }

    public function create()
    {
        $categories = Category::where('Status', 'مفعل')->get();
        return view('products.create', compact('categories'));
    }

    public function edit($id)
    {
        $product = Products::findOrFail($id);
        $categories = Category::where('Status', 'مفعل')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        $validated['Created_by'] = auth()->user()->name ?? 'System';
        $validated['Status'] = $request->is_active == false ? 'غير مفعل' : 'مفعل';
        $validated['cost_price'] = $request->cost_price ?? 0;
        $validated['sell_price'] = $request->sell_price ?? 0;
        $validated['tax_rate'] = $request->tax_rate ?? 0;
        $validated['reorder_point'] = $request->reorder_point ?? 10;
        $validated['wac'] = $request->wac ?? 0;
        $validated['stock_qty'] = $request->stock_qty ?? 0;
        $validated['alert_qty'] = $request->alert_qty ?? 10;
        $validated['unit'] = $request->unit ?? 'قطعة';
        $validated['is_variant'] = $request->is_variant ?? false;
        $validated['is_featured'] = $request->is_featured ?? false;
        $validated['is_active'] = $request->is_active ?? true;

        // Handle variations JSON
        if ($request->has('variations') && $request->variations) {
            $validated['variations'] = $request->variations;
        }

        // Handle image upload
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $name = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/products'), $name);
            $validated['photo'] = $name;
        }

        Products::create($validated);

        session()->flash('Add', 'تم اضافة المنتج بنجاح');
        return redirect('/products');
    }

    public function update(UpdateProductRequest $request, $id = null)
    {
        // Handle both resource route ($id) and modal form (pro_id)
        $productId = $id ?? $request->pro_id;
        $product = Products::findOrFail($productId);

        $validated = $request->validated();

        $validated['cost_price'] = $request->cost_price ?? 0;
        $validated['sell_price'] = $request->sell_price ?? 0;
        $validated['tax_rate'] = $request->tax_rate ?? 0;
        $validated['reorder_point'] = $request->reorder_point ?? 10;
        $validated['wac'] = $request->wac ?? 0;
        $validated['alert_qty'] = $request->alert_qty ?? 10;
        $validated['unit'] = $request->unit ?? 'قطعة';
        $validated['is_variant'] = $request->is_variant ?? false;
        $validated['is_featured'] = $request->is_featured ?? false;
        $validated['is_active'] = $request->is_active ?? true;
        $validated['Status'] = $request->is_active == false ? 'غير مفعل' : 'مفعل';

        // Handle variations JSON
        if ($request->has('variations') && $request->variations) {
            $validated['variations'] = $request->variations;
        } else {
            $validated['variations'] = null;
        }

        // Handle image upload
        if ($request->hasFile('photo')) {
            // Delete old image if exists
            if ($product->photo && File::exists(public_path('uploads/products/' . $product->photo))) {
                File::delete(public_path('uploads/products/' . $product->photo));
            }
            
            $image = $request->file('photo');
            $name = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/products'), $name);
            $validated['photo'] = $name;
        }

        $product->update($validated);

        session()->flash('Edit', 'تم تعديل المنتج بنجاح');
        return redirect('/products');
    }

    public function destroy(Request $request, $id = null)
    {
        // Handle both resource route ($id) and modal form (pro_id)
        $productId = $id ?? $request->pro_id;
        $product = Products::findOrFail($productId);
        
        // Delete image if exists
        if ($product->photo && File::exists(public_path('uploads/products/' . $product->photo))) {
            File::delete(public_path('uploads/products/' . $product->photo));
        }
        
        try {
            $product->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            // FK restrict: product has sales history — block the delete.
            session()->flash('error', 'لا يمكن حذف منتج له مبيعات سابقة');
            return back();
        }

        session()->flash('delete', 'تم حذف المنتج بنجاح');
        return back();
    }
}
