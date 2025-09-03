  <!-- 🔹 Top Navbar -->
  <nav class="navbar navbar-expand bg-primary navbar-dark px-3 print-hide">
  <div class="container-fluid">
  <!-- دکمه تاگل: بلافاصله بعد از برند (سمت راست) -->
    <button class="btn btn-light me-2" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#sidebar"
            aria-controls="sidebar" aria-label="Toggle sidebar">
      <i class="bi bi-list"></i>
    </button>
    <!-- راست: لوگو/نام شرکت -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="#">
      <img src="assets/logo.png" alt="لوگو" width="28" height="28" class="rounded-circle">
      <span class="fw-bold">شرکت نوران تک</span>
    </a>
	
	<!-- منوی میانی -->
<!-- منوی میانی: مخفی در موبایل، نمایش از md به بالا -->
<ul class="navbar-nav me-3 d-none d-md-flex align-items-center">
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="#">خانه</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="about">درباره ما</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">تماس</a>
  </li>
    <!-- نمونه زیرمنو (اختیاری) -->
  
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      خدمات
    </a>
    <ul class="dropdown-menu" aria-labelledby="navDropdown">
      <li><a class="dropdown-item" href="#">خدمت ۱</a></li>
      <li><a class="dropdown-item" href="#">خدمت ۲</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item" href="#">همه خدمات</a></li>
    </ul>
  </li>
  
</ul>



    

    <!-- چپ: پروفایل کاربر -->
    <div class="ms-auto d-flex align-items-center gap-2">
      <div class="dropdown">
        <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
           href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="assets/user.png" alt="" width="28" height="28" class="rounded-circle me-2">
          <span class="d-none d-sm-inline">کاربر سیستم</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
          <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> پروفایل</a></li>
          <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> تنظیمات</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right"></i> خروج</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>