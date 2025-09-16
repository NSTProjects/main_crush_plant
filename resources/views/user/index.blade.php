@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header ">

        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0"> لیست استفاده کننده ها </h5>
            <div class="btn-group mt-2 mt-md-0">
                @php
                use Illuminate\Support\Facades\Auth;
                @endphp

                @if (Auth::user()->role === 'admin')
                <div class="btn-group mt-2 mt-md-0">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        استفاده کننده جدید
                    </button>
                </div>
                @endif
            </div>


        </div>
        <div class="card-body">
            <div class="mx-auto col-md-12">
                <center>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th colspan="6" class="text-center">لیست اجناس فروخته شده</th>
                                </tr>
                                <tr>
                                    <th>#</th>
                                    <th> نام </th>
                                    <th> ایمیل</th>
                                    <th>رول</th>
                                    <th>حذف </th>
                                    <th>ویرایش </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role === 'admin' ? 'مدیر' : 'کاربر' }}</td>


                                    <td>
                                        @if (Auth::id() !== $user->id)
                                        <button href="#" class="delete-btn btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-name="{{ $user->name }}"
                                            data-url="{{ route('user.delete', ['id' => $user->id]) }}">
                                            حذف
                                        </button>
                                        @else
                                        <button class="btn btn-danger btn-sm" disabled>
                                            حذف (غیرفعال)
                                        </button>
                                        @endif
                                    </td>
                                    <td>
                                        <button href="#" class="edit-btn btn btn-success btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->role }}"
                                            data-url="{{ route('user.update', ['id' => $user->id]) }}">
                                            ویرایش
                                        </button>
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
</div>
<!-- Create model -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">اضافه کردن استفاده کننده جدید</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">


                <form class="needs-validation" action="{{route('user.store')}}" method="post" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">نام </label>
                        <input type="text" class="form-control" name="name" id="recipient-name" placeholder="نام ">
                    </div>



                    <div class="mb-3">
                        <label for="message-text" class="col-form-label">ایمیل</label>
                        <input type="text" name="email" class="form-control" id="recipient-name" placeholder="ایمیل">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="col-form-label">نقش کاربر</label>
                        <select name="role" id="role" class="form-control">
                            <option value="admin">مدیر</option>
                            <option value="user">کاربر</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label"> رمز</label>
                        <input type="password" name="password" class="form-control" id="recipient-name">
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
                <h1 class="modal-title fs-5" id="editModalLabel">ویرایش استفاده کننده دیتابس</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit-id">

                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">نام </label>
                        <input type="text" class="form-control" name="name" id="edit-name" placeholder="نام محصول">
                    </div>

                    <div class="mb-3 ">
                        <label for="recipient-name" class="col-form-label"> ایمیل</label>
                        <input type="email" class="form-control" name="email" id="edit-email" placeholder="موجودی اولیه">
                    </div>
                    @if (Auth::user()->role === 'admin')
                    <div class="mb-3">
                        <label for="role" class="col-form-label">نقش کاربر</label>
                        <select name="role" id="edit-role" class="form-control">
                            <option value="admin">مدیر</option>
                            <option value="user">کاربر</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="message-text" class="col-form-label"> رمز</label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password">
                    </div>
                    @endif
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
            const email = this.getAttribute('data-email');
            const role = this.getAttribute('data-role');
            const url = this.getAttribute('data-url');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-role').value = role;
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