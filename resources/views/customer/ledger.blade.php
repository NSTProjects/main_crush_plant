@extends('layouts.master')
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/persianDatepicker-default.css') }}">
@endsection



@section('content')

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center d-print-none">
        <h5 class="mb-0">صورت حساب مشتری</h5>
        <form id="filterForm" class="row g-2">

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
        <div>
            <button type="button" class="btn btn-secondary me-2" onclick="window.print()">پرینت</button>
            <button type="button" class="btn btn-primary" onclick="openPersianDatePicker()" data-bs-toggle="modal" data-bs-target="#exampleModal">پرداخت پول</button>
        </div>
    </div>

    <div class="card-body" style="font-size: 14px;">
        <div class="row mb-4">
            @php
            $creditTotal = $ledgers->where('TransactionType', 'Credit')->sum('Amount');
            $debitTotal = $ledgers->where('TransactionType', 'Debit')->sum('Amount');
            $netTotal = $debitTotal - $creditTotal;
            $runningBalances = ['AFN' => 0, 'USD' => 0, 'PKR' => 0]; // Add more currencies as needed

            @endphp

            <div class="col-md-12">
                <div class="table-responsive">
                    <h5 class="text-center mb-3">صورت‌حساب مشتری</h5>
                    <table class="table table-bordered table-striped text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>شماره</th>
                                <th>نام مشتری</th>
                                <th>شماره تماس</th>
                                <th>آدرس</th>
                                <th>تاریخ صدور</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $customer->id }}</td>
                                <td>{{ $customer->CustomerName }}</td>
                                <td>{{ $customer->Phone }}</td>
                                <td>{{ $customer->Address }}</td>
                                <td style="font-size: 14px;">{{ \Morilog\Jalali\Jalalian::now()->format('Y/m/d') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mt-4">
                    @php
                    $creditTotal = $ledgers->where('TransactionType', 'Credit')->sum('Amount');
                    $debitTotal = $ledgers->where('TransactionType', 'Debit')->sum('Amount');
                    $netTotal = $debitTotal - $creditTotal;
                    $i = 1;

                    // Group totals by currency
                    $currencyGroups = $ledgers->groupBy('Currency');
                    $currencyTotals = [];

                    foreach ($currencyGroups as $currency => $group) {
                    $credit = $group->where('TransactionType', 'Credit')->sum('Amount');
                    $debit = $group->where('TransactionType', 'Debit')->sum('Amount');
                    $balance = $debit - $credit;

                    $currencyTotals[] = [
                    'currency' => $currency,
                    'credit' => $credit,
                    'debit' => $debit,
                    'balance' => $balance,
                    ];
                    }
                    @endphp

                    <table class="table table-bordered table-sm table-hover text-center align-middle" style="font-size: 14px;">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>نوعیت معامله</th>
                                <th>بل #</th>
                                <th>تاریخ</th>
                                <th>توضیحات</th>
                                <th>آوردگی</th>
                                <th>بردگی</th>
                                <th>بیلانس</th>
                                <th>کرنسی</th>
                                <th class="d-print-none " style="width: 1%;">ویرایش</th>
                                <th class="d-print-none" style="width: 1%;">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="ledger-body">
                            @forelse ($ledgers as $ledger)
                            @php
                            $credit = $ledger->TransactionType === 'Credit' ? $ledger->Amount : 0;
                            $debit = $ledger->TransactionType === 'Debit' ? $ledger->Amount : 0;
                            $color = $ledger->TransactionType === 'Debit' ? 'text-danger' : 'text-dark';

                            $currency = $ledger->Currency;
                            $runningBalances[$currency] = ($runningBalances[$currency] ?? 0) + ($debit - $credit);

                            $refType = $ledger->ReferenceType !== 'invoice' ? 'پول نقد' : 'بل';
                            $type = $ledger->TransactionType === 'Credit' ? 'آوردگی' : 'بردگی';
                            @endphp
                            <tr>
                                <td class="{{ $color }}">{{ $i++ }}</td>
                                <td class="{{ $color }}">{{ $type }}</td>
                                <td class="{{ $color }}">{{ $ledger->ReferenceID }} - {{ $refType }}</td>
                                <td class="{{ $color }}">{{ $ledger->DateLedger }}</td>
                                <td class="{{ $color }}">
                                    {{ $ledger->Description }}
                                    @if ($ledger->ReferenceType === 'invoice' && $salesInvoiceItems->has($ledger->ReferenceID))
                                    <br>
                                    @foreach ($salesInvoiceItems[$ledger->ReferenceID] as $item)
                                    <span class="badge bg-success text-light">
                                        {{ $item->product->ProductName ?? 'محصول نامشخص' }}
                                    </span>
                                    @endforeach
                                    @endif
                                </td>
                                <td class="{{ $color }}">{{ $credit }}</td>
                                <td class="{{ $color }}">{{ $debit }}</td>
                                <td>{{ $runningBalances[$ledger->Currency] }}</td>

                                <td>{{ $ledger->Currency }}</td>
                                <td class="d-print-none" style="width: 1%;">
                                    <button class="btn btn-sm btn-outline-primary" @if($ledger->ReferenceType === 'invoice') disabled @endif>
                                        <a href="#" class="edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-id="{{ $ledger->id }}"
                                            data-ledgerDate="{{ $ledger->DateLedger }}"
                                            data-referenceID="{{ $ledger->ReferenceID }}"
                                            data-referenceType="{{ $ledger->ReferenceType }}"
                                            data-transactionType="{{ $ledger->TransactionType }}"
                                            data-description="{{ $ledger->Description }}"
                                            data-amount="{{ $ledger->Amount }}"
                                            data-currency="{{ $ledger->Currency }}"
                                            data-url="{{ route('customer.updateLedger', ['ledger' => $ledger->id]) }}">
                                            <i class="fa fa-edit"></i> edit
                                        </a>
                                    </button>
                                </td>
                                <td class="d-print-none" style="width: 1%;">
                                    <button class="btn btn-sm btn-outline-danger" @if($ledger->ReferenceType === 'invoice') disabled @endif>
                                        <a href="#" class="delete-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-name="{{ $ledger->Amount }}"
                                            data-url="{{ route('customer.deleteLedger', ['id' => $ledger->id]) }}">
                                            <i class="fa fa-trash text-danger"></i> delete
                                        </a>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11">هیچ معاملات اجرا نشد</td>
                            </tr>
                            @endforelse

                            {{-- Totals grouped by currency --}}
                            @foreach ($currencyTotals as $total)
                            <tr class="fw-bold">
                                <td colspan="7" class="text-end">بردگی ({{ $total['currency'] }}):</td>
                                <td>{{ $total['debit'] }}</td>
                                <td></td>
                                <td colspan="2" class="d-print-none"></td>
                            </tr>
                            <tr class="fw-bold">
                                <td colspan="7" class="text-end">آوردگی ({{ $total['currency'] }}):</td>
                                <td>{{ $total['credit'] }}</td>
                                <td></td>
                                <td colspan="2" class="d-print-none"></td>
                            </tr>
                            <tr class="fw-bold">
                                <td colspan="7" class="text-end">بیلانس ({{ $total['currency'] }}):</td>
                                <td style="color: {{ $total['balance'] >= 0 ? 'green' : 'red' }}">
                                    {{ $total['balance'] >= 0 ? $total['balance'] : (-1*$total['balance'] ) }}
                                </td>
                                <td style="color: {{ $total['balance'] >= 0 ? 'green' : 'red' }}">
                                    <span class="float-start">{{ $total['currency'] }}</span>
                                </td>
                                <td colspan="2" class="d-print-none"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Payment -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" dir="rtl">
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
                        <div class="col-6">
                            <label for="LedgerDate" class="form-label">تاریخ</label>
                            <input type="text" class="usage form-control" id="LedgerDate" name="LedgerDate" placeholder="a text box" style="margin-left:0px;" />
                        </div>

                        <div class="col-6">
                            <label for="Amount" class="form-label">مبلغ</label>
                            <input type="number" name="Amount" class="form-control" id="Amount" min="0">
                        </div>
                    </div>


                    <div class="row">
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
                        <div class="col-6"></div>
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



<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">ویرایش مشتری</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="CustomerID" value="{{ $customer->id }}" />
                    <input type="hidden" name="id" id="edit-id">

                    <div class="row">
                        <div class="col-6">
                            <label for="LedgerDate" class="form-label">تاریخ</label>
                            <input type="text" class="usage form-control" id="edit-ledgerDate" name="LedgerDate" placeholder="a text box" style="margin-left:0px;" />
                        </div>

                        <div class="col-6">
                            <label for="Amount" class="form-label">مبلغ</label>
                            <input type="number" name="Amount" class="form-control" id="edit-amount" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="Currency" class="form-label">کرنسی </label>
                            <select name="Currency" class="form-control" id="edit-currency">
                                <option value="AFN">AFN</option>
                                <option value="USD">USD</option>
                                <option value="KPR">KPR</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="ReferenceID" class="form-label">بل نمبر</label>
                            <input type="number" name="ReferenceID" class="form-control" id="edit-referenceID" min="0" value="0">
                        </div>
                        <div class="col-6"></div>
                        <div class="col-6">
                            <label class="form-label">نوعیت پول</label>
                            <div class="row">
                                <div class="form-check col-6">
                                    <input class="form-check-input" type="radio" name="ReferenceType" id="edit-brought" value="payment_in">
                                    <label class="form-check-label" for="edit-brought">آوردگی</label>
                                </div>
                                <div class="form-check col-6">
                                    <input class="form-check-input" type="radio" name="ReferenceType" id="edit-taken" value="payment_out">
                                    <label class="form-check-label" for="edit-taken">بردگی</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-9">
                            <label for="Description" class="form-label">توضیحات</label>
                            <textarea class="form-control" name="Description" id="edit-description" placeholder="آدرس یا توضیحات بیشتر"></textarea>
                        </div>
                        <div class="col-3">
                            <br>
                            <br>
                            <button class="btn btn-primary btn-sm" type="submit">ذخیره تغییرات</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const ledgerDate = this.getAttribute('data-ledgerDate');
            const referenceID = this.getAttribute('data-referenceID');
            const transactionType = this.getAttribute('data-transactionType'); // 'Credit' or 'Debit'
            const description = this.getAttribute('data-description');
            const amount = this.getAttribute('data-amount');
            const currency = this.getAttribute('data-currency');
            const url = this.getAttribute('data-url');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-ledgerDate').value = ledgerDate;
            document.getElementById('edit-referenceID').value = referenceID;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-currency').value = currency;
            document.getElementById('edit-amount').value = amount;
            document.getElementById('editForm').action = url;
            // ✅ Map transactionType to ReferenceType radio buttons
            if (transactionType === 'Credit') {
                document.getElementById('edit-brought').checked = true;
            } else if (transactionType === 'Debit') {
                document.getElementById('edit-taken').checked = true;
            }
        });
    });
</script>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="p-3 modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteModalLabel">حذف مشتری</h1>
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

            document.getElementById('delete-message').textContent = `آیا مطمئن هستید که می‌خواهید ${name} را حذف کنید؟`;
            document.getElementById('delete-confirm-link').href = url;
        });
    });
</script>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/jquery-1.10.1.min.js') }}"></script>
<script src="{{ asset('assets/js/persianDatepicker.js') }}"></script>

<script>
    $(function() {
        $(".usage, .usage-edit").persianDatepicker({
            format: 'YYYY/MM/DD',
            autoClose: true
        });
    });
</script>



<script src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#filterBtn').on('click', function(e) {
            e.preventDefault();

            $.ajax({
                // url: "{{ route('customer-ledger.filter', ['id' => $customer->id]) }}",
                url: "/customer-ledger/filter/1",

                method: "POST",
                data: $('#filterForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    const ledgers = response.ledgers;
                    const itemsMap = response.salesInvoiceItems || {};
                    let rows = '';
                    let i = 1;
                    let runningBalance = 0;

                    const currencyGroups = {};
                    ledgers.forEach(ledger => {
                        const currency = ledger.Currency;
                        if (!currencyGroups[currency]) {
                            currencyGroups[currency] = [];
                        }
                        currencyGroups[currency].push(ledger);
                    });

                    ledgers.forEach(ledger => {
                        const credit = ledger.TransactionType === 'Credit' ? ledger.Amount : 0;
                        const debit = ledger.TransactionType === 'Debit' ? ledger.Amount : 0;
                        const color = ledger.TransactionType === 'Debit' ? 'text-danger' : 'text-dark';
                        const refType = ledger.ReferenceType !== 'invoice' ? 'پول نقد' : 'بل';
                        const type = ledger.TransactionType === 'Credit' ? 'آوردگی' : 'بردگی';

                        if (ledger.Currency === 'AFN') {
                            runningBalance += debit - credit;
                        }

                        rows += `<tr>
            <td class="${color}">${i++}</td>
            <td class="${color}">${type}</td>
            <td class="${color}">${ledger.ReferenceID} - ${refType}</td>
            <td class="${color}">${ledger.DateLedger}</td>
            <td class="${color}">
                ${ledger.Description ?? ''}
                ${ledger.ReferenceType === 'invoice' && itemsMap[ledger.ReferenceID] ? '<br>' + itemsMap[ledger.ReferenceID].map(item =>
                    `<span class="badge bg-success text-light">${item.product?.ProductName ?? 'محصول نامشخص'}</span>`
                ).join(' ') : ''}
            </td>
            <td class="${color}">${credit}</td>
            <td class="${color}">${debit}</td>
            <td>${runningBalance}</td>
            <td>${ledger.Currency}</td>
            <td class="d-print-none">
                <button class="btn btn-sm btn-outline-primary" ${ledger.ReferenceType === 'invoice' ? 'disabled' : ''}>
                    <a href="#" class="edit-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal"
                        data-id="${ledger.id}"
                        data-ledgerDate="${ledger.DateLedger}"
                        data-referenceID="${ledger.ReferenceID}"
                        data-referenceType="${ledger.ReferenceType}"
                        data-transactionType="${ledger.TransactionType}"
                        data-description="${ledger.Description}"
                        data-amount="${ledger.Amount}"
                        data-currency="${ledger.Currency}"
                        data-url="/customer/update-ledger/${ledger.id}">
                        <i class="fa fa-edit"></i> edit
                    </a>
                </button>
            </td>
            <td class="d-print-none">
                <button class="btn btn-sm btn-outline-danger" ${ledger.ReferenceType === 'invoice' ? 'disabled' : ''}>
                    <a href="#" class="delete-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal"
                        data-name="${ledger.Amount}"
                        data-url="/customer/delete-ledger/${ledger.id}">
                        <i class="fa fa-trash text-danger"></i> delete
                    </a>
                </button>
            </td>
        </tr>`;
                    });

                    // Totals grouped by currency
                    Object.entries(currencyGroups).forEach(([currency, group]) => {
                        const credit = group.filter(l => l.TransactionType === 'Credit').reduce((sum, l) => sum + l.Amount, 0);
                        const debit = group.filter(l => l.TransactionType === 'Debit').reduce((sum, l) => sum + l.Amount, 0);
                        const balance = debit - credit;
                        const balanceColor = balance >= 0 ? 'green' : 'red';

                        rows += `
        <tr class="fw-bold">
            <td colspan="7" class="text-end">بردگی (${currency}):</td>
            <td>${debit}</td>
            <td></td>
            <td colspan="2" class="d-print-none"></td>
        </tr>
        <tr class="fw-bold">
            <td colspan="7" class="text-end">آوردگی (${currency}):</td>
            <td>${credit}</td>
            <td></td>
            <td colspan="2" class="d-print-none"></td>
        </tr>
        <tr class="fw-bold">
            <td colspan="7" class="text-end">بیلانس (${currency}):</td>
            <td style="color: ${balanceColor}">${Math.abs(balance)}</td>
            <td style="color: ${balanceColor}"><span class="float-start">${currency}</span></td>
            <td colspan="2" class="d-print-none"></td>
        </tr>`;
                    });

                    $('#ledger-body').html(rows);;
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