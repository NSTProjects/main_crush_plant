@extends('layouts.master')

@section('content')
<div class="card">


    <div class="card-header d-print-none">
        <dev class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3> روزنامچه </h3>

            <form method="GET" action="{{ route('journal') }}" class="d-flex align-items-center flex-wrap gap-2">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" class="form-control usage1" id="start_date" name="start_date" placeholder="تاریخ شروع" readonly>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control usage1" id="end_date" name="end_date" placeholder="تاریخ پایان" readonly>
                    </div>

                    <div class="col-md-6 d-flex   justify-content-between align-items-center gap-2 flex-wrap">
                        <button type="submit" class="btn btn-outline-primary ">
                            <i class="fa fa-filter me-1"></i> فیلتر
                        </button>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            بل جدید
                        </button>

                        <button type="button" class="btn btn-primary" onclick="openPersianDatePicker()" data-bs-toggle="modal" data-bs-target="#exampleModal1">پرداخت پول</button>

                        <button type="button" class="btn btn-outline-dark  print-btn">
                            <i class="fa fa-print me-1"></i> چاپ
                        </button>
                    </div>

                </div>
            </form>
        </dev>

    </div>
    <div class="card-body text-center">
        <div class="mx-auto col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100">
                    <thead>
                        <tr>
                            <th colspan="9" class="text-center">مرکز تجارتی فتحیان</th>
                        </tr>
                        <tr>
                            <th colspan="9" class="text-center">روزنامچه</th>
                        </tr>
                        <tr>
                            <th rowspan="2" class="text-center">#</th>
                            <th rowspan="2" class="text-center">نوعیت معامله</th>
                            <th rowspan="2" class="text-center">مرجع</th>
                            <th rowspan="2" class="text-center">نام مشتری</th>
                            <th rowspan="2" class="text-center">تاریخ</th>
                            <th rowspan="2" class="text-center">توضیحات</th>
                            <th colspan="3">معاملات نقد</th>
                        </tr>
                        <tr>
                            <th>آوردگی</th>
                            <th>بردگی</th>
                            <th>کرنسی</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $currencyGroups = $transactions->groupBy('Currency');
                        $i = 1;
                        @endphp

                        @foreach($transactions as $tx)
                        @php
                        $color = $tx->MoneyIn > 0 ? 'text-dark' : 'text-danger';
                        $type = $tx->MoneyIn > 0 ? 'آوردگی' : 'بردگی';

                        $sourceMap = [
                        'sales_invoice' => 'بل فروش',
                        'expense' => 'مصارف',
                        'customer_ledger' => 'صورت حساب مشتری',
                        ];
                        $translatedSource = $sourceMap[$tx->SourceType] ?? $tx->SourceType;

                        $customer = $customers->firstWhere('id', $tx->CustomerID);
                        @endphp
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td class="{{ $color }}">{{ $type }}</td>
                            <td class="{{ $color }}">{{ $translatedSource }}</td>
                            <td class="{{ $color }}">{{ $customer->CustomerName ?? 'نامشخص' }}</td>
                            <td class="{{ $color }}">{{ $tx->JalaliDate }}</td>
                            <td class="{{ $color }}">{{ $tx->Description }}</td>
                            <td class="{{ $color }}">{{ number_format($tx->MoneyIn, 0) }}</td>
                            <td class="{{ $color }}">{{ number_format($tx->MoneyOut, 0) }}</td>
                            <td class="{{ $color }}">{{ $tx->Currency }}</td>
                        </tr>
                        @endforeach

                        @foreach($currencyGroups as $currency => $group)
                        @php
                        $totalIn = $group->sum('MoneyIn');
                        $totalOut = $group->sum('MoneyOut');
                        $balance = $totalIn - $totalOut;
                        @endphp
                        <tr class="table-secondary fw-bold">
                            <th colspan="6" style="text-align: left;">مجموعه ({{ $currency }}):</th>
                            <th>{{ number_format($totalIn, 0) }}</th>
                            <th>{{ number_format($totalOut, 0) }}</th>
                            <th>{{ $currency }}</th>
                        </tr>
                        <tr class="table-secondary fw-bold">
                            <th colspan="6" style="text-align: left;">باقی مانده ({{ $currency }}):</th>
                            <th colspan="2" class="text-info">{{ number_format($balance, 0) }}</th>
                            <th class="text-info">{{ $currency }}</th>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    document.querySelector('.print-btn').addEventListener('click', function() {
        window.print();
    });
</script>

<!-- Create model -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-xl"> <!-- Full-width modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-4" id="exampleModalLabel">اضافه کردن بل فروش</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <div class="modal-body">

                <!-- New bill  -->
                <form action="{{route('sales-invoice.store')}}" method="POST">
                    @csrf
                    <div class="mb-2 row">
                        <!-- Select Customer -->
                        <div class="col-md-6">
                            <label for="customer">نام مشتری</label>
                            <select name="CustomerID" class="form-control" required>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->CustomerName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Invoice Date -->
                        <div class="col-8 col-md-4">
                            <label for="InvoiceDate">تاریخ بل</label>
                            <input type="text" class="usage form-control" id="invoiceDate" name="InvoiceDate" placeholder="a text box" style="margin-left:0px;" />

                        </div>
                        <div class="col-4 col-md-2">
                            <br>
                            <button type="button" class="btn btn-secondary" id="add-row">افزودن </button>

                        </div>

                    </div>

                    <!-- Product Items -->
                    <div id="invoice-items">
                        <div class="p-3 mb-2 border card border-secondary">
                            <div class="row g-2">
                                <div class=" col-8 col-md-4 ">
                                    <label class="form-label">انتخاب جنس</label>
                                    <select name="items[0][ProductID]" class="form-control product-select" required>
                                        <option value="">انتخاب جنس</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->UnitPrice }}" data-unit="{{ $product->Unit }}">
                                            {{ $product->ProductName }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class=" col-4 col-md-1 ">
                                    <label class="form-label">مقدار</label>
                                    <input type="number" name="items[0][Quantity]" class="form-control quantity" required>
                                </div>
                                <div class=" col-6 col-md-1 ">
                                    <label class="form-label">قیمت</label>
                                    <input type="number" name="items[0][UnitPrice]" class="form-control unit-price" required>
                                </div>
                                <div class=" col-6 col-md-1 ">
                                    <label class="form-label">واحد</label>
                                    <input type="text" class="form-control unit-name" readonly>
                                </div>
                                <div class=" col-6 col-md-2 ">
                                    <label class="form-label">قیمت کلی</label>
                                    <input type="number" name="items[0][TotalPrice]" class="form-control total-price" readonly>
                                </div>
                                <div class="col-6 col-md-2 d-flex align-items-end ">
                                    <button type="button" class="btn btn-danger w-100 remove-row">حذف</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 col-md-3">
                            <label class="form-label">مجموعه پول</label>
                            <input type="number" name="TotalPrice" class="form-control main-total-price" readonly>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">تخفیف</label>
                            <input type="number" name="DiscountAmount" class="form-control discount" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">پول دریافت شده</label>
                            <input type="number" name="RecievedAmount" class="form-control recived" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">پول قرض</label>
                            <input type="number" name="BalanceAmount" class="form-control balanceAmount " readonly>
                        </div>
                    </div>

                    <!-- Invoice Summary -->
                    <div class="mt-3">
                        <label>توضیحات</label>
                        <textarea name="Description" class="form-control"></textarea>
                    </div>

                    <button type="submit" class="mt-3 btn btn-primary">ثبت بل</button>
                </form>

            </div>

        </div>
    </div>
</div>

<script>
    document.getElementById('exampleModal').addEventListener('show.bs.modal', function() {
        // Clear all total-price fields
        document.querySelectorAll('.total-price').forEach(input => {
            input.value = '';
        });

        // Clear main total and balance fields
        const mainTotal = document.querySelector('.main-total-price');
        const discount = document.querySelector('.discount');
        const received = document.querySelector('.recived');
        const balance = document.querySelector('.balanceAmount');

        if (mainTotal) mainTotal.value = '';
        if (discount) discount.value = '';
        if (received) received.value = '';
        if (balance) balance.value = '';
    });


    let rowIndex = 1;

    // Add new product card
    document.getElementById('add-row').addEventListener('click', function() {
        const container = document.getElementById('invoice-items');
        const firstCard = container.querySelector('.card');
        const newCard = firstCard.cloneNode(true);

        // Clear input values and update name attributes
        [...newCard.querySelectorAll('input, select')].forEach(el => {
            const name = el.getAttribute('name');
            if (name) {
                const newName = name.replace(/\d+/, rowIndex);
                el.setAttribute('name', newName);
            }
            if (el.tagName === 'INPUT') el.value = '';
        });

        container.appendChild(newCard);
        rowIndex++;
    });

    // take Unit Price from Product


    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            setTimeout(() => {
                const selectedOption = e.target.selectedOptions[0];
                const price = selectedOption.getAttribute('data-price');
                const unit = selectedOption.getAttribute('data-unit');
                const card = e.target.closest('.card');
                const unitPriceInput = card.querySelector('.unit-price');
                const unitInput = card.querySelector('.unit-name');

                if (unitPriceInput && price) {
                    unitPriceInput.value = price;
                }

                if (unitInput && unit) {
                    unitInput.value = unit;
                }
            }, 10); // تأخیر کوتاه برای اطمینان از تغییر مقدار
        }
    });


    function calculateTotals() {
        // مجموع کل قیمت‌ها
        let grandTotal = 0;
        document.querySelectorAll('.total-price').forEach(input => {
            grandTotal += parseFloat(input.value) || 0;
        });
        document.querySelector('.main-total-price').value = grandTotal;

        // محاسبه پول قرض
        const discount = parseFloat(document.querySelector('.discount').value) || 0;
        const received = parseFloat(document.querySelector('.recived').value) || 0;
        const balance = grandTotal - (discount + received);
        document.querySelector('.balanceAmount').value = balance;
    }

    // محاسبه قیمت هر ردیف و سپس فراخوانی تابع اصلی
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
            const card = e.target.closest('.card');
            const quantity = parseFloat(card.querySelector('.quantity').value) || 0;
            const unitPrice = parseFloat(card.querySelector('.unit-price').value) || 0;
            const total = quantity * unitPrice;
            card.querySelector('.total-price').value = total;
        }

        // در هر تغییر، محاسبه کلی انجام شود
        calculateTotals();
    });



    // Remove product card
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            const card = e.target.closest('.card');
            const container = document.getElementById('invoice-items');
            if (container.querySelectorAll('.card').length > 1) {
                card.remove();
            }
        }
    });
</script>

<!-- Modal for Payment -->
<div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">پرداخت جدید</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('customer.storeLedger') }}" method="post" novalidate>
                    @csrf
                    <input type="hidden" name="CustomerID" value="{{ $customer->id }}" />
                    <div class="row">

                        <div class="col-md-6">
                            <label for="customer">نام مشتری</label>
                            <select name="CustomerID" class="form-control" required>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->CustomerName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="LedgerDate" class="form-label">تاریخ</label>
                            <input type="text" class="usage form-control" id="LedgerDate" name="LedgerDate" placeholder="a text box" style="margin-left:0px;" />
                        </div>


                    </div>


                    <div class="row">
                        <div class="col-6">
                            <label for="Amount" class="form-label">مبلغ</label>
                            <input type="number" name="Amount" class="form-control" id="Amount" min="0">
                        </div>
                        <div class="col-6">
                            <label for="Currency" class="form-label">کرنسی </label>
                            <select name="Currency" class="form-control" id="Currency">
                                <option value="AFN">AFN</option>
                                <option value="USD">USD</option>
                                <option value="KPR">KPR</option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label for="ReferenceID" class="form-label">بل نمبر</label>
                            <input type="number" name="ReferenceID" class="form-control" id="ReferenceID" min="0" value="0">
                        </div>

                        <div class="col-6">
                            <label class="form-label">نوعیت پول</label>
                            <div class="row">
                                <div class="form-check col-6">
                                    <input class="form-check-input" type="radio" name="ReferenceType" id="brought" value="payment_in" checked>
                                    <label class="form-check-label" for="brought">آوردگی</label>
                                </div>
                                <div class="form-check col-6">
                                    <input class="form-check-input" type="radio" name="ReferenceType" id="taken" value="payment_out">
                                    <label class="form-check-label" for="taken">بردگی</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-9">
                            <label for="Description" class="form-label">توضیحات</label>
                            <textarea class="form-control" name="Description" id="Description" placeholder="آدرس یا توضیحات بیشتر"></textarea>
                        </div>

                        <div class="col-3">
                            <br>
                            <br>

                            <button class="btn btn-success btn-sm" type="submit">ذخیره کردن</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection