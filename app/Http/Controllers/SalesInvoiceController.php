<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\Product;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Delivery;
use App\Models\SalesInvoiceItem;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

use PhpParser\Node\Stmt\TryCatch;

class SalesInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $salesInvoiceItems = SalesInvoiceItem::with(['salseInoice.customer', 'product']) // eager load invoice → customer and product
            ->where('IsDeleted', false)
            ->get()
            ->transform(function ($invoice) {
                if ($invoice->InvoiceDate) {
                    $invoice->InvoiceDate = Jalalian::fromDateTime($invoice->InvoiceDate)
                        ->format('Y-m-d');
                }
                return $invoice;
            });


        // $query = SalesInvoice::with('customer')->where('IsDeleted', false);

        // // اگر کاربر تاریخ خاصی انتخاب نکرده باشد، فقط فاکتورهای ماه جاری را نمایش بده
        // if (!$request->filled('filter_type')) {
        //     // تاریخ شروع ماه جاری
        //     $startOfMonth = Jalalian::now()->toCarbon()->startOfMonth()->format('Y-m-d');

        //     // تاریخ پایان ماه جاری
        //     $endOfMonth = Jalalian::now()->toCarbon()->endOfMonth()->format('Y-m-d');
        //     $query->whereBetween('InvoiceDate', [$startOfMonth, $endOfMonth]);
        // }

        // // اگر کاربر فیلتر خاصی انتخاب کرده باشد
        // if ($request->filled('start_date') && $request->filled('end_date')) {

        //     $startDate = Jalalian::fromFormat('Y-n-j', $request->input('start_date'))->toCarbon();
        //     $endDate = Jalalian::fromFormat('Y-n-j', $request->input('end_date'))->toCarbon();

        //     $query->whereBetween('InvoiceDate', [$startDate, $endDate]);
        // }
        $query = SalesInvoice::with('customer')->where('IsDeleted', false);

        // First check if user provided a date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Jalalian::fromFormat('Y-n-j', $request->input('start_date'))->toCarbon();
            $endDate = Jalalian::fromFormat('Y-n-j', $request->input('end_date'))->toCarbon();
            $query->whereBetween('InvoiceDate', [$startDate, $endDate]);
        }
        // Otherwise, default to current month
        // elseif (!$request->filled('filter_type')) {
        //     $startOfMonth = Jalalian::now()->toCarbon()->startOfMonth()->format('Y-m-d');
        //     $endOfMonth = Jalalian::now()->toCarbon()->endOfMonth()->format('Y-m-d');
        //     $query->whereBetween('InvoiceDate', [$startOfMonth, $endOfMonth]);
        // }



        $salesInvoices = $query->get()->transform(function ($invoice) {
            if ($invoice->InvoiceDate) {
                $invoice->DateInvoice = Jalalian::fromDateTime($invoice->InvoiceDate)->format('Y-m-d');
            }
            return $invoice;
        });


        $products = Product::where('IsDeleted', false)->get();


        return view('salesInvoice.index', [
            'page' => 'salesInvoice',
            'salesInvoices' => $salesInvoices,
            'salesInvoiceItems' => $salesInvoiceItems,
            'products' => $products,
        ])->with('customers', Customer::where('IsDeleted', false)->get());
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
        $shamsiDate = $request->InvoiceDate;
        $miladiDate = Jalalian::fromFormat('Y-n-j', $shamsiDate)->toCarbon();

        DB::beginTransaction();

        try {


            // Step 1: Create the invoice header
            $invoice = SalesInvoice::create([
                'InvoiceDate'     => $miladiDate,
                'CustomerID'      => $request->CustomerID,
                'TotalAmount'     => $request->TotalPrice,
                'DiscountAmount'  => $request->DiscountAmount,
                'RecievedAmount'  => $request->RecievedAmount,
                'BalanceAmount'   => $request->BalanceAmount,
                'Description'     => $request->Description,
                'SyncStatus'      => 'pending',
                'IsDeleted'       => false,
            ]);

            // Step 2: Merge duplicate items
            $mergedItems = [];
            // $invoice1 = [];
            foreach ($request->items as $item) {
                $productId = $item['ProductID'];
                $quantity = floatval($item['Quantity']);
                $unitPrice = floatval($item['UnitPrice']);
                $totalPrice = floatval($item['TotalPrice']);

                if (isset($mergedItems[$productId])) {
                    $mergedItems[$productId]['Quantity'] += $quantity;
                    $mergedItems[$productId]['UnitPrice'] = $unitPrice;
                    $mergedItems[$productId]['TotalPrice'] += $totalPrice;
                } else {
                    $mergedItems[$productId] = [
                        'ProductID'  => $productId,
                        'Quantity'   => $quantity,
                        'UnitPrice'   => $unitPrice,
                        'TotalPrice' => $totalPrice,
                    ];
                }
            }

            // Step 3: Validate and insert each item
            foreach ($mergedItems as $productId => $data) {
                $product = Product::find($productId);

                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'error' => "Product with ID {$productId} not found."
                    ], 422);
                }

                $deliveredVolume = Delivery::where('ProductID', $productId)->sum('TotalVolume');
                $availableStock = floatval($product->OpenStock) + floatval($deliveredVolume);

                if ($availableStock < $data['Quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'error' => "Insufficient stock for '{$product->ProductName}'. Available: {$availableStock}, Requested: {$data['Quantity']}"
                    ], 422);
                }


                // Insert item into sales_invoice_items
                SalesInvoiceItem::create([
                    'InvoiceID'   => $invoice->id,
                    'ProductID'   => $productId,
                    'Quantity'    => $data['Quantity'],
                    'UnitPrice'   => $data['UnitPrice'],
                    'TotalPrice'  => $data['TotalPrice'],
                    'SyncStatus'  => 'pending',
                    'IsDeleted'   => false,
                ]);
            }
            CustomerLedger::create([
                'CustomerID'   => $request->CustomerID,
                'LedgerDate'   => $miladiDate,
                'Description'    =>  $request->Description,
                'TransactionType'   => 'Debit',
                'Amount'  => $request->BalanceAmount,
                'ReferenceID'  => $invoice->id,
                'ReferenceType'  => 'invoice',
                'SyncStatus'  => 'pending',
                'IsDeleted'   => false,

            ]);


            DB::commit();
            // return response()->json(['message' => 'Sales invoice created successfully.'], 200);
            // return redirect()->route('sales-invoice.print')->with('success', 'لیست فروش با موفقیت ثبت شد');
            return redirect()->route('sales-invoice.print', ['id' => $invoice->id])
                ->with('success', 'لیست فروش با موفقیت ثبت شد');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Invoice creation failed', ['error' => $e->getMessage()]);
            // return response()->json(['error' => 'Something went wrong.'], 500);
            return redirect()->route('sales-invoice.index')->with('error', 'لیست فروش با موفقیت ثبت نشد');
        }
    }



    public function print($id)
    {

        $invoice = SalesInvoice::with(['customer', 'salesInvoiceItem.product'])
            ->where('IsDeleted', false)
            ->findOrFail($id);
        $invoice->DateInvoice = Jalalian::fromDateTime($invoice->InvoiceDate)->format('Y-m-d');
        // return $invoice;

        return view('salesInvoice.print', compact('invoice'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesInvoice $salesInvoice)
    {
        $shamsiDate = $request->input('InvoiceDate');
        $miladiDate = Jalalian::fromFormat('Y-n-j', $shamsiDate)->toCarbon();

        DB::beginTransaction();

        try {
            SalesInvoiceItem::where('InvoiceID', $salesInvoice->id)->delete();
            CustomerLedger::where('ReferenceID', $salesInvoice->id)
                ->where('ReferenceType', 'invoice')
                ->delete();

            // Step 1: Create the invoice header


            $salesInvoice->update([
                'InvoiceDate' => $miladiDate,
                'CustomerID' => $request->input('CustomerID'),
                'TotalAmount' => $request->input('TotalAmount'),
                'DiscountAmount' => $request->input('DiscountAmount'),
                'RecievedAmount' => $request->input('RecievedAmount'),
                'BalanceAmount' => $request->input('TotalAmount') - ($request->input('DiscountAmount') + $request->input('RecievedAmount')),
                'Description' => $request->input('Description'),
                'SyncStatus' => 'pending',
            ]);

            // Step 2: Merge duplicate items
            $mergedItems = [];
            $items = array_values($request->items);
            foreach ($items as $item) {
                $productId = $item['ProductID'];
                $quantity = floatval($item['Quantity']);
                $unitPrice = floatval($item['UnitPrice']);
                $totalPrice = floatval($item['TotalPrice']);

                if (isset($mergedItems[$productId])) {
                    $mergedItems[$productId]['Quantity'] += $quantity;
                    $mergedItems[$productId]['UnitPrice'] = $unitPrice;
                    $mergedItems[$productId]['TotalPrice'] += $totalPrice;
                } else {
                    $mergedItems[$productId] = [
                        'ProductID'  => $productId,
                        'Quantity'   => $quantity,
                        'UnitPrice'   => $unitPrice,
                        'TotalPrice' => $totalPrice,
                    ];
                }
            }

            // Step 3: Validate and insert each item
            foreach ($mergedItems as $productId => $data) {
                $product = Product::find($productId);

                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'error' => "Product with ID {$productId} not found."
                    ], 422);
                }

                $deliveredVolume = Delivery::where('ProductID', $productId)->sum('TotalVolume');
                $availableStock = floatval($product->OpenStock) + floatval($deliveredVolume);

                if ($availableStock < $data['Quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'error' => "Insufficient stock for '{$product->ProductName}'. Available: {$availableStock}, Requested: {$data['Quantity']}"
                    ], 422);
                }


                // Insert item into sales_invoice_items
                SalesInvoiceItem::create([
                    'InvoiceID'   => $salesInvoice->id,
                    'ProductID'   => $productId,
                    'Quantity'    => $data['Quantity'],
                    'UnitPrice'   => $data['UnitPrice'],
                    'TotalPrice'  => $data['TotalPrice'],
                    'SyncStatus'  => 'pending',
                    'IsDeleted'   => false,
                ]);
            }
            CustomerLedger::create([
                'CustomerID'   => $request->CustomerID,
                'LedgerDate'   => $miladiDate,
                'Description'    =>  $request->Description,
                'TransactionType'   => 'Debit',
                'Amount'  => $request->BalanceAmount,
                'ReferenceID'  => $salesInvoice->id,
                'ReferenceType'  => 'invoice',
                'SyncStatus'  => 'pending',
                'IsDeleted'   => false,

            ]);


            DB::commit();
            return redirect()->route('sales-invoice.print', ['id' => $salesInvoice->id])
                ->with('success', 'لیست فروش با موفقیت ثبت شد');
            // return redirect()->route('sales-invoice.index')->with('success', 'لیست فروش با موفقیت ویرایش شد');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SalesInvoice update failed', ['error' => $e->getMessage()]);
            return redirect()->route('sales-invoice.index')->with('error', 'لیست فروش با موفقیت ثبت نشد');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        DB::beginTransaction();
        try {
            SalesInvoiceItem::where('InvoiceID', $id)->update([
                'IsDeleted' => true,
                'SyncStatus' => 'pending', // Optional: flag for resync
            ]);

            CustomerLedger::where('ReferenceID', $id)
                ->where('ReferenceType', 'invoice')
                ->update([
                    'IsDeleted' => true,
                    'SyncStatus' => 'pending', // Optional: flag for resync
                ]);

            $salesInvoice = SalesInvoice::findOrFail($id);
            $salesInvoice->update([
                'IsDeleted' => true,
                'SyncStatus' => 'pending', // Optional: flag for resync
            ]);
            DB::commit();

            return redirect()->back()->with('status', 'salesInvoice marked as deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sales-invoice.index')->with('error', 'لیست فروش با موفقیت ثبت نشد');
        }
    }
}
