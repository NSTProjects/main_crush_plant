<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\journal;
use App\Models\Product;
use App\Models\SalesInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class UserController extends Controller
{
    //

    public function dashboard()
    {
        // return view('dashboard'); // مطمئن شو که این ویو وجود داره
        // return Customer::count();
        return view('dashboard', [
            'customerCount' => Customer::count(),
            'productCount' => Product::count(),
            'invoiceCount' => SalesInvoice::count(),
            'expenseTotal' => Expense::sum('Amount'),
            'recentInvoices' => SalesInvoice::latest()->take(5)->get()->transform(function ($invoice) {
                if ($invoice->InvoiceDate) {
                    $invoice->InvoiceDate = Jalalian::fromDateTime($invoice->InvoiceDate)
                        ->format('Y-m-d');
                }
                return $invoice;
            }),
        ]);
    }


    public function journal(Request $request)
    {
        // $transactions = journal::orderBy('TransactionDate', 'desc')->get();
        // // Convert each TransactionDate to Jalali
        // foreach ($transactions as $transaction) {
        //     $transaction->JalaliDate = Jalalian::fromDateTime($transaction->TransactionDate)->format('Y/m/d');
        // }

        // if ($request->filled('start_date') && $request->filled('end_date')) {
        //     $startDate = Jalalian::fromFormat('Y-n-j', $request->input('start_date'))->toCarbon();
        //     $endDate = Jalalian::fromFormat('Y-n-j', $request->input('end_date'))->toCarbon();
        //     $query->whereBetween('TransactionDate', [$startDate, $endDate]);
        // }
        // // Otherwise, default to current month
        // elseif (!$request->filled('filter_type')) {
        //     $startOfMonth = Jalalian::now()->toCarbon()->startOfMonth()->format('Y-m-d');
        //     $endOfMonth = Jalalian::now()->toCarbon()->endOfMonth()->format('Y-m-d');
        //     $query->whereBetween('TransactionDate', [$startOfMonth, $endOfMonth]);
        // }

        // $expenses = $query->get()->transform(function ($expense) {
        //     if ($expense->ExpenseDate) {
        //         $expense->DateExpense = Jalalian::fromDateTime($expense->ExpenseDate)->format('Y-m-d');
        //     }
        //     return $expense;
        // });

        // Start with base query
        $query = journal::query();

        // Filter by Jalali start and end date if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Jalalian::fromFormat('Y-n-j', $request->input('start_date'))->toCarbon();
            $endDate = Jalalian::fromFormat('Y-n-j', $request->input('end_date'))->toCarbon();
            $query->whereBetween('TransactionDate', [$startDate, $endDate]);
        }
        // Otherwise, show last 30 days
        else {
            $thirtyDaysAgo = Carbon::now()->subDays(30)->format('Y-m-d');
            $today = Carbon::now()->format('Y-m-d');
            $query->whereBetween('TransactionDate', [$thirtyDaysAgo, $today]);
        }

        // Fetch filtered transactions
        $transactions = $query->orderBy('TransactionDate', 'desc')->get();

        // Convert TransactionDate to Jalali
        foreach ($transactions as $transaction) {
            try {
                $transaction->JalaliDate = Jalalian::fromDateTime($transaction->TransactionDate)->format('Y/m/d');
            } catch (\Exception $e) {
                $transaction->JalaliDate = $transaction->TransactionDate; // fallback
            }
        }

        $products = Product::where('IsDeleted', false)->get();
        $customers = Customer::where('IsDeleted', false)->get();
        // return $transactions;
        return view('journal', compact('transactions', 'products', 'customers'));
    }
}
