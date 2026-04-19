<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\Products;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('customer')->orderBy('id', 'desc')->get();
        $customers = Customer::where('Status', 'مفعل')->get();
        return view('sales.sales', compact('sales', 'customers'));
    }

    public function show($id)
    {
        $sale = Sale::with(['customer', 'saleItems.product'])->find($id);
        
        if (!$sale) {
            return '<div class="alert alert-danger">الفاتورة غير موجودة</div>';
        }
        
        $html = '
        <div class="invoice-details">
            <h5>رقم الفاتورة: ' . $sale->invoice_number . '</h5>
            <p>العميل: ' . ($sale->customer->Customer_name ?? 'زائر') . '</p>
            <p>التاريخ: ' . $sale->created_at . '</p>
            <p>طريقة الدفع: ';
            
        if($sale->payment_method == 'cash') $html .= 'نقدي';
        elseif($sale->payment_method == 'card') $html .= 'بطاقة';
        else $html .= 'Split';
        
        $html .= '</p>
        </div>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>الكمية</th>
                    <th>السعر</th>
                    <th>الاجمالي</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($sale->saleItems as $item) {
            $html .= '<tr>
                <td>' . ($item->product->Product_name ?? 'منتج محذوف') . '</td>
                <td>' . $item->qty . '</td>
                <td>' . number_format($item->unit_price, 2) . '</td>
                <td>' . number_format($item->total, 2) . '</td>
            </tr>';
        }
        
        $html .= '</tbody>
            <tfoot>
                <tr>
                    <th colspan="3">المجموع</th>
                    <th>' . number_format($sale->subtotal, 2) . '</th>
                </tr>
                <tr>
                    <th colspan="3">الضريبة</th>
                    <th>' . number_format($sale->tax_amount, 2) . '</th>
                </tr>
                <tr>
                    <th colspan="3">الخصم</th>
                    <th>' . number_format($sale->discount, 2) . '</th>
                </tr>
                <tr>
                    <th colspan="3">الاجمالي</th>
                    <th>' . number_format($sale->total, 2) . '</th>
                </tr>
            </tfoot>
        </table>';
        
        return $html;
    }

    public function pos()
    {
        $products = Products::with('category')->where('Status', 'مفعل')->get();
        $categories = \App\Models\Category::all();
        $customers = Customer::where('Status', 'مفعل')->get();
        return view('sales.pos', compact('products', 'categories', 'customers'));
    }

    public function posFullscreen()
    {
        $products = Products::with('category')->where('Status', 'مفعل')->get();
        $categories = \App\Models\Category::all();
        $customers = Customer::where('Status', 'مفعل')->get();
        return view('sales.pos-fullscreen', compact('products', 'categories', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
        ], [
            'payment_method.required' => 'يرجي اختيار طريقة الدفع',
        ]);

        $invoice_number = 'INV-' . date('YmdHis');

        $subtotal = $request->subtotal;
        $tax_amount = $request->tax_amount ?? 0;
        $discount = $request->discount ?? 0;
        $total = $subtotal + $tax_amount - $discount;

        $payment_method = $request->payment_method;
        $cash_amount = $request->cash_amount ?? 0;
        $card_amount = $request->card_amount ?? 0;
        $paid_amount = $cash_amount + $card_amount;
        $change_due = $paid_amount > $total ? $paid_amount - $total : 0;

        $sale = Sale::create([
            'invoice_number'  => $invoice_number,
            'customer_id'     => $request->customer_id,
            'subtotal'        => $subtotal,
            'tax_amount'      => $tax_amount,
            'discount'        => $discount,
            'total'           => $total,
            'payment_method'  => $payment_method,
            'cash_amount'     => $cash_amount,
            'card_amount'     => $card_amount,
            'paid_amount'     => $paid_amount,
            'change_due'      => $change_due,
            'Status'          => 'completed',
            'Created_by'      => Auth::user()->name,
        ]);

        $items = json_decode($request->items_json, true);
        if ($items) {
            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id'     => $sale->id,
                    'product_id'  => $item['product_id'],
                    'qty'         => $item['qty'],
                    'unit_price'  => $item['price'],
                    'total'       => $item['qty'] * $item['price'],
                ]);

                $product = Products::find($item['product_id']);
                if ($product) {
                    $product->decrement('stock_qty', $item['qty']);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'sale_id' => $sale->id]);
        }

        session()->flash('Add', 'تم اكمال البيع بنجاح');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $sale = Sale::with('saleItems')->find($request->id);
        
        if ($sale) {
            foreach ($sale->saleItems as $item) {
                $product = Products::find($item->product_id);
                if ($product) {
                    $product->increment('stock_qty', $item->qty);
                }
            }
            $sale->delete();
        }

        session()->flash('delete', 'تم حذف البيع بنجاح');
        return redirect()->back();
    }

    public function suspend(Request $request)
    {
        $invoice_number = 'SUSP-' . date('YmdHis');
        
        $items = json_decode($request->items_json, true);
        
        $total = 0;
        if ($items) {
            foreach ($items as $item) {
                $total += $item['qty'] * ($item['price'] ?? 0);
            }
        }

        DB::table('suspended_sales')->insert([
            'invoice_number' => $invoice_number,
            'customer_id'    => $request->customer_id,
            'items_json'     => $request->items_json,
            'total'         => $total,
            'Created_by'    => Auth::user()->name,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        session()->flash('Add', 'تم تعليق البيع بنجاح');
        return redirect()->back();
    }

    public function getSuspended()
    {
        $suspended = DB::table('suspended_sales')->get();
        return response()->json($suspended);
    }

    public function resumeSuspended($id)
    {
        $suspended = DB::table('suspended_sales')->where('id', $id)->first();
        return response()->json($suspended);
    }

    public function deleteSuspended($id)
    {
        DB::table('suspended_sales')->where('id', $id)->delete();
        session()->flash('delete', 'تم حذف البيع المعلق بنجاح');
        return redirect()->back();
    }

    // AJAX: Get products by category
    public function getProducts(Request $request)
    {
        $categoryId = $request->category_id;
        
        $products = Products::with('category')
            ->where('Status', 'مفعل');
        
        if ($categoryId && $categoryId !== 'all') {
            $products->where('category_id', $categoryId);
        }
        
        $products = $products->get();
        
        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }

    // AJAX: Search products
    public function searchProducts(Request $request)
    {
        $query = $request->q;
        
        $products = Products::with('category')
            ->where('Status', 'مفعل')
            ->where(function($q) use ($query) {
                $q->where('Product_name', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->get();
        
        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }

    // AJAX: Get product by barcode
    public function getProductByBarcode($barcode)
    {
        // First search in product barcode
        $product = Products::with('category', 'variations')
            ->where('Status', 'مفعل')
            ->where('barcode', $barcode)
            ->first();
        
        // If not found, search in variations barcode
        if (!$product) {
            $variation = \App\Models\ProductVariation::with('product.category', 'product.variations')
                ->where('barcode', $barcode)
                ->first();
            
            if ($variation) {
                $product = $variation->product;
                $product->selected_variation = $variation;
            }
        }
        
        if ($product) {
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'المنتج غير موجود'
        ]);
    }
}