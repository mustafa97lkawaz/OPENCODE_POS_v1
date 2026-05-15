<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Expenses\StoreExpenseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpensesExport;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('category');

        // Date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->where('expense_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('expense_date', '<=', $request->end_date);
        }

        // Category filter
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Payment method filter
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('Expense_name', 'like', '%' . $request->search . '%')
                  ->orWhere('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $expense_categories = ExpenseCategory::all();

        // Calculate summaries
        $totalExpenses = $expenses->sum('amount');
        $paidExpenses = $expenses->where('status', 'paid')->sum('amount');
        $pendingExpenses = $expenses->where('status', 'pending')->sum('amount');

        // Driver-portable date helpers (SQLite vs MySQL)
        $driver  = DB::connection()->getDriverName();
        $monthFn = $driver === 'sqlite' ? "CAST(strftime('%m', expense_date) AS INTEGER)" : 'MONTH(expense_date)';
        $weekFn  = $driver === 'sqlite' ? "CAST(strftime('%W', expense_date) AS INTEGER)" : 'WEEK(expense_date)';

        // Monthly summary for the current year
        $monthlyExpenses = Expense::select(
            DB::raw("$monthFn as month"),
            DB::raw('SUM(amount) as total')
        )
        ->whereYear('expense_date', date('Y'))
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

        // Weekly summary for the current month
        $weeklyExpenses = Expense::select(
            DB::raw("$weekFn as week"),
            DB::raw('SUM(amount) as total')
        )
        ->whereMonth('expense_date', date('m'))
        ->whereYear('expense_date', date('Y'))
        ->groupBy('week')
        ->pluck('total', 'week')
        ->toArray();

        // Category distribution
        $categoryExpenses = Expense::select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        return view('expenses.expenses', compact(
            'expenses', 
            'expense_categories',
            'totalExpenses',
            'paidExpenses',
            'pendingExpenses',
            'monthlyExpenses',
            'weeklyExpenses',
            'categoryExpenses'
        ));
    }

    public function create()
    {
        $expense_categories = ExpenseCategory::all();
        return view('expenses.create', compact('expense_categories'));
    }

    public function store(StoreExpenseRequest $request)
    {

        $data = $request->except(['attachment', '_token']);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/expenses'), $filename);
            $data['attachment'] = $filename;
        }

        $data['recurring'] = $request->has('recurring') ? true : false;
        $data['Created_by'] = Auth::user()->name;

        Expense::create($data);

        session()->flash('Add', 'تم اضافة المصروف بنجاح');
        return redirect()->route('expenses.index');
    }

    public function show($id)
    {
        $expense = Expense::with('category')->findOrFail($id);
        return view('expenses.show', compact('expense'));
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $expense_categories = ExpenseCategory::all();
        return view('expenses.edit', compact('expense', 'expense_categories'));
    }

    public function update(StoreExpenseRequest $request, $id)
    {

        $expense = Expense::findOrFail($id);
        $data = $request->except(['attachment', '_token', '_method']);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($expense->attachment && file_exists(public_path('uploads/expenses/' . $expense->attachment))) {
                File::delete(public_path('uploads/expenses/' . $expense->attachment));
            }
            
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/expenses'), $filename);
            $data['attachment'] = $filename;
        }

        $data['recurring'] = $request->has('recurring') ? true : false;

        $expense->update($data);

        session()->flash('edit', 'تم تعديل المصروف بنجاح');
        return redirect()->route('expenses.index');
    }

    public function destroy(Request $request)
    {
        $expense = Expense::findOrFail($request->id);
        
        // Delete attachment if exists
        if ($expense->attachment && file_exists(public_path('uploads/expenses/' . $expense->attachment))) {
            File::delete(public_path('uploads/expenses/' . $expense->attachment));
        }
        
        $expense->delete();

        session()->flash('delete', 'تم حذف المصروف بنجاح');
        return redirect()->back();
    }

    public function export(Request $request)
    {
        $query = Expense::with('category');

        // Apply same filters as index
        if ($request->has('start_date') && $request->start_date) {
            $query->where('expense_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('expense_date', '<=', $request->end_date);
        }
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        return Excel::download(new ExpensesExport($expenses), 'expenses_' . date('Y-m-d') . '.xlsx');
    }
}
