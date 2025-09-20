<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\SalesInvoiceItem;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('customer.index')
            ->with('page', 'customer')
            ->with('customers', Customer::where('IsDeleted', false)->get());
        // ->with('customers', Customer::all());

    }


    // Show the Debit and Credit
    public function showDebit()
    {
        $customers = Customer::where('IsDeleted', false)->get();

        $customerDebits = $customers->map(function ($customer) {
            $ledgers = CustomerLedger::where('IsDeleted', false)
                ->where('CustomerID', $customer->id)
                ->get();

            // Group ledgers by currency
            $currencyGroups = $ledgers->groupBy('Currency');

            $currencyTotals = [];

            foreach ($currencyGroups as $currency => $group) {
                $totalDebit = $group->where('TransactionType', 'Debit')->sum('Amount');
                $totalCredit = $group->where('TransactionType', 'Credit')->sum('Amount');
                $netTotal = $totalCredit - $totalDebit;

                // Only include if net is negative (customer owes money)
                if ($netTotal < 0) {
                    $currencyTotals[] = [
                        'currency' => $currency,
                        'total_debit' => $totalDebit,
                        'total_credit' => $totalCredit,
                        'net_total' => $netTotal,
                    ];
                }
            }

            return [
                'customer' => $customer,
                'currency_totals' => $currencyTotals,
            ];
        })->filter(function ($entry) {
            return count($entry['currency_totals']) > 0;
        });

        // return $customerDebits;
        return view('customer.debit')
            ->with('page', 'customer')
            ->with('customerDebits', $customerDebits);
    }



    public function showCredit()
    {
        $customers = Customer::where('IsDeleted', false)->get();

        $customerCredits = $customers->map(function ($customer) {
            $ledgers = CustomerLedger::where('IsDeleted', false)
                ->where('CustomerID', $customer->id)
                ->get();

            $currencyGroups = $ledgers->groupBy('Currency');
            $currencyTotals = [];

            foreach ($currencyGroups as $currency => $group) {
                $totalDebit = $group->where('TransactionType', 'Debit')->sum('Amount');
                $totalCredit = $group->where('TransactionType', 'Credit')->sum('Amount');
                $netTotal = $totalCredit - $totalDebit;

                if ($netTotal > 0) {
                    $currencyTotals[] = [
                        'currency' => $currency,
                        'total_debit' => $totalDebit,
                        'total_credit' => $totalCredit,
                        'net_total' => $netTotal,
                    ];
                }
            }

            return [
                'customer' => $customer,
                'currency_totals' => $currencyTotals,
            ];
        })->filter(function ($entry) {
            return count($entry['currency_totals']) > 0;
        });

        return view('customer.credit')
            ->with('page', 'customer')
            ->with('customerCredits', $customerCredits);
    }


    /**
     * Show the form for Show Leader.
     */

    public function showLedger($id)
    {
        // Step 1: Find the customer
        $customer = Customer::findOrFail($id);

        // Step 2: Get ledgers where IsDeleted is false and CustomerID matches
        $ledgers = CustomerLedger::where('IsDeleted', false)
            ->where('CustomerID', $customer->id)
            ->get()->transform(function ($ledger) {
                if ($ledger->LedgerDate) {
                    $ledger->DateLedger = Jalalian::fromDateTime($ledger->LedgerDate)->format('Y-m-d');
                }
                return $ledger;
            });
        // 🔍 Filter only invoice-type ledgers with valid ReferenceID
        $invoiceLedgers = $ledgers->filter(function ($ledger) {
            return $ledger->ReferenceType === 'invoice' && !is_null($ledger->ReferenceID);
        });
        // Step 1: Extract all ReferenceIDs from invoice-ledgers
        $invoiceIds = $invoiceLedgers->pluck('ReferenceID')->unique()->toArray();

        // Step 2: Get all SalesInvoiceItems that match those invoice IDs
        $salesInvoiceItems = SalesInvoiceItem::with(['product'])
            ->where('IsDeleted', false)
            ->whereIn('InvoiceID', $invoiceIds)
            ->get()
            ->groupBy('InvoiceID'); // Step 3: Group items by InvoiceID

        // 🔗 Load invoice and items for those filtered entries

        // return $salesInvoiceItems;
        // Step 3: Return the view with both customer and ledgers
        return view('customer.ledger')
            ->with('page', 'customer')
            ->with('customer', $customer)
            ->with('ledgers', $ledgers)
            ->with('salesInvoiceItems', $salesInvoiceItems);
    }


    public function filterLedger(Request $request, $id)
    {
        try {

            $customer = Customer::findOrFail($id);
            // return $customer;

            $query = CustomerLedger::where('IsDeleted', false)
                ->where('CustomerID', $customer->id);

            // if ($request->filled('start_date') && $request->filled('end_date')) {
            //     $startDate = Jalalian::fromFormat('Y-m-d', $request->start_date)->toCarbon();
            //     $endDate = Jalalian::fromFormat('Y-m-d', $request->end_date)->toCarbon();
            //     $query->whereBetween('LedgerDate', [$startDate, $endDate]);
            // }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Jalalian::fromFormat('Y-n-j', $request->start_date)->toCarbon();
                $endDate = Jalalian::fromFormat('Y-n-j', $request->end_date)->toCarbon();
                $query->whereBetween('LedgerDate', [$startDate, $endDate]);
            }

            $ledgers = $query->get()->transform(function ($ledger) {
                $ledger->DateLedger = Jalalian::fromDateTime($ledger->LedgerDate)->format('Y-m-d');
                return $ledger;
            });

            return response()->json(['ledgers' => $ledgers]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function storeLedger(Request $request)
    {
        // return $request;
        $shamsiDate = $request->LedgerDate;
        $miladiDate = Jalalian::fromFormat('Y-n-j', $shamsiDate)->toCarbon();

        $tranType = $request->ReferenceType === 'payment_in' ? 'Credit' : 'Debit';

        CustomerLedger::create([
            'CustomerID'   => $request->CustomerID,
            'LedgerDate'   => $miladiDate,
            'Description'    =>  $request->Description,
            'TransactionType'   => $tranType,
            'Amount'  => $request->Amount,
            'Currency'  => $request->Currency,
            'ReferenceID'  => $request->ReferenceID,
            'ReferenceType'  => $request->ReferenceType,
            'SyncStatus'  => 'pending',
            'IsDeleted'   => false,

        ]);
        return redirect()->back()->with('success', 'مشتری با موفقیت ثبت شد');
    }


    public function updateLedger(Request $request, CustomerLedger $ledger)
    {
        $shamsiDate = $request->LedgerDate;
        $miladiDate = Jalalian::fromFormat('Y-n-j', $shamsiDate)->toCarbon();
        // return $request;
        $tranType = $request->ReferenceType === 'payment_in' ? 'Credit' : 'Debit';

        $ledger->update([
            'CustomerID' => $request->input('CustomerID'),
            'LedgerDate' => $miladiDate,
            'Description' => $request->input('Description'),
            'TransactionType' => $tranType,
            'Amount' => $request->input('Amount'),
            'Currency' => $request->input('Currency'),
            'ReferenceType' => $request->input('ReferenceType'),
            'SyncStatus' => 'pending', // Optional: if you want to reset sync
        ]);

        return redirect()->back()->with('success', 'مشتری با موفقیت ثبت شد');
    }

    public function destroyLedger(string $id)
    {
        $ledger = CustomerLedger::findOrFail($id);
        $ledger->update([
            'IsDeleted' => true,
            'SyncStatus' => 'pending', // Optional: flag for resync
        ]);
        return redirect()->back();
    }



    // new controller for store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'CustomerName' => 'required|string|min:3|max:100',
            'Phone' => 'required|string|max:20',
            'Address' => 'required|string|max:200',
        ], [
            'CustomerName.required' => 'لطفا نام را بنویسید',
            'CustomerName.min' => 'نام مشتری نباید کمتر از ۳ حرف باشد',
        ]);

        Customer::create([
            'CustomerName' => $validated['CustomerName'],
            'Phone' => $validated['Phone'],
            'Address' => $validated['Address'] ?? null,
            'SyncStatus' => 'pending',
            'IsDeleted' => false,
        ]);

        return redirect()->route('customer.index')->with('success', 'مشتری با موفقیت ثبت شد');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        // ✅ Validation
        $validated = $request->validate([
            'CustomerName' => 'required|string|min:3|max:100',
            'Phone' => 'required|string|max:20',
            'Address' => 'required|string|max:200',
        ], [
            'CustomerName.required' => 'لطفا نام را بنویسید',
            'CustomerName.min' => 'نام مشتری نباید کمتر از ۳ حرف باشد',
            'Phone.required' => 'شماره تماس الزامی است',
            'Address.required' => 'آدرس الزامی است',
        ]);

        // ✅ Update using Eloquent
        $customer->update([
            'CustomerName' => $validated['CustomerName'],
            'Phone' => $validated['Phone'],
            'Address' => $validated['Address'],
            'SyncStatus' => 'pending', // Optional: if you want to reset sync
        ]);

        // ✅ Redirect with success message
        return redirect()->route('customer.index')->with('success', 'مشتری با موفقیت ویرایش شد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->update([
            'IsDeleted' => true,
            'SyncStatus' => 'pending', // Optional: flag for resync
        ]);
        return redirect()->back();
    }
}
