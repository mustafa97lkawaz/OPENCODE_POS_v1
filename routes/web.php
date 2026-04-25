<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PrintController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/old-home', [App\Http\Controllers\HomeController::class, 'oldDashboard'])->name('old-home');

Route::group(['middleware' => ['auth']], function() {
    // Product & Inventory
    Route::resource('products', App\Http\Controllers\ProductsController::class);
    Route::resource('categories', App\Http\Controllers\CategoryController::class);
    Route::resource('stock_adjustments', App\Http\Controllers\StockAdjustmentController::class);
    
    // People
    Route::resource('sections', App\Http\Controllers\SectionsController::class);
    Route::resource('suppliers', App\Http\Controllers\SupplierController::class);
    Route::post('suppliers/{supplier}/payment', [App\Http\Controllers\SupplierController::class, 'addPayment'])->name('suppliers.payment');
    Route::resource('customers', App\Http\Controllers\CustomerController::class);
    
    // Expenses
    Route::resource('expense_categories', App\Http\Controllers\ExpenseCategoryController::class);
    Route::resource('expenses', App\Http\Controllers\ExpenseController::class);
    Route::patch('expenses/update', [App\Http\Controllers\ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('expenses/destroy', [App\Http\Controllers\ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('expenses/export', [App\Http\Controllers\ExpenseController::class, 'export'])->name('expenses.export');
    
    // Sales & POS
    Route::resource('sales', App\Http\Controllers\SaleController::class);
    Route::get('pos', [App\Http\Controllers\SaleController::class, 'pos'])->name('pos');
    Route::get('pos/fullscreen', [App\Http\Controllers\SaleController::class, 'posFullscreen'])->name('pos.fullscreen');
    Route::post('sales/suspend', [App\Http\Controllers\SaleController::class, 'suspend'])->name('sales.suspend');
    Route::get('suspended-sales', [App\Http\Controllers\SuspendedSaleController::class, 'index'])->name('suspended.index');
    Route::delete('suspended-sales/{suspendedSale}', [App\Http\Controllers\SuspendedSaleController::class, 'destroy'])->name('suspended.destroy');
    Route::get('suspended-sales/{suspendedSale}/resume', [App\Http\Controllers\SuspendedSaleController::class, 'resume'])->name('suspended.resume');
    
    // Sales Returns
    Route::get('sales/{sale}/return', [App\Http\Controllers\SaleReturnController::class, 'create'])->name('sales.return');
    Route::post('sales/return', [App\Http\Controllers\SaleReturnController::class, 'store'])->name('sales.return.store');
    Route::get('sales-returns', [App\Http\Controllers\SaleReturnController::class, 'index'])->name('returns.index');
    Route::get('sales/{sale}/returns', [App\Http\Controllers\SaleReturnController::class, 'show'])->name('sales.returns');
    
    // POS AJAX Routes
    Route::get('pos/products', [App\Http\Controllers\SaleController::class, 'getProducts'])->name('pos.products');
    Route::get('pos/products/search', [App\Http\Controllers\SaleController::class, 'searchProducts'])->name('pos.products.search');
    Route::get('pos/products/barcode/{barcode}', [App\Http\Controllers\SaleController::class, 'getProductByBarcode'])->name('pos.products.barcode');
    
    // Print
    Route::get('print/printers',              [PrintController::class, 'getPrinters'])->name('print.printers');
    Route::get('print/test',                  [PrintController::class, 'testPrint'])->name('print.test');
    Route::get('print/receipt/{saleId}',      [PrintController::class, 'printReceipt'])->name('print.receipt');
    Route::get('print/debug/{name?}',         [PrintController::class, 'debugPrinter'])->name('print.debug');

    // Settings
    Route::resource('settings', App\Http\Controllers\SettingController::class);
    
    // Reports
    Route::post('Search_customers', App\Http\Controllers\Customers_Report::class . '@Search_customers')->name('Search_customers');
    Route::get('customers_report', App\Http\Controllers\Customers_Report::class . '@index');
    
    // Sales Report
    Route::get('reports/sales', [App\Http\Controllers\SalesReportController::class, 'index'])->name('reports.sales');
    Route::get('reports/sales/export', [App\Http\Controllers\SalesReportController::class, 'export'])->name('reports.sales.export');
    
    // Inventory Report
    Route::get('reports/inventory', [App\Http\Controllers\InventoryReportController::class, 'index'])->name('reports.inventory');
    
    // Profit Report
    Route::get('reports/profit', [App\Http\Controllers\ProfitReportController::class, 'index'])->name('reports.profit');
    Route::get('reports/profit/by-product', [App\Http\Controllers\ProfitReportController::class, 'byProduct'])->name('reports.profit.by_product');
    
    // Dashboard
    Route::get('reports/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('reports.dashboard');
    Route::get('api/dashboard/sales', [App\Http\Controllers\DashboardController::class, 'getSalesData']);
    Route::get('api/dashboard/products', [App\Http\Controllers\DashboardController::class, 'getTopProducts']);
    Route::get('api/dashboard/categories', [App\Http\Controllers\DashboardController::class, 'getCategoryData']);
    
    // Users & Roles
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::resource('users', App\Http\Controllers\UserController::class);
});
