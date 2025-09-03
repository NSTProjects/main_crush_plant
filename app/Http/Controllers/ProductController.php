<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Product;
use App\Models\SalesInvoiceItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $products = Product::where('IsDeleted', false)->get();

        // Get total delivered quantity per product
        $deliveries = Delivery::where('IsDeleted', false)
            ->select('ProductID', DB::raw('SUM(TotalVolume) as total_delivered'))
            ->groupBy('ProductID')
            ->pluck('total_delivered', 'ProductID');
        // returns [ProductID => total_delivered]

        $salesInvoiceItems = SalesInvoiceItem::where('IsDeleted', false)
            ->select('ProductID', DB::raw('SUM(Quantity) as total_sale'))
            ->groupBy('ProductID')
            ->pluck('total_sale', 'ProductID');


        return view('product.index', compact('products', 'deliveries', 'salesInvoiceItems'))
            ->with('page', 'product');


        // return view('product.index')
        //     ->with('page', 'product')
        //     ->with('products', Product::where('IsDeleted', false)->get());


        // ->with('products', Product::all());
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
        // return $request->all();

        $product = Product::create([
            'ProductName' => $request->input('ProductName'),
            'OpenStock' => $request->input('OpenStock'),
            'Unit' => $request->input('Unit'),
            'UnitPrice' => $request->input('UnitPrice'),
            'SyncStatus' => 'pending',
            'IsDeleted' => false,
        ]);

        Log::info('New product created:', $product->toArray());
        return redirect()->route('product.index')->with('success', 'محصول با موفقیت ثبت شد');
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
    public function update(Request $request, Product $product)
    {


        // ✅ Update using Eloquent
        $product->update([
            'ProductName' => $request->input('ProductName'),
            'OpenStock' => $request->input('OpenStock'),
            'Unit' => $request->input('Unit'),
            'UnitPrice' => $request->input('UnitPrice'),
            'SyncStatus' => 'pending',
        ]);


        // ✅ Redirect with success message
        return redirect()->route('product.index')->with('success', 'مشتری با موفقیت ویرایش شد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'IsDeleted' => true,
            'SyncStatus' => 'pending', // Optional: flag for resync
        ]);

        return redirect()->back()->with('status', 'Product marked as deleted.');
    }
}
