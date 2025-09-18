@extends('layouts.master')

@section('content')
<div class="card">


    <div class="card-header d-print-none">
        <dev class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3>لیست مصارف </h3>


            <form id="filterForm" class="row ">


                <div class="col-md-2">
                    <input type="text" class="form-control" name="expense_category" placeholder="نام کتگوری مصرف">
                </div>

                <div class="col-md-2">
                    <input type="text" class="form-control usage1" name="start_date" placeholder="تاریخ شروع" readonly>
                </div>

                <div class="col-md-2">
                    <input type="text" class="form-control usage1" name="end_date" placeholder="تاریخ پایان" readonly>
                </div>

                <div class="col-md-6 d-flex   justify-content-between align-items-center gap-2 flex-wrap">
                    <button type="button" id="filterBtn" class="btn btn-primary">فیلتر</button>

                    <button type="button" class="btn btn-outline-success " data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <i class="fa fa-plus me-1"></i> مصارف جدید
                    </button>

                    <button type="button" class="btn btn-outline-dark  print-btn">
                        <i class="fa fa-print me-1"></i> چاپ
                    </button>
                </div>
            </form>

        </dev>

    </div>
    <div class="card-body">
        <div class="mx-auto col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100">
                    <thead>
                        <tr>
                            <th colspan="9" class="text-center">لیست مصارف</th>
                        </tr>
                        <tr>
                            <th>آدی</th>
                            <th>تاریخ مصرف</th>
                            <th> نوع مصرف</th>
                            <th>توضیحات</th>
                            <th>مبلغ</th>
                            <th class="d-print-none">ععلکرد</th>
                        </tr>
                    </thead>
                    <tbody id="expenseTableBody">
                        @foreach($expenses as $expense)
                        <tr>
                            <td>{{$expense->id}}</td>
                            <td>{{$expense->DateExpense}}</td>
                            <td>{{$expense->ExpenseType}}</td>
                            <td>{{$expense->Description}}</td>
                            <td>{{ rtrim(rtrim(number_format($expense->Amount, 2, '.', ''), '0'), '.') }}</td>
                            <td class="d-print-none">
                                <!-- Trigger modal with a unique ID per expesnse -->
                                <button class="btn btn-danger btn-sm delete-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"
                                    data-name="{{ rtrim(rtrim(number_format($expense->Amount, 2, '.', ''), '0'), '.') }}"
                                    data-url="{{ route('expense.delete', ['id' => $expense->id]) }}">
                                    حذف
                                </button>

                                <button class="btn btn-info btn-sm edit-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="{{$expense->id}}"
                                    data-expenseDate="{{$expense->DateExpense}}"
                                    data-expenseType="{{$expense->ExpenseType}}"
                                    data-amount="{{rtrim(rtrim(number_format($expense->Amount, 2, '.', ''), '0'), '.')}}"
                                    data-description="{{$expense->Description}}"
                                    data-url="{{ route('expense.update', ['expense' => $expense->id]) }}">
                                    ویرایش
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" class="text-end fw-bold">مجموعه:</td>

                            <td class="fw-bold">
                                {{ $expenses->sum('Amount') }} (AFN)
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
                <h1 class="modal-title fs-5" id="exampleModalLabel">اضافه کردن مصرف</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" action="{{route('expense.store')}}" method="post" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">تاریخ مصرف</label>
                        <input type="text" class="usage form-control" id="ExpenseDate" name="ExpenseDate" placeholder="a text box" style="margin-left:0px;" />

                        <!-- <input type="date" class="form-control" name="ExpenseDate" id="recipient-name" placeholder="تاریخ"> -->
                    </div>

                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label"> نوع مصرف</label>
                        <input type="text" class="form-control" name="ExpenseType" id="recipient-name" placeholder="نوع مصرف ">
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label"> مقدار مصرف </label>
                        <input type="number" name="Amount" class="form-control" id="recipient-name" placeholder="مقدار ">
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">توضیحات</label>
                        <textarea class="form-control" name="Description" placeholder="توضیحات " id="recipient-name"></textarea>
                    </div>
                    <div class="md-3">
                        <button class="btn btn-primary" type="submit">ذخیره کردن</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#filterBtn').on('click', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('expense.filter.ajax') }}", // ✅ Update to your expense route
                method: "POST",
                data: $('#filterForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log("✅ Expense AJAX successful");
                    console.log("📦 Response:", response);

                    let rows = '';
                    let totalAmount = 0;

                    if (response.data.length === 0) {
                        rows = `<tr><td colspan="6" class="text-center text-danger">هیچ نتیجه‌ای یافت نشد</td></tr>`;
                    } else {
                        response.data.forEach(expense => {
                            const amountFormatted = parseFloat(expense.Amount).toLocaleString('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 2
                            });

                            rows += `<tr>
                <td>${expense.id}</td>
                <td>${expense.DateExpense ?? 'N/A'}</td>
                <td>${expense.ExpenseType ?? 'N/A'}</td>
                <td>${expense.Description ?? 'N/A'}</td>
                <td>${amountFormatted}</td>
                <td class="d-print-none">
                    <button class="btn btn-danger btn-sm delete-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal"
                        data-name="${amountFormatted}"
                        data-url="/expense/delete/${expense.id}">
                        حذف
                    </button>

                    <button class="btn btn-info btn-sm edit-btn"
                         data-bs-toggle="modal"
                          data-bs-target="#editModal"
                         data-id="${expense.id}"
                           data-expensedate="${expense.DateExpense}"
                          data-expensetype="${expense.ExpenseType}"
                          data-amount="${amountFormatted}"
                       data-description="${expense.Description}"
                          data-url="/expense/update/${expense.id}">
                          ویرایش
                    </button>
                </td>
            </tr>`;

                            totalAmount += parseFloat(expense.Amount);
                        });

                        const totalFormatted = totalAmount.toLocaleString('en-US', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 2
                        });

                        rows += `<tr>
            <td colspan="4" class="text-end fw-bold">مجموعه:</td>
            <td class="fw-bold">${totalFormatted} (AFN)</td>
            <td class="d-print-none"></td>
        </tr>`;
                    }

                    $('#expenseTableBody').html(rows);
                },
                error: function(xhr, status, error) {
                    console.error("❌ Expense AJAX error:", xhr.responseText);
                    alert("خطا در دریافت اطلاعات: " + xhr.status + " - " + error);
                }
            });
        });
    });
</script>



<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">ویرایش محصول</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">تاریخ مصرف</label>
                        <input type="text" class="usage-edit form-control" id="edit-expenseDate" name="ExpenseDate" readonly />

                        <!-- <input type="date" class="form-control" name="ExpenseDate" id="edit-expenseDate" placeholder="تاریخ"> -->
                    </div>

                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label"> نوع مصرف</label>
                        <input type="text" class="form-control" name="ExpenseType" id="edit-expenseType" placeholder="نوع مصرف ">
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label"> مقدار مصرف </label>
                        <input type="number" name="Amount" class="form-control" id="edit-amount" placeholder="مقدار ">
                    </div>
                    <div class="mb-3">
                        <label class="col-form-label">توضیحات</label>
                        <textarea class="form-control" name="Description" placeholder="توضیحات " id="edit-description"></textarea>
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
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('edit-btn')) {
            const button = e.target;

            const id = button.getAttribute('data-id');
            const expenseDate = button.getAttribute('data-expenseDate');
            const expenseType = button.getAttribute('data-expenseType');
            const amount = button.getAttribute('data-amount');
            const description = button.getAttribute('data-description');
            const url = button.getAttribute('data-url');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-expenseDate').value = expenseDate;
            document.getElementById('edit-expenseType').value = expenseType;
            document.getElementById('edit-amount').value = amount;
            document.getElementById('edit-description').value = description;
            document.getElementById('editForm').action = url;

            $(".usage-edit").persianDatepicker({
                // selectedBefore: !0
            });
        }
    });
</script>



<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="p-3 modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteModalLabel">حذف مصارف</h1>
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
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('delete-btn')) {
            const button = e.target;

            const name = button.getAttribute('data-name');
            const url = button.getAttribute('data-url');

            document.getElementById('delete-message').textContent = `آیا مطمئن هستید که می‌خواهید ${name} را حذف کنید؟`;
            document.getElementById('delete-confirm-link').href = url;
        }
    });
</script>



@endsection