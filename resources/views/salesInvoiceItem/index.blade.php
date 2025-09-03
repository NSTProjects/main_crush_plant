@extends('layouts.master')

@section('content')

<div class="card">
    <div class="card-header ">
        لیست اجناس فروخته شده
    </div>
    <div class="card-body">
        <div class="mx-auto col-md-12">
            <center>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th colspan="6" class="text-center">لیست اجناس فروخته شده</th>
                            </tr>
                            <tr>
                                <th>آدی</th>
                                <th> نام محصول</th>
                                <th> نام مشتری</th>
                                <th>مقدار </th>
                                <th>واحد </th>
                                <th>قیمت کلی </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i=1;
                            @endphp
                            @foreach($salesInvoiceItems as $salesInvoiceItem)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{ $salesInvoiceItem->product?->ProductName ?? 'N/A' }}</td>
                                <td>{{ $salesInvoiceItem->salseInoice?->customer?->CustomerName ?? 'N/A' }}</td>
                                <td>{{$salesInvoiceItem->Quantity}}</td>
                                <td>{{ $salesInvoiceItem->product?->Unit ?? 'N/A' }}</td>
                                <td>{{$salesInvoiceItem->TotalPrice}}</td>

                            </tr>
                            @endforeach
                            <tr>
                                <th colspan="5" class="text-center"> مجموعه :</th>
                                <th>{{ $salesInvoiceItems->sum('TotalPrice')}} (AFN)</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </center>
        </div>
    </div>
</div>

















@endsection