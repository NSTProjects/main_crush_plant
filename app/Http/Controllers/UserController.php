<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\journal;
use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Morilog\Jalali\Jalalian;



class UserController extends Controller
{
    //

    public function index()
    {
        // $users = User::get();
        // return $users;
        // return view('user.index', compact('users'));
        $user = Auth::user();

        if ($user->role === 'admin') {
            $users = User::all(); // Admin sees all users
        } else {
            $users = User::where('id', $user->id)->get(); // Regular user sees only themselves
        }

        return view('user.index', compact('users'));
    }

    public function store(Request $request)
    {
        // return $request->all();

        User::create([
            'name' => $request->input('name'),
            'email' =>  $request->input('email'),
            'password' => $request->input('password'),
            'role' => $request->input('role'),
        ]);

        return redirect()->back()->with('success', 'محصول با موفقیت ثبت شد');
    }




    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->input('email') !== $user->email) {
            $request->validate([
                'email' => 'required|email|unique:users,email',
            ]);
            $user->email = $request->input('email');
        }

        $user->name = $request->input('name');
        $user->role = $request->input('role');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->back()->with('success', 'کاربر با موفقیت ویرایش شد');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Prevent the logged-in user from deleting themselves
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'شما نمی‌توانید حساب خود را حذف کنید.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'کاربر با موفقیت حذف شد.');
    }



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
            $thirtyDaysAgo = Carbon::now()->format('Y-m-d');
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

        // 🔍 Filter only invoice-type ledgers with valid ReferenceID
        // $customerTransactions = $transactions->filter(function ($transaction) {
        //     return  !is_null($transaction->CustomerID);
        // });
        // // Step 1: Extract all ReferenceIDs from invoice-ledgers
        // $customerResult = $customerTransactions->pluck('CustomerID')->unique()->toArray();
        // // return $invoiceIds;


        $customers = Customer::where('IsDeleted', false)->get();

        $products = Product::where('IsDeleted', false)->get();

        // return $transactions;
        return view('journal', compact('transactions', 'products', 'customers'));
    }
}
