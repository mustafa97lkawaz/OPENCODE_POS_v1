<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('customers.customers', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Customer_name' => 'required',
        ], [
            'Customer_name.required' => 'يرجي ادخال اسم العميل',
        ]);

        Customer::create([
            'Customer_name'   => $request->Customer_name,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'address'        => $request->address,
            'type'           => $request->type ?? 'walk-in',
            'account_balance'=> $request->account_balance ?? 0,
            'Status'         => $request->Status ?? 'مفعل',
            'Created_by'     => Auth::user()->name,
        ]);

        session()->flash('Add', 'تم اضافة العميل بنجاح');
        return redirect()->back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'Customer_name' => 'required',
        ], [
            'Customer_name.required' => 'يرجي ادخال اسم العميل',
        ]);

        $customer = Customer::findOrFail($request->id);
        $customer->update([
            'Customer_name'   => $request->Customer_name,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'address'        => $request->address,
            'type'           => $request->type,
            'account_balance'=> $request->account_balance,
            'Status'         => $request->Status,
        ]);

        session()->flash('edit', 'تم تعديل العميل بنجاح');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        Customer::find($request->id)->delete();

        session()->flash('delete', 'تم حذف العميل بنجاح');
        return redirect()->back();
    }
}