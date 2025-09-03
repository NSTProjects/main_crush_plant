<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery (برای Select2 لازم است) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
<script>
  // مقداردهی Select2 وقتی مودال باز شد (برای حل z-index/اسکرول)
  const $modal = $('#exampleModal');
  $modal.on('shown.bs.modal', function () {
    // اگر قبلاً مقداردهی شده، destroy کن تا دو بار اجرا نشود
    if ($('#mySelect2').hasClass('select2-hidden-accessible')) {
      $('#mySelect2').select2('destroy');
    }
    $('#mySelect2').select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: 'جنس انتخاب کنید',
      allowClear: true,
      dropdownParent: $modal    // خیلی مهم داخل مودال
      // dir: 'rtl',            // در صورت نیاز، اگر dir="rtl" ندارید
    });
  });

  // اگر می‌خواهی بیرون از مودال هم کار کند (بدون صبر برای باز شدن):
  // $('#mySelect2').select2({ theme:'bootstrap-5', width:'100%' });


$("#mySelect2").change(function(){
	alert("ok");
});
</script>