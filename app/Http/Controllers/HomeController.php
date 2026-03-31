<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\Expense;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Products stats
        $products_count = Products::count();
        $low_stock_products = Products::whereRaw('stock_qty <= reorder_point')->count();
        
        // Customers & Suppliers
        $customers_count = Customer::count();
        $suppliers_count = Supplier::count();
        
        // Today's sales
        $today_sales = Sale::whereDate('created_at', today())->where('Status', 'completed')->sum('total');
        $today_sales_count = Sale::whereDate('created_at', today())->where('Status', 'completed')->count();
        
        // Monthly sales
        $monthly_sales = Sale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->where('Status', 'completed')->sum('total');
        
        // Expenses this month
        $monthly_expenses = Expense::whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount');
        
        // Net profit (monthly)
        $net_profit = $monthly_sales - $monthly_expenses;

        // Chart for daily sales (last 7 days)
        $chartjs = app()->chartjs
            ->name('barChartTest')
            ->type('bar')
            ->size(['width' => 100, 'height' => 50])
            ->labels(['الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت', 'الأحد'])
            ->datasets([
                [
                    "label" => "المبيعات",
                    'backgroundColor' => ['#81b214'],
                    'data' => [0, 0, 0, 0, 0, 0, $today_sales]
                ],
            ])
            ->options([]);

        $chartjs_2 = app()->chartjs
            ->name('pieChartTest')
            ->type('pie')
            ->size(['width' => 340, 'height' => 200])
            ->labels(['منتجات', 'عملاء', 'موردين'])
            ->datasets([
                [
                    'backgroundColor' => ['#81b214', '#4a9eff', '#ff9642'],
                    'data' => [$products_count, $customers_count, $suppliers_count]
                ]
            ])
            ->options([]);

        return view('home', compact(
            'chartjs', 
            'chartjs_2',
            'products_count',
            'low_stock_products',
            'customers_count',
            'suppliers_count',
            'today_sales',
            'today_sales_count',
            'monthly_sales',
            'monthly_expenses',
            'net_profit'
        ));
    }
}
