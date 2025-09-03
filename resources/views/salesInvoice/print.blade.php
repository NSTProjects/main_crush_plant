<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>چاپ فاکتور فروش</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            direction: rtl;
            margin: 20px;
        }

        .invoice-box {
            border: 1px solid #ccc;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: center;
        }

        .summary {
            margin-top: 20px;
            text-align: left;
        }

        .print-btn {
            margin-bottom: 20px;
            text-align: center;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="print-btn">
        <button onclick="window.print()">چاپ فاکتور</button>
        <a href="{{ route('sales-invoice.index') }}">برگشت</a>
    </div>

    <div class="invoice-box">
        <h2> شرکت فتحیان</h2>
        <h4 class="text-center">فاکتور فروش</h4>

        <div style="float: left;">
            <p><strong>شماره فاکتور:</strong> {{ $invoice->id }}</p>
            <p><strong>تاریخ:</strong> {{ $invoice->DateInvoice }}</p>
        </div>
        <div style="float: right;">
            <p><strong>مشتری:</strong> {{ $invoice->customer->CustomerName }}</p>
            <p><strong>تلفن:</strong> {{ $invoice->customer->Phone }}</p>
            <p><strong>آدرس:</strong> {{ $invoice->customer->Address }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>نام محصول</th>
                    <th>تعداد</th>
                    <th>واحد</th>
                    <th>قیمت واحد</th>
                    <th>قیمت کل</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->salesInvoiceItem as $item)
                <tr>
                    <td>{{ $item->product->ProductName }}</td>
                    <td>{{ $item->Quantity }}</td>
                    <td>{{ $item->product->Unit }}</td>
                    <td>{{ number_format($item->UnitPrice, 0) }}</td>
                    <td>{{ number_format($item->TotalPrice, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <p><strong>مبلغ کل:</strong> {{ number_format($invoice->TotalAmount, 0) }}</p>
            <p><strong>تخفیف:</strong> {{ number_format($invoice->DiscountAmount, 0) }}</p>
            <p><strong>مبلغ دریافت‌شده:</strong> {{ number_format($invoice->RecievedAmount, 0) }}</p>
            <p><strong>باقی‌مانده:</strong> {{ number_format($invoice->BalanceAmount, 0) }}</p>
            <p style="text-align:right;"><strong>توضیحات:</strong> {{ $invoice->Description }}</p>
        </div>

    </div>

</body>

</html>