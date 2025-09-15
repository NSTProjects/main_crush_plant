<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\Log;

class SalesInvoiceItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $query = SalesInvoiceItem::with(['product', 'salseInoice.customer'])
    //         ->where('IsDeleted', false);

    //     // Filter by InvoiceDate (from related salseInoice)
    //     if ($request->filled('start_date') && $request->filled('end_date')) {
    //         $startDate = Jalalian::fromFormat('Y-n-j', $request->input('start_date'))->toCarbon();
    //         $endDate = Jalalian::fromFormat('Y-n-j', $request->input('end_date'))->toCarbon();

    //         $query->whereHas('salseInoice', function ($q) use ($startDate, $endDate) {
    //             $q->whereBetween('InvoiceDate', [$startDate, $endDate]);
    //         });
    //     }
    //     // Default to current Jalali month
    //     elseif (!$request->filled('filter_type')) {
    //         $startOfMonth = Jalalian::now()->toCarbon()->startOfMonth()->format('Y-m-d');
    //         $endOfMonth = Jalalian::now()->toCarbon()->endOfMonth()->format('Y-m-d');

    //         $query->whereHas('salseInoice', function ($q) use ($startOfMonth, $endOfMonth) {
    //             $q->whereBetween('InvoiceDate', [$startOfMonth, $endOfMonth]);
    //         });
    //     }

    //     $salesInvoiceItems = $query->get()->transform(function ($item) {
    //         if ($item->salseInoice && $item->salseInoice->InvoiceDate) {
    //             $item->PersianInvoiceDate = Jalalian::fromDateTime($item->salseInoice->InvoiceDate)->format('Y-m-d');
    //         }
    //         return $item;
    //     });

    //     return view('salesInvoiceItem.index', [
    //         'page' => 'salesInvoiceItem',
    //         'salesInvoiceItems' => $salesInvoiceItems,
    //     ]);
    // }

    public function index(Request $request)
    {
        $query = SalesInvoiceItem::with(['product', 'salseInoice.customer'])
            ->where('IsDeleted', false);

        // Filter by product name
        if ($request->filled('product_name')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('ProductName', 'like', '%' . $request->product_name . '%');
            });
        }

        // Filter by customer name
        if ($request->filled('customer_name')) {
            $query->whereHas('salseInoice.customer', function ($q) use ($request) {
                $q->where('CustomerName', 'like', '%' . $request->customer_name . '%');
            });
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Jalalian::fromFormat('Y-n-j', $request->start_date)->toCarbon();
            $endDate = Jalalian::fromFormat('Y-n-j', $request->end_date)->toCarbon();
            $query->whereHas('salseInoice', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('InvoiceDate', [$startDate, $endDate]);
            });
        }

        $salesInvoiceItems = $query->get()->transform(function ($item) {
            if ($item->salseInoice && $item->salseInoice->InvoiceDate) {
                $item->PersianInvoiceDate = Jalalian::fromDateTime($item->salseInoice->InvoiceDate)->format('Y-m-d');
            }
            return $item;
        });

        // Return JSON if AJAX
        if ($request->ajax()) {
            return response()->json(['data' => $salesInvoiceItems]);
        }
        $products = Product::where('IsDeleted', false)->get();
        $customers = Customer::where('IsDeleted', false)->get();

        return view('salesInvoiceItem.index', [
            'page' => 'salesInvoiceItem',
            'salesInvoiceItems' => $salesInvoiceItems,
            'products' => $products,
            'customers' => $customers,
        ]);
    }



    public function filter(Request $request)
    {
        $query = SalesInvoiceItem::with(['product', 'salseInoice.customer'])
            ->where('IsDeleted', false);

        if ($request->filled('product_name')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('ProductName', 'like', '%' . $request->product_name . '%');
            });
        }

        if ($request->filled('customer_name')) {
            $query->whereHas('salseInoice.customer', function ($q) use ($request) {
                $q->where('CustomerName', 'like', '%' . $request->customer_name . '%');
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Jalalian::fromFormat('Y-n-j', $request->start_date)->toCarbon();
            $endDate = Jalalian::fromFormat('Y-n-j', $request->end_date)->toCarbon();
            $query->whereHas('salseInoice', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('InvoiceDate', [$startDate, $endDate]);
            });
        }

        $salesInvoiceItems = $query->get()->transform(function ($item) {
            if ($item->salseInoice && $item->salseInoice->InvoiceDate) {
                $item->PersianInvoiceDate = Jalalian::fromDateTime($item->salseInoice->InvoiceDate)->format('Y-m-d');
            }
            return $item;
        });

        return response()->json(['data' => $salesInvoiceItems]);
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
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
