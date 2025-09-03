<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Product;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Delivery::with('product')->where('IsDeleted', false);

        // اگر کاربر تاریخ خاصی انتخاب نکرده باشد، فقط فاکتورهای ماه جاری را نمایش بده
        // if (!$request->filled('filter_type')) {
        //     // تاریخ شروع ماه جاری
        //     $startOfMonth = Jalalian::now()->toCarbon()->startOfMonth()->format('Y-m-d');

        //     // تاریخ پایان ماه جاری
        //     $endOfMonth = Jalalian::now()->toCarbon()->endOfMonth()->format('Y-m-d');
        //     $query->whereBetween('DeliveryDate', [$startOfMonth, $endOfMonth]);
        // }

        // // اگر کاربر فیلتر خاصی انتخاب کرده باشد
        // if ($request->filled('start_date') && $request->filled('end_date')) {

        //     $startDate = Jalalian::fromFormat('Y-n-j', $request->input('start_date'))->toCarbon();
        //     $endDate = Jalalian::fromFormat('Y-n-j', $request->input('end_date'))->toCarbon();

        //     $query->whereBetween('DeliveryDate', [$startDate, $endDate]);
        // }

        // First check if user provided a date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Jalalian::fromFormat('Y-n-j', $request->input('start_date'))->toCarbon();
            $endDate = Jalalian::fromFormat('Y-n-j', $request->input('end_date'))->toCarbon();
            $query->whereBetween('DeliveryDate', [$startDate, $endDate]);
        }
        // Otherwise, default to current month
        elseif (!$request->filled('filter_type')) {
            $startOfMonth = Jalalian::now()->toCarbon()->startOfMonth()->format('Y-m-d');
            $endOfMonth = Jalalian::now()->toCarbon()->endOfMonth()->format('Y-m-d');
            $query->whereBetween('DeliveryDate', [$startOfMonth, $endOfMonth]);
        }
        $deliveries = $query->get()->transform(function ($delivery) {
            if ($delivery->DeliveryDate) {
                $delivery->DateDelivery = Jalalian::fromDateTime($delivery->DeliveryDate)->format('Y-m-d');
            }
            return $delivery;
        });

        // $deliveries = Delivery::with('product') // eager load product
        //     ->where('IsDeleted', false)
        //     ->get();
        return view('delivery.index', [
            'page' => 'delivery',
            'deliveries' => $deliveries
        ])
            ->with('products', Product::where('IsDeleted', false)->get());
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
        $shamsiDate = $request->DeliveryDate;
        $miladiDate = Jalalian::fromFormat('Y-n-j', $shamsiDate)->toCarbon();
        // return $request;

        Delivery::create([
            'DeliveryDate' => $miladiDate,
            'ProductID' => $request->input('ProductID'),
            'Vehicle' => $request->input('Vehicle'),
            'NumOfTrucks' => $request->input('NumOfTrucks'),
            'CubicMetersPerTruck' => $request->input('CubicMetersPerTruck'),
            'TotalVolume' => $request->input('CubicMetersPerTruck') * $request->input('NumOfTrucks'),
            'Description' => $request->input('Description'),
            'SyncStatus' => 'pending',
            'IsDeleted' => false,
        ]);

        return redirect()->route('delivery.index')->with('success', 'آورده گی با موفقیت ثبت شد');
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
    public function update(Request $request, Delivery $delivery)
    {
        $shamsiDate = $request->DeliveryDate;
        $miladiDate = Jalalian::fromFormat('Y-n-j', $shamsiDate)->toCarbon();
        // ✅ Update using Eloquent
        $delivery->update([
            'DeliveryDate' => $miladiDate,
            'ProductID' => $request->input('ProductID'),
            'Vehicle' => $request->input('Vehicle'),
            'NumOfTrucks' => $request->input('NumOfTrucks'),
            'CubicMetersPerTruck' => $request->input('CubicMetersPerTruck'),
            'TotalVolume' => $request->input('CubicMetersPerTruck') * $request->input('NumOfTrucks'),
            'Description' => $request->input('Description'),
            'SyncStatus' => 'pending',
        ]);


        // ✅ Redirect with success message
        return redirect()->route('delivery.index')->with('success', 'آورده گی با موفقیت ویرایش شد');
        // return $request;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // return $id;
        $delivery = Delivery::findOrFail($id);
        $delivery->update([
            'IsDeleted' => true,
            'SyncStatus' => 'pending', // Optional: flag for resync
        ]);

        return redirect()->back()->with('status', 'delivery marked as deleted.');
    }
}
