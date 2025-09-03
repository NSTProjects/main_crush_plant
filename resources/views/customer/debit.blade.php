@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center d-print-none ">
        <h1> لیست مشتریان قرضدار
        </h1>
        <button class="btn btn-sm btn-primary" onclick="window.print()">
            چاپ صفحه
        </button>

    </div>
    <div class="card-body">
        <div class="mx-auto col-md-12">
            <center>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover w-100">
                        <thead>
                            <tr>

                                <th class="text-center" colspan="6">لیست مشتریان قرضدار </th>
                            </tr>
                            <tr>
                                <th>نام مشتری</th>
                                <th>تلفن نمبر</th>
                                <th>آدرس</th>
                                <th> قرض</th>
                                <th> رسید</th>
                                <th> بیلانس</th>

                            </tr>
                        </thead>
                        <tbody>

                            @php
                            $sumDebit = 0;
                            $sumCredit = 0;
                            @endphp

                            @foreach($customerDebits as $entry)
                            @php
                            $sumDebit += $entry['total_debit'];
                            $sumCredit += $entry['total_credit'];
                            @endphp
                            <tr>
                                <td>{{ $entry['customer']->CustomerName }}</td>
                                <td>{{ $entry['customer']->Phone }}</td>
                                <td>{{ $entry['customer']->Address }}</td>
                                <td>{{ $entry['total_debit'] }}</td>
                                <td>{{ $entry['total_credit'] }}</td>
                                <td class="text-info">{{ $entry['net_total'] * -1 }} <span style="float: left;">AFN</span></td>
                            </tr>
                            @endforeach
                            <tr class="table-secondary">
                                <th colspan="3" class="text-center">مجموع</th>
                                <th>{{ $sumDebit }}</th>
                                <th>{{ $sumCredit }}</th>
                                <th class="text-info">{{ ($sumCredit - $sumDebit) * -1 }} <span style="float: left;">AFN</span></th>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </center>
        </div>
    </div>
</div>

@endsection