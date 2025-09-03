@extends('layouts.master')

@section('content')

<div class="card">
    <div class="card-header d-print-none">
        <dev class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3>لیست آورده گی </h3>

            <form method="GET" action="{{ route('delivery.index') }}" class="d-flex align-items-center flex-wrap gap-2">
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

                        <button type="button" class="btn btn-outline-success " data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <i class="fa fa-plus me-1"></i> آورده گی جدید
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
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-80" style=" font-size:14px;">
                    <thead>
                        <tr>
                            <th colspan="9" class="text-center">لیست آورده گی</th>
                        </tr>
                        <tr>
                            <th>آدی</th>
                            <th>تاریخ تحویل</th>
                            <th> نام محصول</th>
                            <th class="d-print-none">توضیحات </th>
                            <th>موتر</th>
                            <th>تعداد موتر</th>
                            <th> m/3 فی موتر </th>
                            <th> تعداد کلی</th>
                            <th class="d-print-none">ععلکرد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveries as $delivery)
                        <tr>
                            <td>{{$delivery->id}}</td>
                            <td>{{$delivery->DateDelivery}}</td>
                            <td>{{ $delivery->product->ProductName ?? 'N/A' }}</td>
                            <td class="d-print-none">{{$delivery->Description}}</td>
                            <td>{{$delivery->Vehicle}}</td>
                            <td>{{ rtrim(rtrim(number_format($delivery->NumOfTrucks, 2, '.', ''), '0'), '.') }}</td>
                            <td>{{ rtrim(rtrim(number_format($delivery->CubicMetersPerTruck, 2, '.', ''), '0'), '.') }}</td>
                            <td>{{ rtrim(rtrim(number_format($delivery->TotalVolume, 2, '.', ''), '0'), '.') }}</td>
                            <td class="d-print-none">
                                <!-- Trigger modal with a unique ID per delivery -->
                                <button class="btn btn-danger btn-sm delete-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"
                                    data-name="{{ $delivery->Vehicle }}"
                                    data-url="{{ route('delivery.delete', ['id' => $delivery->id]) }}">
                                    حذف
                                </button>

                                <button class="btn btn-success btn-sm edit-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="{{$delivery->id}}"
                                    data-date="{{$delivery->DateDelivery}}"
                                    data-productID="{{$delivery->ProductID}}"
                                    data-vehicle="{{$delivery->Vehicle}}"
                                    data-numOfTrucks="{{ rtrim(rtrim(number_format($delivery->NumOfTrucks, 2, '.', ''), '0'), '.')}}"
                                    data-cubicMetersPerTruck="{{rtrim(rtrim(number_format($delivery->CubicMetersPerTruck, 2, '.', ''), '0'), '.')}}"
                                    data-description="{{$delivery->Description}}"
                                    data-url="{{ route('delivery.update', ['delivery' => $delivery->id]) }}">
                                    ویرایش
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td class="d-print-none"></td>
                            <td colspan="4" class="text-end fw-bold">مجموعه:</td>

                            <td class="fw-bold">
                                {{ $deliveries->sum('NumOfTrucks') }}
                            </td>
                            <td class="fw-bold"> {{ $deliveries->sum('CubicMetersPerTruck') }}</td>
                            <td class="fw-bold">
                                {{ $deliveries->sum('TotalVolume') }}
                            </td>
                            <td class="d-print-none"></td>
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

<!-- Create model -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">ثبت آورده گی </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">


                <form action="{{route('delivery.store')}}" method="post">
                    @csrf

                    <div class="mb-3">
                        <label for="product-select" class="form-label">انتخاب سنگ</label>
                        <select name="ProductID" id="product-select" class="form-select" required>
                            <option value="" selected></option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->ProductName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="recipient-name" class="col-form-label">تاریخ</label>
                            <!-- <input type="date" class="form-control" name="DeliveryDate"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" id="recipient-name"> -->
                            <input type="text" class="usage form-control" id="invoiceDate" name="DeliveryDate" placeholder="a text box" style="margin-left:0px;" />

                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="message-text" class="col-form-label">موتر</label>
                            <input type="text" name="Vehicle" class="form-control" required id="recipient-name" placeholder="موتر">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="message-text" class="form-label">تعداد موتر</label>
                            <input type="number" name="NumOfTrucks" class="form-control" id="recipient-name" value="0" min="0" step="any">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="message-text" class="col-form-label"> m/3 بر موتر</label>
                            <input type="number" name="CubicMetersPerTruck" class="form-control" id="recipient-name" value="0" min="0" step="any">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label"> توضیحات</label>
                        <textarea type="text" name="Description" class="form-control" id="recipient-name" placeholder="توضیحات"> </textarea>
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
                <h1 class="modal-title fs-5" id="editModalLabel">ویرایش آورده گی</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label for="edit-productID" class="form-label">انتخاب سنگ</label>
                        <select name="ProductID" id="edit-productID" class="form-control" required>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->ProductName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <!-- <label for="recipient-name" class="col-form-label">تاریخ</label>
                            <input type="date" class="form-control" name="DeliveryDate"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" id="edit-date"> -->

                            <!-- Invoice Date -->
                            <label for="InvoiceDate">تاریخ </label>
                            <!-- <input type="text" class="usage form-control" id="edit-date" name="DeliveryDate" placeholder="a text box" style="margin-left:0px;" /> -->
                            <input type="text" class="usage-edit form-control" id="edit-date" name="DeliveryDate" readonly />

                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="message-text" class="col-form-label">موتر</label>
                            <input type="text" name="Vehicle" class="form-control" required id="edit-vehicle" placeholder="موتر">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="message-text" class="form-label">تعداد موتر</label>
                            <input type="number" name="NumOfTrucks" class="form-control" id="edit-numOfTrucks" value="0" min="0" step="any">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="message-text" class="col-form-label"> m/3 بر موتر </label>
                            <input type="number" name="CubicMetersPerTruck" class="form-control" id="edit-cubicMetersPerTruck" value="0" min="0" step="any">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label"> توضیحات</label>
                        <textarea type="text" name="Description" class="form-control" id="edit-description" placeholder="توضیحات"> </textarea>
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
            const date = this.getAttribute('data-date');
            const productID = this.getAttribute('data-productID');
            const vehicle = this.getAttribute('data-vehicle');
            const numOfTrucks = this.getAttribute('data-numOfTrucks');
            const cubicMetersPerTruck = this.getAttribute('data-cubicMetersPerTruck');
            const description = this.getAttribute('data-description');
            const url = this.getAttribute('data-url');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-date').value = date;
            document.getElementById('edit-productID').value = productID;
            document.getElementById('edit-vehicle').value = vehicle;
            document.getElementById('edit-numOfTrucks').value = numOfTrucks;
            document.getElementById('edit-cubicMetersPerTruck').value = cubicMetersPerTruck;
            document.getElementById('edit-description').value = description;
            document.getElementById('editForm').action = url;
            $(".usage-edit").persianDatepicker({
                // selectedBefore: !0
            });
        });
    });
</script>





<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="p-3 modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteModalLabel">حذف سنگ</h1>
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