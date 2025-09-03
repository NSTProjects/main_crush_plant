<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use Illuminate\Http\Request;

class SalesInvoiceItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $salesInvoiceItems =  SalesInvoiceItem::with(['product', 'invoice.customer']) // eager load Custormer
        //     ->where('IsDeleted', false)
        //     ->get();
        $salesInvoiceItems = SalesInvoiceItem::with(['product', 'salseInoice.customer'])
            ->where('IsDeleted', false)
            ->get();

        // return $salesInvoiceItems;

        $products = Product::where('IsDeleted', false)->get();
        // $salesInvoices = SalesInvoice::where('IsDeleted', false)->get();
        $salesInvoices =  SalesInvoice::with('customer') // eager load Custormer
            ->where('IsDeleted', false)
            ->get();
        return view('salesInvoiceItem.index', [
            'page' => 'salesInvoiceItem',
            'salesInvoiceItems' => $salesInvoiceItems,
            'products' => $products,
            'salesInvoices' => $salesInvoices
        ]);
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
