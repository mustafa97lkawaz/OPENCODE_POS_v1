<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StockAdjustment;
use App\Models\Products;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $stock_adjustments = StockAdjustment::with('product')->orderBy('id', 'desc')->get();
        $products = Products::where('Status', 'مفعل')->get();
        return view('stock_adjustments.stock_adjustments', compact('stock_adjustments', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'qty_change' => 'required|integer',
            'type' => 'required',
        ], [
            'product_id.required' => 'يرجي اختيار المنتج',
            'qty_change.required' => 'يرجي ادخال الكمية',
            'type.required' => 'يرجي اختيار نوع التعديل',
        ]);

        StockAdjustment::create([
            'product_id'  => $request->product_id,
            'qty_change'  => $request->qty_change,
            'type'        => $request->type,
            'reason'      => $request->reason,
            'Created_by'  => Auth::user()->name,
        ]);

        $product = Products::find($request->product_id);
        if ($product) {
            if (in_array($request->type, ['added', 'expired'])) {
                $product->increment('stock_qty', $request->qty_change);
            } elseif (in_array($request->type, ['damaged', 'removed'])) {
                $product->decrement('stock_qty', $request->qty_change);
            }
        }

        session()->flash('Add', 'تم اضافة تعديل المخزون بنجاح');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $adjustment = StockAdjustment::find($request->id);
        
        if ($adjustment) {
            $product = Products::find($adjustment->product_id);
            if ($product) {
                if (in_array($adjustment->type, ['added', 'expired'])) {
                    $product->decrement('stock_qty', $adjustment->qty_change);
                } elseif (in_array($adjustment->type, ['damaged', 'removed'])) {
                    $product->increment('stock_qty', $adjustment->qty_change);
                }
            }
            $adjustment->delete();
        }

        session()->flash('delete', 'تم حذف تعديل المخزون بنجاح');
        return redirect()->back();
    }
}