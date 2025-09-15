@extends('layouts.master')

@section('content')

<div class="card">
    <div class="card-header ">
        لیست اجناس فروخته شده

        <form id="filterForm" class="row g-2">
            <div class="col-md-3">
                <select class="form-select" name="product_name">
                    <option value="">انتخاب محصول</option>
                    @foreach($products as $product)
                    <option value="{{ $product->ProductName }}">{{ $product->ProductName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <select class="form-select" name="customer_name">
                    <option value="">انتخاب مشتری</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->CustomerName }}">{{ $customer->CustomerName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control usage1" name="start_date" placeholder="تاریخ شروع" readonly>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control usage1" name="end_date" placeholder="تاریخ پایان" readonly>
            </div>
            <div class="col-md-3">
                <button type="button" id="filterBtn" class="btn btn-primary">فیلتر</button>

            </div>
        </form>

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
                                <th>#</th>
                                <th> نام محصول</th>
                                <th> نام مشتری</th>
                                <th>تاریخ</th>
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
                                <td>{{ $salesInvoiceItem->PersianInvoiceDate ?? 'N/A' }}</td>
                                <td>{{$salesInvoiceItem->Quantity}}</td>
                                <td>{{ $salesInvoiceItem->product?->Unit ?? 'N/A' }}</td>
                                <td>{{$salesInvoiceItem->TotalPrice}}</td>

                            </tr>
                            @endforeach
                            <tr>
                                <th colspan="4" class="text-center"> مجموعه :</th>
                                <th>{{ $salesInvoiceItems->sum('Quantity')}} </th>
                                <th class="text-center"> </th>
                                <th>{{ $salesInvoiceItems->sum('TotalPrice')}} (AFN)</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </center>
        </div>
    </div>
</div>

<!-- src="https://code.jquery.com/jquery-3.6.0.min.js" -->

<script src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#filterBtn').on('click', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('seles-item.filter') }}",
                method: "POST",
                data: $('#filterForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log("✅ AJAX request successful");
                    console.log("📦 Response data:", response);

                    let rows = '';
                    let i = 1;
                    let totalQty = 0;
                    let totalPrice = 0;

                    if (response.data.length === 0) {
                        rows = `<tr><td colspan="7" class="text-center text-danger">هیچ نتیجه‌ای یافت نشد</td></tr>`;
                    } else {
                        response.data.forEach(item => {
                            rows += `<tr>
                            <td>${i++}</td>
                            <td>${item.product?.ProductName ?? 'N/A'}</td>
                            <td>${item.salse_inoice?.customer?.CustomerName ?? 'N/A'}</td>
                            <td>${item.PersianInvoiceDate ?? 'N/A'}</td>
                            <td>${item.Quantity}</td>
                            <td>${item.product?.Unit ?? 'N/A'}</td>
                            <td>${item.TotalPrice}</td>
                        </tr>`;
                            totalQty += parseFloat(item.Quantity);
                            totalPrice += item.TotalPrice;
                        });

                        rows += `<tr>
                        <th colspan="4" class="text-center">مجموعه :</th>
                        <th>${totalQty}</th>
                        <th></th>
                        <th>${totalPrice} (AFN)</th>
                    </tr>`;
                    }

                    $('tbody').html(rows);
                },
                error: function(xhr, status, error) {
                    console.error("❌ AJAX error:", xhr.responseText);
                    alert("AJAX failed: " + xhr.status + " - " + error);
                }
            });
        });
    });
</script>



@endsection