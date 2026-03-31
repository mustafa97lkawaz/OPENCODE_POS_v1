<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Products;
use Illuminate\Support\Facades\DB;

class ProfitReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'saleItems.product']);

        // Date range filter
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date . ' 23:59:59']);
        } elseif ($request->start_date) {
            $query->where('created_at', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $sales = $query->orderBy('id', 'desc')->get();

        // Calculate profit per sale
        $salesWithProfit = $sales->map(function ($sale) {
            $profit = 0;
            foreach ($sale->saleItems as $item) {
                $product = Products::find($item->product_id);
                if ($product) {
                    $cost = $product->cost_price ?? 0;
                    $profit += ($item->unit_price - $cost) * $item->qty;
                }
            }
            $sale->profit = $profit;
            return $sale;
        });

        $totalRevenue = $sales->sum('total');
        $totalCost = 0;
        $totalProfit = 0;

        foreach ($sales as $sale) {
            foreach ($sale->saleItems as $item) {
                $product = Products::find($item->product_id);
                if ($product) {
                    $cost = $product->cost_price ?? 0;
                    $totalCost += $cost * $item->qty;
                    $totalProfit += ($item->unit_price - $cost) * $item->qty;
                }
            }
        }

        // Group by date for chart
        $dailyProfit = $salesWithProfit->groupBy(function($sale) {
            return $sale->created_at->format('Y-m-d');
        })->map(function($daySales) {
            return $daySales->sum('profit');
        });

        // Product profit analysis
        $productProfits = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.Product_name',
                DB::raw('SUM(sale_items.qty) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_revenue'),
                DB::raw('SUM(sale_items.qty * products.cost_price) as total_cost'),
                DB::raw('SUM(sale_items.total - (sale_items.qty * products.cost_price)) as total_profit')
            )
            ->when($request->start_date, function($q) use ($request) {
                return $q->where('sale_items.created_at', '>=', $request->start_date);
            })
            ->when($request->end_date, function($q) use ($request) {
                return $q->where('sale_items.created_at', '<=', $request->end_date . ' 23:59:59');
            })
            ->groupBy('products.id', 'products.Product_name')
            ->orderByDesc('total_profit')
            ->limit(10)
            ->get();

        return view('reports.profit.index', compact(
            'salesWithProfit',
            'totalRevenue',
            'totalCost',
            'totalProfit',
            'dailyProfit',
            'productProfits'
        ));
    }

    public function byProduct(Request $request)
    {
        $query = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.Product_name',
                DB::raw('SUM(sale_items.qty) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_revenue'),
                DB::raw('SUM(sale_items.qty * products.cost_price) as total_cost'),
                DB::raw('SUM(sale_items.total - (sale_items.qty * products.cost_price)) as total_profit')
            );

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('sale_items.created_at', [$request->start_date, $request->end_date . ' 23:59:59']);
        }

        $productProfits = $query->groupBy('products.id', 'products.Product_name')
            ->orderByDesc('total_profit')
            ->get();

        $totalProfit = $productProfits->sum('total_profit');

        return view('reports.profit.by_product', compact('productProfits', 'totalProfit'));
    }
}
