<?php

namespace App\Http\Controllers;

use App\Models\SuspendedSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuspendedSaleController extends Controller
{
    public function index()
    {
        $suspendedSales = SuspendedSale::with('customer')->latest()->get();
        return view('suspended_sales.suspended_sales', compact('suspendedSales'));
    }

    public function destroy(SuspendedSale $suspendedSale)
    {
        $suspendedSale->delete();
        return redirect()->route('suspended.index')->with('delete', 'تم حذف العملية المؤجلة بنجاح');
    }

    public function resume(SuspendedSale $suspendedSale)
    {
        // items_json is already cast to array, no need to decode
        $items = $suspendedSale->items_json;
        return redirect()->route('pos')->with([
            'resume_sale' => true,
            'suspended_id' => $suspendedSale->id,
            'suspended_items' => $items,
            'suspended_customer' => $suspendedSale->customer_id
        ]);
    }
}
