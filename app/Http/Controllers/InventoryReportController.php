<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Category;

class InventoryReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Products::with('category');

        // Filter by category
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by stock status
        if ($request->stock_status) {
            switch ($request->stock_status) {
                case 'out_of_stock':
                    $query->where('stock_qty', '<=', 0);
                    break;
                case 'low_stock':
                    $query->where('stock_qty', '>', 0)->whereRaw('stock_qty <= alert_qty');
                    break;
                case 'in_stock':
                    $query->where('stock_qty', '>', 0)->whereRaw('stock_qty > alert_qty');
                    break;
            }
        }

        $products = $query->orderBy('stock_qty', 'asc')->get();
        $categories = Category::all();

        // Calculate statistics
        $totalProducts = $products->count();
        $outOfStock = $products->filter(function($p) { return $p->stock_qty <= 0; })->count();
        $lowStock = $products->filter(function($p) { return $p->stock_qty > 0 && $p->stock_qty <= $p->alert_qty; })->count();
        $inStock = $products->filter(function($p) { return $p->stock_qty > $p->alert_qty; })->count();
        $totalStockValue = $products->sum(function($p) { return $p->stock_qty * $p->cost_price; });

        return view('reports.inventory.index', compact(
            'products',
            'categories',
            'totalProducts',
            'outOfStock',
            'lowStock',
            'inStock',
            'totalStockValue'
        ));
    }
}
