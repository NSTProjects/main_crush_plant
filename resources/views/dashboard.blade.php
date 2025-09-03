@extends('layouts.master')

@section('content')
<div class="container">
    <h2 class="mb-4">📊 داشبورد مدیریتی</h2>

    {{-- کارت‌های آماری --}}
    <div class="row">
        @php
        $cards = [
        ['title' => 'مشتریان', 'value' => $customerCount, 'color' => 'primary'],
        ['title' => 'اجناس', 'value' => $productCount, 'color' => 'success'],
        ['title' => 'بل ها', 'value' => $invoiceCount, 'color' => 'warning'],
        ['title' => 'هزینه‌ها', 'value' => ' (AFN) ' . number_format($expenseTotal, 2), 'color' => 'danger'],
        ];
        @endphp

        @foreach($cards as $card)
        <div class="col-md-3">
            <div class="card text-white bg-{{ $card['color'] }} mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $card['title'] }}</h5>
                    <p class="card-text">{{ $card['value'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- جدول فاکتورهای اخیر --}}
    <h4 class="mt-5">🧾 بل های اخیر</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>تاریخ </th>
                    <th> مشتری</th>
                    <th>مبلغ کل</th>
                    <th>باقی‌مانده</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentInvoices as $invoice)
                <tr>
                    <td>{{ $invoice->InvoiceDate }}</td>
                    <td>{{ $invoice->customer?->CustomerName ?? 'N/A' }}</td>
                    <td>{{ $invoice->TotalAmount }}</td>
                    <td>{{ $invoice->BalanceAmount }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">هیچ فاکتوری ثبت نشده است.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>




    <form action="{{ route('backup.database') }}" method="POST">
        @csrf
        <button type="submit">Backup Database Now</button>
    </form>
</div>
@endsection