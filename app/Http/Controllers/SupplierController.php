<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('id', 'desc')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Supplier_name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ], [
            'Supplier_name.required' => 'يرجي ادخال اسم المورد',
            'email.email' => 'يرجي ادخال بريد الكتروني صحيح',
        ]);

        Supplier::create([
            'Supplier_name' => $request->Supplier_name,
            'company_name'  => $request->company_name,
            'contact_person'=> $request->contact_person,
            'phone'         => $request->phone,
            'email'         => $request->email,
            'address'       => $request->address,
            'balance'       => $request->balance ?? 0,
            'notes'         => $request->notes,
            'Created_by'    => Auth::user()->name,
        ]);

        session()->flash('Add', 'تم اضافة المورد بنجاح');
        return redirect()->route('suppliers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'Supplier_name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ], [
            'Supplier_name.required' => 'يرجي ادخال اسم المورد',
            'email.email' => 'يرجي ادخال بريد الكتروني صحيح',
        ]);

        $supplier->update([
            'Supplier_name' => $request->Supplier_name,
            'company_name'  => $request->company_name,
            'contact_person'=> $request->contact_person,
            'phone'         => $request->phone,
            'email'         => $request->email,
            'address'       => $request->address,
            'balance'       => $request->balance ?? 0,
            'notes'         => $request->notes,
        ]);

        session()->flash('edit', 'تم تعديل المورد بنجاح');
        return redirect()->route('suppliers.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        session()->flash('delete', 'تم حذف المورد بنجاح');
        return redirect()->route('suppliers.index');
    }

    /**
     * Add payment to supplier.
     */
    public function addPayment(Request $request, Supplier $supplier)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required',
            'notes' => 'nullable',
        ], [
            'amount.required' => 'يرجي ادخال المبلغ',
            'amount.min' => 'المبلغ يجب ان يكون اكبر من صفر',
            'payment_method.required' => 'يرجي اختيار طريقة الدفع',
        ]);

        // Update supplier balance
        $supplier->update([
            'balance' => $supplier->balance - $request->amount
        ]);

        session()->flash('Add', 'تم اضافة الدفعة بنجاح');
        return redirect()->back();
    }
}