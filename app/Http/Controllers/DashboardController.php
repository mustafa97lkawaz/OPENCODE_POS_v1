<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Products;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Sales data for last 7 days
        $salesData = $this->getSalesLast7Days();
        
        // Top selling products
        $topProducts = $this->getTopSellingProducts();
        
        // Category distribution
        $categoryDistribution = $this->getCategoryDistribution();
        
        // Today's stats
        $todaySales = Sale::whereDate('created_at', today())->sum('total');
        $todayOrders = Sale::whereDate('created_at', today())->count();
        
        // Low stock alerts
        $lowStockCount = Products::where('Status', 'مفعل')
            ->whereRaw('stock_qty <= alert_qty')
            ->count();
            
        $outOfStockCount = Products::where('Status', 'مفعل')
            ->where('stock_qty', '<=', 0)
            ->count();

        return view('reports.dashboard', compact(
            'salesData',
            'topProducts',
            'categoryDistribution',
            'todaySales',
            'todayOrders',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    private function getSalesLast7Days()
    {
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            $total = Sale::whereDate('created_at', $date)->sum('total');
            $data[] = $total;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getTopSellingProducts()
    {
        return DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.Product_name',
                DB::raw('SUM(sale_items.qty) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_revenue')
            )
            ->where('sale_items.created_at', '>=', now()->subDays(30))
            ->groupBy('products.Product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();
    }

    private function getCategoryDistribution()
    {
        return DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.Category_name',
                DB::raw('COUNT(products.id) as product_count'),
                DB::raw('SUM(products.stock_qty) as total_stock')
            )
            ->where('products.Status', 'مفعل')
            ->groupBy('categories.id', 'categories.Category_name')
            ->get();
    }

    // API for AJAX refresh
    public function getSalesData()
    {
        return response()->json($this->getSalesLast7Days());
    }

    public function getTopProducts()
    {
        return response()->json($this->getTopSellingProducts());
    }

    public function getCategoryData()
    {
        return response()->json($this->getCategoryDistribution());
    }
}
