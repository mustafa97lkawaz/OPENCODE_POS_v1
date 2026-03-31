<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Products;
use App\Models\SaleReturn;

class SaleReturnController extends Controller
{
    /**
     * Display a listing of all returns
     */
    public function index()
    {
        $returns = SaleReturn::with(['sale', 'product'])
            ->orderBy('id', 'desc')
            ->get();
        return view('sales.returns', compact('returns'));
    }

    /**
     * Show the form for creating a return for a specific sale
     */
    public function create($sale_id)
    {
        $sale = Sale::with(['customer', 'saleItems.product'])->find($sale_id);
        
        if (!$sale) {
            session()->flash('delete', 'الفاتورة غير موجودة');
            return redirect()->route('sales.index');
        }

        return view('sales.return_form', compact('sale'));
    }

    /**
     * Store a newly created return in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'items' => 'required|array|min:1',
        ], [
            'sale_id.required' => 'يرجي تحديد الفاتورة',
            'items.required' => 'يرجي تحديد منتجات للمرتجع',
        ]);

        $sale_id = $request->sale_id;
        $items = $request->items;
        $reason = $request->reason;

        foreach ($items as $item) {
            if (isset($item['return_qty']) && $item['return_qty'] > 0) {
                $return_qty = min((int)$item['return_qty'], (int)$item['sold_qty']);
                
                if ($return_qty > 0) {
                    // Create return record
                    SaleReturn::create([
                        'sale_id' => $sale_id,
                        'product_id' => $item['product_id'],
                        'qty' => $return_qty,
                        'unit_price' => $item['unit_price'],
                        'total' => $return_qty * $item['unit_price'],
                        'reason' => $reason,
                        'Created_by' => Auth::user()->name,
                    ]);

                    // Update product stock
                    $product = Products::find($item['product_id']);
                    if ($product) {
                        $product->increment('stock_qty', $return_qty);
                    }
                }
            }
        }

        session()->flash('Add', 'تم معالجة المرتجع بنجاح');
        return redirect()->route('sales.index');
    }

    /**
     * Display returns for a specific sale
     */
    public function show($sale_id)
    {
        $returns = SaleReturn::with('product')
            ->where('sale_id', $sale_id)
            ->get();
        
        $sale = Sale::find($sale_id);
        
        return view('sales.sale_returns', compact('returns', 'sale'));
    }
}
