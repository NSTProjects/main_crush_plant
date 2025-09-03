@extends('layouts.master')

@section('content')

<div class="card">




    <div class="card-header d-print-none">
        <dev class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3>لیست بل فروشات</h3>

            <form method="GET" action="{{ route('sales-invoice.index') }}" class="d-flex align-items-center flex-wrap gap-2">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" class="form-control usage1" id="start_date" name="start_date" placeholder="تاریخ شروع" readonly>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control usage1" id="end_date" name="end_date" placeholder="تاریخ پایان" readonly>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">فیلتر</button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            بل جدید
                        </button>
                        <button type="button" class="btn btn-outline-dark  print-btn">
                            <i class="fa fa-print me-1"></i> چاپ
                        </button>
                    </div>
                </div>
            </form>
        </dev>

    </div>
    <div class="card-body">
        <div class="mx-auto col-md-12">
            <center>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th colspan="12" class="text-center">لیست بل فروشات</th>
                            </tr>
                            <tr>
                                <th>آدی</th>
                                <th>تاریخ بل</th>
                                <th> نام مشتری</th>
                                <th>توضیحات </th>
                                <th>جمله مبلغ بل</th>
                                <th>فصدی تخفیف</th>
                                <th>پول دریافت شد </th>
                                <th>قرض</th>
                                <th class="d-print-none text-center" colspan="4">ععلکرد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesInvoices as $salesInvoice)
                            <tr>
                                <td>{{$salesInvoice->id}}</td>
                                <td>{{$salesInvoice->DateInvoice}}</td>
                                <td>{{ $salesInvoice->customer?->CustomerName ?? 'N/A' }}</td>
                                <td>{{$salesInvoice->Description}}</td>
                                <td>{{$salesInvoice->TotalAmount}}</td>
                                <td>{{$salesInvoice->DiscountAmount}}</td>
                                <td>{{$salesInvoice->RecievedAmount}}</td>
                                <td>{{$salesInvoice->BalanceAmount}}</td>
                                <td class="d-print-none" style="width: 1%;">
                                    <!-- Trigger modal with a unique ID per salesInvoice -->
                                    <button href="#" class="btn btn-danger btn-sm delete-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-name="{{ $salesInvoice->TotalAmount }}"
                                        data-url="{{ route('salesInvoice.delete', ['id' => $salesInvoice->id]) }}">
                                        حذف
                                    </button>
                                </td>
                                <td class="d-print-none" style="width: 1%;">
                                    <button href="#" class="btn btn-success btn-sm edit-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        data-id="{{ $salesInvoice->id }}"
                                        data-date="{{ $salesInvoice->DateInvoice }}"
                                        data-customerID="{{ $salesInvoice->CustomerID }}"
                                        data-totalAmount="{{ $salesInvoice->TotalAmount }}"
                                        data-discountAmount="{{ $salesInvoice->DiscountAmount }}"
                                        data-recievedAmount="{{ $salesInvoice->RecievedAmount }}"
                                        data-balanceAmount="{{ $salesInvoice->BalanceAmount }}"
                                        data-description="{{ $salesInvoice->Description }}"
                                        data-url="{{ route('sales-invoice.update', $salesInvoice->id) }}"
                                        data-items='{{$salesInvoiceItems}}'>
                                        ویرایش
                                    </button>
                                </td>
                                <td class="d-print-none" style="width: 1%;">
                                    <button class="view-btn btn btn-sm btn-info"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewModal"
                                        data-id="{{ $salesInvoice->id }}"
                                        data-date="{{ $salesInvoice->DateInvoice }}"
                                        data-customer="{{ $salesInvoice->customer?->CustomerName ?? 'N/A' }}"
                                        data-totalAmount="{{ $salesInvoice->TotalAmount }}"
                                        data-discountAmount="{{ $salesInvoice->DiscountAmount }}"
                                        data-recievedAmount="{{ $salesInvoice->RecievedAmount }}"
                                        data-balanceAmount="{{ $salesInvoice->BalanceAmount }}"
                                        data-description="{{ $salesInvoice->Description }}"
                                        data-url="{{ route('sales-invoice.update', $salesInvoice->id) }}"
                                        data-items='{{$salesInvoiceItems}}'>
                                        نمایش
                                    </button>
                                </td>
                                <td class="d-print-none" style="width: 1%;">
                                    <a href="{{ route('sales-invoice.print', ['id' => $salesInvoice->id]) }}" class="btn btn-sm btn-primary">چاپ</a>

                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <th colspan="4">مجموعه:</th>
                                <th> {{ $salesInvoices->sum('TotalAmount') }}</th>
                                <th> {{ $salesInvoices->sum('DiscountAmount') }}</th>
                                <th> {{ $salesInvoices->sum('RecievedAmount') }}</th>
                                <th> {{ $salesInvoices->sum('BalanceAmount') }} (AFN)</th>
                                <th class="d-print-none" colspan="4"></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </center>
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






<!-- Edit Modal -->


<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form method="POST" id="editForm">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ویرایش بل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-id" name="InvoiceID">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>تاریخ</label>
                            <!-- <input type="date" id="edit-date" name="InvoiceDate" class="form-control"> -->
                            <input type="text" class="usage-edit form-control" id="edit-date" name="InvoiceDate" readonly />

                        </div>
                        <div class="col-md-4">
                            <label>مشتری</label>
                            <select id="edit-customerID" name="CustomerID" class="form-control">
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->CustomerName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>توضیحات</label>
                            <input type="text" id="edit-description" name="Description" class="form-control">
                        </div>
                    </div>

                    <div id="edit-invoice-items"></div>

                    <button type="button" id="add-edit-row" class="btn btn-primary mt-3">افزودن جنس</button>

                    <div class="row mt-4">
                        <div class="col-md-3">
                            <label>مجموع کل</label>
                            <input type="number" id="edit-totalAmount" name="TotalAmount" class="form-control main-total-price" readonly>
                        </div>
                        <div class="col-md-3">
                            <label>تخفیف</label>
                            <input type="number" id="edit-discountAmount" name="DiscountAmount" class="form-control discount">
                        </div>
                        <div class="col-md-3">
                            <label>دریافتی</label>
                            <input type="number" id="edit-recievedAmount" name="RecievedAmount" class="form-control recived">
                        </div>
                        <div class="col-md-3">
                            <label>باقی‌مانده</label>
                            <input type="number" id="edit-balanceAmount" name="BalanceAmount" class="form-control balanceAmount" readonly>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">ذخیره تغییرات</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Fill other fields
            document.getElementById('edit-id').value = this.getAttribute('data-id');
            document.getElementById('edit-date').value = this.getAttribute('data-date');
            document.getElementById('edit-customerID').value = this.getAttribute('data-customerID');
            document.getElementById('edit-totalAmount').value = this.getAttribute('data-totalAmount');
            document.getElementById('edit-discountAmount').value = this.getAttribute('data-discountAmount');
            document.getElementById('edit-recievedAmount').value = this.getAttribute('data-recievedAmount');
            document.getElementById('edit-balanceAmount').value = this.getAttribute('data-balanceAmount');
            document.getElementById('edit-description').value = this.getAttribute('data-description');
            document.getElementById('editForm').action = this.getAttribute('data-url');
            // Get the data-items attribute and parse it
            let tests = this.getAttribute('data-items');
            tests = JSON.parse(tests);

            // Get the target InvoiceID from data-id
            const targetInvoiceID = parseInt(this.getAttribute('data-id'));

            // Filter the items by InvoiceID
            const items = tests.filter(test => test.InvoiceID === targetInvoiceID);


            // const items = JSON.parse(this.getAttribute('data-items'));
            const container = document.getElementById('edit-invoice-items');
            container.innerHTML = '';

            items.forEach((item, index) => {
                const card = document.createElement('div');
                card.className = 'card p-3 mb-2 border border-secondary';
                card.innerHTML = `
                <div class="row g-2">
                    <div class="col-md-2">
                    
                        <select name="items[${index}][ProductID]" class="form-control product-select" required>
                            <option value="${item.ProductID}" selected>${item.product.ProductName}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[${index}][Quantity]" class="form-control quantity" value="${item.Quantity}">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[${index}][UnitPrice]" class="form-control unit-price" value="${item.UnitPrice}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control unit-name" value="${item.product.Unit}" readonly>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[${index}][TotalPrice]" class="form-control total-price" value="${item.TotalPrice}" readonly>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger w-100 remove-row">حذف</button>
                    </div>
                </div>
            `;
                container.appendChild(card);
            });




            let editRowIndex = 1000; // Start high to avoid name conflicts

            document.getElementById('add-edit-row').addEventListener('click', function() {
                const container = document.getElementById('edit-invoice-items');
                const card = document.createElement('div');
                card.className = 'card p-3 mb-2 border border-secondary';
                card.innerHTML = `
        <div class="row g-2">
            <div class="col-md-2">
               

                 <select name="items[${editRowIndex}][ProductID]" class="form-control product-select" required>
                                        <option value="">انتخاب جنس</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->UnitPrice }}" data-unit="{{ $product->Unit }}">
                                            {{ $product->ProductName }}
                                        </option>
                                        @endforeach
                                    </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${editRowIndex}][Quantity]" class="form-control quantity" value="">
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${editRowIndex}][UnitPrice]" class="form-control unit-price" value="">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control unit-name" value="" readonly>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${editRowIndex}][TotalPrice]" class="form-control total-price" value="" readonly>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger w-100 remove-row">حذف</button>
            </div>
        </div>
    `;
                container.appendChild(card);
                editRowIndex++;
            });

            // Remove button
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row')) {
                    const card = e.target.closest('.card');
                    const container = document.getElementById('edit-invoice-items');
                    if (container.querySelectorAll('.card').length > 1) {
                        card.remove();
                        calculateEditTotals(); // Recalculate after removal
                    }
                }
            });

            // Live calucuate 
            function calculateEditTotals() {
                let grandTotal = 0;
                document.querySelectorAll('#edit-invoice-items .total-price').forEach(input => {
                    grandTotal += parseFloat(input.value) || 0;
                });
                document.getElementById('edit-totalAmount').value = grandTotal;

                const discount = parseFloat(document.getElementById('edit-discountAmount').value) || 0;
                const received = parseFloat(document.getElementById('edit-recievedAmount').value) || 0;
                const balance = grandTotal - (discount + received);
                document.getElementById('edit-balanceAmount').value = balance;
            }
            document.addEventListener('input', function(e) {
                if (
                    e.target.closest('#edit-invoice-items') &&
                    (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price'))
                ) {
                    const card = e.target.closest('.card');
                    const quantity = parseFloat(card.querySelector('.quantity').value) || 0;
                    const unitPrice = parseFloat(card.querySelector('.unit-price').value) || 0;
                    const total = quantity * unitPrice;
                    card.querySelector('.total-price').value = total;
                }

                if (
                    e.target.closest('#editModal') &&
                    (e.target.classList.contains('quantity') ||
                        e.target.classList.contains('unit-price') ||
                        e.target.classList.contains('discount') ||
                        e.target.classList.contains('recived'))
                ) {
                    calculateEditTotals();
                }
            });

            $(".usage-edit").persianDatepicker({
                // selectedBefore: !0
            });
        });
    });
</script>
<!-- view Part -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header pt-1 pb-1">
                <h5 class="modal-title">نمایش اجناس ها</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="view-invoice-items"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function() {
            const totalAmount = this.getAttribute('data-totalAmount');
            const discountAmount = this.getAttribute('data-discountAmount');
            const recievedAmount = this.getAttribute('data-recievedAmount');
            const balanceAmount = this.getAttribute('data-balanceAmount');
            // Parse items
            let tests = this.getAttribute('data-items');
            tests = JSON.parse(tests);
            const targetInvoiceID = parseInt(this.getAttribute('data-id'));
            const items = tests.filter(test => test.InvoiceID === targetInvoiceID);
            // Render as table
            const container = document.getElementById('view-invoice-items');
            container.innerHTML = '';

            const table = document.createElement('table');
            table.className = 'table table-bordered table-striped';

            // Table header
            table.innerHTML = `
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>اجناس</th>
                    <th>مقدار</th>
                    <th> قیمت</th>
                    <th>واحد</th>
                    <th> قیمت کلی</th>
                </tr>
            </thead>
            <tbody>
                ${items.map((item, index) => `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.product.ProductName}</td>
                        <td>${item.Quantity}</td>
                        <td>${item.UnitPrice}</td>
                        <td>${item.product.Unit}</td>
                        <td>${item.TotalPrice}</td>
                    </tr>
                `).join('')}
                <tr>
                <th colspan='5'>مجموعه:</th>
                <th>${totalAmount}</th> 
                </tr>
                <tr>
                <th colspan='5'>تخفیف:</th>
                <th>${discountAmount}</th> 
                </tr>
                <tr>
                <th colspan='5'>دریافت شده:</th>
                <th>${recievedAmount}</th> 
                </tr>
                <tr>
                <th colspan='5'>قرض</th>
                <th>${balanceAmount}</th> 
                </tr>
            </tbody>
        `;
            container.appendChild(table);
        });
    });
</script>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="p-3 modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteModalLabel">حذف جنس</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3 text-center" id="delete-message">آیا مطمئن هستید؟</p>
                <div class="gap-2 d-flex justify-content-center">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">خیر</button>
                    <a class="btn btn-sm btn-danger" id="delete-confirm-link" href="#">بله</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const name = this.getAttribute('data-name');
            const url = this.getAttribute('data-url');
            console.log(url)

            document.getElementById('delete-message').textContent = `آیا مطمئن هستید که می‌خواهید ${name} را حذف کنید؟`;
            document.getElementById('delete-confirm-link').href = url;
            console.log(url)
        });
    });
</script>




@endsection