<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title> فتحیان (کرش پلنټ) </title>
  <link rel="icon" type="image/x-icon" href="{{asset('assets/logo.png')}}">
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet"> -->
  <link href="{{ asset('assets/css/bootstrap.rtl.min.css') }}" rel="stylesheet">
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"> -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"> -->
  <link href="{{ asset('assets/fontawesome-free/css/all-min.css') }}" rel="stylesheet" />

  <link href="{{ asset('assets/css/bootstrap-icons.css ') }}" rel="stylesheet">
  <!-- <link href="{{ asset('assets/css/bootstrap-icons.css') }}" rel="stylesheet"> -->
  <!-- <link rel="stylesheet" href="../public/assets/css/style.css">
      -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" rel="stylesheet" /> -->
  <!-- <link href="{{asset(' assets/css/all.min.css') }}" rel="stylesheet" /> -->

  <link rel="stylesheet" href="{{ asset('assets/css/persianDatepicker-default.css') }}" />

  @yield('styles')

  <!-- <link rel="stylesheet" href="{{ asset('../assets/css/style.css')}}"> -->
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script> -->
</head>

<body style="font-size: 15px;">
  <!-- 🔹 Top Navbar -->
  <nav class="px-3 navbar navbar-expand bg-primary navbar-dark d-print-none">
    <div class="container-fluid">
      <!-- دکمه تاگل: بلافاصله بعد از برند (سمت راست) -->
      <button class="btn btn-light me-2" type="button"
        data-bs-toggle="offcanvas" data-bs-target="#sidebar"
        aria-controls="sidebar" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>
      <!-- راست: لوگو/نام شرکت -->
      <a class="gap-2 navbar-brand d-flex align-items-center" href="{{route('dashboard')}}">
        <img src="{{ asset('assets/logo.png')}}" alt="لوگو" width="28" height="28" class="rounded-circle">
        <span class="fw-bold">فتحیان (کرش پلنټ) </span>
      </a>



      <!-- چپ: پروفایل کاربر -->
      <div class="gap-2 ms-auto d-flex align-items-center">
        <div class="dropdown">
          <a class="text-white d-flex align-items-center text-decoration-none dropdown-toggle"
            href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
            <img src=" {{ asset('assets/user.png')}}" alt="" width="28" height="28" class="rounded-circle me-2">
            <span class="d-none d-sm-inline">کاربر سیستم</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
            <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> پروفایل</a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> تنظیمات</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item" style="background: none; border: none; padding: 0;">
                  <i class="bi bi-box-arrow-right"></i> خروج
                </button>
              </form>
            </li>
            <!--                         
                        <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right"></i> خروج</a></li> -->
          </ul>
        </div>
      </div>
    </div>
  </nav>



  <!-- 🔹 Sidebar (always overlay) -->
  <div class="offcanvas offcanvas-start text-bg-light sidebar-md" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="sidebarLabel">منو</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="p-0 offcanvas-body">
      <div class="p-3 sidebar">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link active" href="{{route('dashboard')}}"><i class="bi bi-house"></i> داشبورد</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{route('journal')}}"><i class="bi bi-bar-chart"></i> روزنامچه</a>
          </li>
          <li class="nav-item">
            <a class="nav-link d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#submenu1" role="button" aria-expanded="false" aria-controls="submenu1">
              <span><i class="bi bi-folder"></i> اجناس</span>
              <i class="bi bi-caret-down-fill small"></i>
            </a>
            <ul class="collapse ps-3" id="submenu1">
              <li><a class="nav-link" href="{{route('product.index')}}">اجناس</a></li>
              <!-- <li><a class="nav-link " href="{{route('delivery.index')}}"> آوردگی</a></li> -->

            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#submenu3" role="button" aria-expanded="false" aria-controls="submenu3">
              <span><i class="bi bi-folder"></i> فروشات</span>
              <i class="bi bi-caret-down-fill small"></i>
            </a>
            <ul class="collapse ps-3" id="submenu3">

              <li><a class="nav-link" href="{{route('sales-invoice.index')}}">لیست بل ها</a></li>
              <li><a class="nav-link" href="{{route('sales-invoice-item.index')}}">لیست فروشات</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#submenu4" role="button" aria-expanded="false" aria-controls="submenu4">
              <span><i class="bi bi-folder"></i> مشتریان</span>
              <i class="bi bi-caret-down-fill small"></i>
            </a>
            <ul class="collapse ps-3" id="submenu4">
              <li><a class="nav-link" href="{{route('customer.index')}}">مشتریان</a></li>
              <li><a class="nav-link" href="{{route('customer-debit')}}">قرضداران</a></li>
              <li><a class="nav-link" href="{{route('customer-credit')}}">طلبکاران</a></li>

            </ul>
          </li>

          <li><a class="nav-link" href="{{route('expense.index')}}"><i class="fas fa-dollar"></i>مصارف </a></li>
          <li class="nav-item d-none">
            <a class="nav-link" href="#"><i class="bi bi-gear"></i> تنظیمات</a>
          </li>
        </ul>
      </div>
    </div>
  </div>


  <!-- 🔹 Main Content (always full width underneath) -->
  <main class="content-area">
    <div class="py-4 container-fluid ">
      @yield('content')
    </div>
  </main>

  <script src="{{ asset('assets/js/jquery-1.10.1.min.js')}}"></script>
  <script src="{{ asset('assets/js/persianDatepicker.js')}}"></script>


  <script>
    $(".usage").persianDatepicker({
      selectedBefore: !0
    });
  </script>
  <script>
    $(".usage1").persianDatepicker({
      format: 'YYYY/MM/DD',
      autoClose: true,
      initialValueType: 'gregorian',
      calendarType: 'persian'
    });
  </script>
  <script src="{{ asset('assets/js/chart.js')}}"></script>
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{ asset('assets/js/chart.umd.min.js')}}"></script>
  <!-- for Excel  -->
  <script src="{{ asset('assets/js/xlsx.full.min.js')}}"></script>

  @yield('scripts')


</body>

</html>