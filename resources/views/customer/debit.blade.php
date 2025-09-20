@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center d-print-none ">
        <h1> لیست مشتریان قرضدار </h1>
        <button class="btn btn-sm btn-primary" onclick="window.print()"> چاپ صفحه </button>
    </div>

    <div class="card-body">
        <div class="mx-auto col-md-12">
            <center>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover w-100">
                        <thead>
                            <tr>
                                <th class="text-center" colspan="7">لیست مشتریان قرضدار</th>
                            </tr>
                            <tr>
                                <th>نام مشتری</th>
                                <th>تلفن نمبر</th>
                                <th>آدرس</th>
                                <th>کرنسی</th>
                                <th>قرض</th>
                                <th>رسید</th>
                                <th>بیلانس</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $grandTotals = [];
                            @endphp

                            @foreach($customerDebits as $entry)
                            @foreach($entry['currency_totals'] as $total)
                            @php
                            $currency = $total['currency'];
                            $grandTotals[$currency]['debit'] = ($grandTotals[$currency]['debit'] ?? 0) + $total['total_debit'];
                            $grandTotals[$currency]['credit'] = ($grandTotals[$currency]['credit'] ?? 0) + $total['total_credit'];
                            @endphp
                            <tr>
                                <td>{{ $entry['customer']->CustomerName }}</td>
                                <td>{{ $entry['customer']->Phone }}</td>
                                <td>{{ $entry['customer']->Address }}</td>
                                <td>{{ $currency }}</td>
                                <td>{{ $total['total_debit'] }}</td>
                                <td>{{ $total['total_credit'] }}</td>
                                <td class="text-info">
                                    {{ abs($total['net_total']) }}
                                    <span style="float: left;">{{ $currency }}</span>
                                </td>
                            </tr>
                            @endforeach
                            @endforeach

                            @foreach($grandTotals as $currency => $totals)
                            @php
                            $net = $totals['credit'] - $totals['debit'];
                            @endphp
                            <tr class="table-secondary fw-bold">
                                <th colspan="4" class="text-center">مجموع ({{ $currency }})</th>
                                <th>{{ $totals['debit'] }}</th>
                                <th>{{ $totals['credit'] }}</th>
                                <th class="text-info">
                                    {{ abs($net) }}
                                    <span style="float: left;">{{ $currency }}</span>
                                </th>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </center>
        </div>
    </div>
</div>
@endsection