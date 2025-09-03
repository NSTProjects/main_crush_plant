@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header d-print-none">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0">لیست اجناس</h5>
            <div class="btn-group mt-2 mt-md-0">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    اضافه کردن اجناس
                </button>
                <button onclick="window.print()" class="btn btn-outline-primary">
                    چاپ جدول
                </button>
                <button onclick="exportTableToExcel()" class="btn btn-outline-primary">
                    اکسل
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="mx-auto col-md-12">
            <center>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover w-100">
                        <thead>
                            <tr>
                                <th colspan="12" class="text-center">لیست اجناس موجود</th>
                            </tr>
                            <tr>
                                <th rowspan="2">آدی</th>
                                <th rowspan="2">نام</th>
                                <th colspan="4" class="text-center">مقادر M/3</th>
                                <th rowspan="2">قیمت</th>
                                <th rowspan="2">مجموعه</th>
                                <th rowspan="2" class="d-print-none">Action</th>
                            </tr>
                            <tr>
                                <th> اولیه</th>
                                <th>آورده گی</th>
                                <th> فروش</th>
                                <th> موجود</th>

                            </tr>
                        </thead>
                        <tbody>
                            @php $temp = 0; @endphp

                            @foreach($products as $product)
                            @php
                            $deliveriesQty = $deliveries[$product->id] ?? 0;
                            $salesQty = $salesInvoiceItems[$product->id] ?? 0;
                            $currentStock = $product->OpenStock + $deliveriesQty - $salesQty;
                            $stockValue = $product->UnitPrice * $currentStock;
                            $temp += $stockValue;
                            @endphp

                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->ProductName }}</td>
                                <td>{{ $product->OpenStock }}</td>
                                <td>{{ $deliveriesQty }}</td>
                                <td>{{ $salesQty }}</td>
                                <td>{{ $currentStock }} ({{ $product->Unit }})</td>
                                <td>{{ $product->UnitPrice }}</td>
                                <td>{{ number_format($stockValue, 0) }}</td>
                                <td class="d-print-none">
                                    <button href="#" class="delete-btn btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-name="{{ $product->ProductName }}"
                                        data-url="{{ route('product.delete', ['id' => $product->id]) }}">
                                        حذف
                                    </button>
                                    <button href="#" class="edit-btn btn btn-success btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->ProductName }}"
                                        data-openStock="{{ $product->OpenStock }}"
                                        data-unit="{{ $product->Unit }}"
                                        data-unitPrice="{{ $product->UnitPrice }}"
                                        data-url="{{ route('product.update', ['product' => $product->id]) }}">
                                        ویرایش
                                    </button>
                                </td>
                            </tr>
                            @endforeach

                            <tr class="table-success">
                                <th colspan="7" class="text-end">مجموعه کل ارزش موجودی:</th>
                                <th class="fw-bold">{{ number_format($temp, 0) }} <span style="float: left;"> AFN</span></th>
                                <th class="d-print-none"></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </center>
        </div>
    </div>
</div>

<script>
    function exportTableToExcel() {
        const table = document.querySelector("table");
        const workbook = XLSX.utils.table_to_book(table, {
            sheet: "Products"
        });
        XLSX.writeFile(workbook, "products.xlsx");
    }
</script>
<!-- Create model -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">اضافه کردن اجناس</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">


                <form class="needs-validation" action="{{route('product.store')}}" method="post" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">نام جنس</label>
                        <input type="text" class="form-control" name="ProductName" id="recipient-name" placeholder="نام محصول">
                    </div>

                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">موجودی اولیه</label>
                        <input type="number" class="form-control" step="2" value="0" name="OpenStock" id="recipient-name" placeholder="موجودی اولیه">
                    </div>

                    <div class="mb-3">
                        <label for="message-text" class="col-form-label">واحد</label>
                        <input type="text" name="Unit" class="form-control" id="recipient-name" placeholder="واحد">
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label"> قیمت فی واحد</label>
                        <input type="number" name="UnitPrice" class="form-control" id="recipient-name" placeholder="قیمت">
                    </div>
                    <div class="md-3">
                        <button class="btn btn-primary" type="submit">ذخیره کردن</button>
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
                <h1 class="modal-title fs-5" id="editModalLabel">ویرایش جنس</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit-id">

                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">نام جنس</label>
                        <input type="text" class="form-control" name="ProductName" id="edit-name" placeholder="نام محصول">
                    </div>

                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">موجودی اولیه</label>
                        <input type="number" class="form-control" step="2" value="0" name="OpenStock" id="edit-openStock" placeholder="موجودی اولیه">
                    </div>

                    <div class="mb-3">
                        <label for="message-text" class="col-form-label">واحد</label>
                        <input type="text" name="Unit" class="form-control" id="edit-unit" placeholder="واحد">
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label"> قیمت فی واحد</label>
                        <input type="number" name="UnitPrice" class="form-control" id="edit-unitPrice" placeholder="قیمت">
                    </div>
                    <div class="md-3">
                        <button class="btn btn-primary" type="submit">ذخیره تغییرات</button>
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
            const name = this.getAttribute('data-name');
            const openStock = this.getAttribute('data-openStock');
            const unit = this.getAttribute('data-unit');
            const unitPrice = this.getAttribute('data-unitPrice');
            const url = this.getAttribute('data-url');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-openStock').value = openStock;
            document.getElementById('edit-unit').value = unit;
            document.getElementById('edit-unitPrice').value = unitPrice;
            document.getElementById('editForm').action = url;
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

            document.getElementById('delete-message').textContent = `آیا مطمئن هستید که می‌خواهید ${name} را حذف کنید؟`;
            document.getElementById('delete-confirm-link').href = url;
        });
    });
</script>

@endsection