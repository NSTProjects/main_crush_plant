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
                            <th colspan="9" class="text-center">مرکز تجارتی فتحیان </th>
                        </tr>
                        <tr>
                            <th colspan="9" class="text-center">روزنامچه</th>
                        </tr>
                        <tr>
                            <th rowspan="2" class="text-center"># </th>
                            <th rowspan="2" class="text-center">نوعیت معامله</th>
                            <th rowspan="2" class="text-center">مرجع</th>
                            <th rowspan="2" class="text-center"> تاریخ </th>
                            <th rowspan="2" class="text-center">توضیحات</th>
                            <th colspan="2">معاملات نقد</th>
                        </tr>
                        <tr>

                            <th>آوردگی</th>
                            <th>بردگی</th>
                        </tr>

                    </thead>
                    <tbody>
                        @php
                        $i = 1;
                        @endphp
                        @foreach($transactions as $tx)
                        <tr>
                            <td>{{$i++}}</td>
                            <td class="{{  $tx->MoneyIn >0  ? 'text-dark' : 'text-danger' }}">
                                {{ $tx->MoneyIn >0  ? 'آوردگی ' : 'بردگی' }}
                            </td>
                            @php
                            $sourceMap = [
                            'sales_invoice' => 'بل فروش',
                            'expense' => 'مصارف',
                            'customer_ledger' => 'صورت حساب مشتری',
                            ];

                            $translatedSource = $sourceMap[$tx->SourceType] ?? $tx->SourceType;
                            @endphp

                            <td class="{{  $tx->MoneyIn >0  ? 'text-dark' : 'text-danger' }}"> {{ $translatedSource }}
                            </td>
                            <td class="{{  $tx->MoneyIn >0  ? 'text-dark' : 'text-danger' }}">{{ $tx->JalaliDate }}</td>
                            <td class="{{  $tx->MoneyIn >0  ? 'text-dark' : 'text-danger' }}">{{ $tx->Description }}</td>
                            <td class="{{  $tx->MoneyIn >0  ? 'text-dark' : 'text-danger' }}">{{ number_format($tx->MoneyIn, 0) }}</td>
                            <td class="{{  $tx->MoneyIn >0  ? 'text-dark' : 'text-danger' }}">{{ number_format($tx->MoneyOut, 0) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <th colspan="5" style="text-align: left;">مجموعه: </th>
                            <th> {{ $transactions->sum('MoneyIn') }}</th>
                            <th>{{ $transactions->sum('MoneyOut') }}</th>
                        </tr>
                        <tr>
                            <th colspan="5" style="text-align: left;">باقی مانده: </th>
                            <th colspan="2" class="text-info"> {{ $transactions->sum('MoneyIn') - $transactions->sum('MoneyOut') }} (AFN)</th>
                        </tr>
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
@endsection