<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\Products;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with('customer')->orderBy('id', 'desc');

        // Date range filter
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date . ' 23:59:59']);
        } elseif ($request->start_date) {
            $query->where('created_at', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        // Payment method filter
        if ($request->payment_method && $request->payment_method != 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        // Customer filter
        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        $sales = $query->get();
        $customers = Customer::where('Status', 'مفعل')->get();

        // Calculate totals
        $totalSales = $sales->sum('total');
        $totalTax = $sales->sum('tax_amount');
        $totalDiscount = $sales->sum('discount');
        
        // Calculate profit (need to calculate cost)
        $totalProfit = 0;
        foreach ($sales as $sale) {
            foreach ($sale->saleItems as $item) {
                $product = Products::find($item->product_id);
                if ($product) {
                    $cost = $product->cost_price ?? 0;
                    $totalProfit += ($item->unit_price - $cost) * $item->qty;
                }
            }
        }

        return view('reports.sales.index', compact(
            'sales', 
            'customers', 
            'totalSales', 
            'totalTax', 
            'totalDiscount',
            'totalProfit'
        ));
    }

    public function export(Request $request)
    {
        $query = Sale::with('customer');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date . ' 23:59:59']);
        }

        if ($request->payment_method && $request->payment_method != 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        $sales = $query->orderBy('id', 'desc')->get();

        return Excel::download(new SalesReportExport($sales), 'sales_report_' . date('YmdHis') . '.xlsx');
    }
}
