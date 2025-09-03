@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header ">
        لیست مشتریان

        <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#exampleModal">اضافه کردن مشتری</button>

    </div>
    <div class="card-body">
        <div class="mx-auto col-md-12">
            <center>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>آدی</th>
                                <th>نام مشتری</th>
                                <th>تلفن نمبر</th>
                                <th>آدرس</th>
                                <th colspan="3" class="text-center">ععلکرد</th>

                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i=1;
                            @endphp
                            @foreach($customers as $customer)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{$customer->CustomerName}}</td>
                                <td>{{$customer->Phone}}</td>
                                <td>{{$customer->Address}}</td>
                                <td style="width:1%;">
                                    <!-- Trigger modal with a unique ID per customer -->
                                    <button class="btn btn-danger btn-sm delete-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-name="{{ $customer->CustomerName }}"
                                        data-url="{{ route('customer.delete', ['id' => $customer->id]) }}">
                                        حذف
                                    </button>
                                </td>
                                <td style="width:1%;">
                                    <button class="btn btn-success btn-sm edit-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        data-id="{{$customer->id}}"
                                        data-name="{{$customer->CustomerName}}"
                                        data-phone="{{$customer->Phone}}"
                                        data-address="{{$customer->Address}}"
                                        data-url="{{ route('customer.update', ['customer' => $customer->id]) }}">
                                        ویرایش
                                    </button>
                                </td>
                                <td style="width:1%;">
                                    <a href="{{ route('customer-ledger', ['id' => $customer->id]) }}" class=" btn btn-sm btn-info">
                                        صورت_حساب
                                    </a>
                                </td>


                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </center>
        </div>
    </div>
</div>


<!-- Create model -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">اضافه کردن مشتری</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">


                <form class="needs-validation" action="{{route('customer.store')}}" method="post" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">نام مشتری</label>
                        <input type="text" class="form-control" name="CustomerName" id="recipient-name" placeholder="نام مشتری">

                    </div>

                    <div class="mb-3">
                        <label for="message-text" class="col-form-label">شماره تماس</label>
                        <input type="text" name="Phone" class="form-control" id="recipient-name" placeholder=" شماره تماس">
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label">آدرس مشتری</label>
                        <textarea class="form-control" name="Address" id="message-text" placeholder="آدرس "></textarea>

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
                <h1 class="modal-title fs-5" id="editModalLabel">ویرایش مشتری</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label class="col-form-label">نام مشتری</label>
                        <input type="text" class="form-control" name="CustomerName" placeholder="نام مشتری" id="edit-name">
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">شماره تماس</label>
                        <input type="text" class="form-control" name="Phone" placeholder="شماره تماس" id="edit-phone">
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">آدرس مشتری</label>
                        <textarea class="form-control" name="Address" placeholder="آدرس " id="edit-address"></textarea>
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
            const phone = this.getAttribute('data-phone');
            const address = this.getAttribute('data-address');
            const url = this.getAttribute('data-url');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-phone').value = phone;
            document.getElementById('edit-address').value = address;
            document.getElementById('editForm').action = url;
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