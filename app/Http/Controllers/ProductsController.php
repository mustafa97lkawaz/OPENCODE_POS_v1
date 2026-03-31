<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Category;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Product_name' => 'required|string|max:999',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|unique:products,sku|max:50',
            'barcode' => 'nullable|string|unique:products,barcode|max:50',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cost_price' => 'nullable|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'reorder_point' => 'nullable|integer|min:0',
            'wac' => 'nullable|numeric|min:0',
            'stock_qty' => 'nullable|integer|min:0',
            'expire_date' => 'nullable|date',
            'alert_qty' => 'nullable|integer|min:0',
            'is_variant' => 'nullable|boolean',
            'variant_name' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'variations' => 'nullable|json',
            'max_stock' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [], [
            'Product_name' => 'اسم المنتج',
            'category_id' => 'القسم',
            'sku' => 'رمز المنتج',
            'barcode' => 'الباركود',
            'description' => 'الملاحظات',
            'photo' => 'صورة المنتج',
            'cost_price' => 'سعر التكلفة',
            'sell_price' => 'سعر البيع',
            'tax_rate' => 'نسبة الضريبة',
            'reorder_point' => 'نقطة اعادة الطلب',
            'wac' => 'متوسط السعر المرجح',
            'stock_qty' => 'الكمية',
            'expire_date' => 'تاريخ الانتهاء',
            'alert_qty' => 'كمية التنبيه',
            'is_variant' => 'له متغيرات',
            'variant_name' => 'اسم المتغير',
            'unit' => 'الوحدة',
            'variations' => 'المتغيرات',
            'max_stock' => 'الكمية القصوى',
            'is_featured' => 'منتج مميز',
            'is_active' => 'الحالة',
        ]);

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

    public function update(Request $request, $id = null)
    {
        // Handle both resource route ($id) and modal form (pro_id)
        $productId = $id ?? $request->pro_id;
        $product = Products::findOrFail($productId);

        $validated = $request->validate([
            'Product_name' => 'required|string|max:999',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $productId,
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $productId,
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cost_price' => 'nullable|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'reorder_point' => 'nullable|integer|min:0',
            'wac' => 'nullable|numeric|min:0',
            'stock_qty' => 'nullable|integer|min:0',
            'expire_date' => 'nullable|date',
            'alert_qty' => 'nullable|integer|min:0',
            'is_variant' => 'nullable|boolean',
            'variant_name' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'variations' => 'nullable|json',
            'max_stock' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [], [
            'Product_name' => 'اسم المنتج',
            'category_id' => 'القسم',
            'sku' => 'رمز المنتج',
            'barcode' => 'الباركود',
            'description' => 'الملاحظات',
            'photo' => 'صورة المنتج',
            'cost_price' => 'سعر التكلفة',
            'sell_price' => 'سعر البيع',
            'tax_rate' => 'نسبة الضريبة',
            'reorder_point' => 'نقطة اعادة الطلب',
            'wac' => 'متوسط السعر المرجح',
            'stock_qty' => 'الكمية',
            'expire_date' => 'تاريخ الانتهاء',
            'alert_qty' => 'كمية التنبيه',
            'is_variant' => 'له متغيرات',
            'variant_name' => 'اسم المتغير',
            'unit' => 'الوحدة',
            'variations' => 'المتغيرات',
            'max_stock' => 'الكمية القصوى',
            'is_featured' => 'منتج مميز',
            'is_active' => 'الحالة',
        ]);

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
        
        $product->delete();
        session()->flash('delete', 'تم حذف المنتج بنجاح');
        return back();
    }
}
