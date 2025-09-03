<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Expense::where('IsDeleted', false);

        // اگر کاربر تاریخ خاصی انتخاب نکرده باشد، فقط فاکتورهای ماه جاری را نمایش بده
        // if (!$request->filled('filter_type')) {
        //     // تاریخ شروع ماه جاری
        //     $startOfMonth = Jalalian::now()->toCarbon()->startOfMonth()->format('Y-m-d');

        //     // تاریخ پایان ماه جاری
        //     $endOfMonth = Jalalian::now()->toCarbon()->endOfMonth()->format('Y-m-d');
        //     $query->whereBetween('ExpenseDate', [$startOfMonth, $endOfMonth]);
        // }

        // // اگر کاربر فیلتر خاصی انتخاب کرده باشد
        // if ($request->filled('start_date') && $request->filled('end_date')) {

        //     $startDate = Jalalian::fromFormat('Y-n-j', $request->input('start_date'))->toCarbon();
        //     $endDate = Jalalian::fromFormat('Y-n-j', $request->input('end_date'))->toCarbon();

        //     $query->whereBetween('ExpenseDate', [$startDate, $endDate]);
        // }
        // First check if user provided a date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Jalalian::fromFormat('Y-n-j', $request->input('start_date'))->toCarbon();
            $endDate = Jalalian::fromFormat('Y-n-j', $request->input('end_date'))->toCarbon();
            $query->whereBetween('ExpenseDate', [$startDate, $endDate]);
        }
        // Otherwise, default to current month
        elseif (!$request->filled('filter_type')) {
            $startOfMonth = Jalalian::now()->toCarbon()->startOfMonth()->format('Y-m-d');
            $endOfMonth = Jalalian::now()->toCarbon()->endOfMonth()->format('Y-m-d');
            $query->whereBetween('ExpenseDate', [$startOfMonth, $endOfMonth]);
        }

        $expenses = $query->get()->transform(function ($expense) {
            if ($expense->ExpenseDate) {
                $expense->DateExpense = Jalalian::fromDateTime($expense->ExpenseDate)->format('Y-m-d');
            }
            return $expense;
        });

        return view('expense.index')
            ->with('page', 'expense')
            ->with('expenses', $expenses);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $shamsiDate = $request->ExpenseDate;
        $miladiDate = Jalalian::fromFormat('Y-n-j', $shamsiDate)->toCarbon();

        Expense::create([
            'ExpenseDate' => $miladiDate,
            'ExpenseType' => $request->input('ExpenseType'),
            'Amount' => $request->input('Amount'),
            'Description' => $request->input('Description'),
            'SyncStatus' => 'pending',
            'IsDeleted' => false,
        ]);

        return redirect()->route('expense.index')->with('success', 'مصرف با موفقیت ثبت شد');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $shamsiDate = $request->ExpenseDate;
        $miladiDate = Jalalian::fromFormat('Y-n-j', $shamsiDate)->toCarbon();
        // ✅ Update using Eloquent
        $expense->update([
            'ExpenseDate' => $miladiDate,
            'ExpenseType' => $request->input('ExpenseType'),
            'Amount' => $request->input('Amount'),
            'Description' => $request->input('Description'),
            'SyncStatus' => 'pending',
        ]);


        // ✅ Redirect with success message
        return redirect()->route('expense.index')->with('success', 'مشتری با موفقیت ویرایش شد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->update([
            'IsDeleted' => true,
            'SyncStatus' => 'pending', // Optional: flag for resync
        ]);

        return redirect()->back()->with('status', 'Expense marked as deleted.');
    }
}
