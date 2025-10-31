<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - Forwardedge</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <link href="{{ asset('frontend/assets/images/fav.png') }}" rel="icon">
  <link href="{{ asset('backend/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
      {{-- ✅ CSRF meta for forms & AJAX --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- (Optional) If you ALWAYS serve from the same host/path, you can help relative URLs --}}
  {{-- <base href="/"> --}}
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
<!-- iziToast CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">

  <link href="{{ asset('backend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

  <link href="{{ asset('backend/assets/css/style.css') }}" rel="stylesheet">
  <style>
    /* Modern form styling */
.modern-form .section-header {
    font-size: 1.25rem;
    font-weight: 600;
    color: #0d6efd; /* Bootstrap primary */
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.4rem;
    margin-bottom: 1rem;
}

/* Inputs */
.modern-form .form-control,
.modern-form .form-select {
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease-in-out;
}

.modern-form .form-control:focus,
.modern-form .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13,110,253,.15);
}

/* Buttons */
.modern-form .btn-primary {
    background: linear-gradient(45deg, #0d6efd, #0a58ca);
    border: none;
    border-radius: 0.5rem;
}
.modern-form .btn-primary:hover {
    background: linear-gradient(45deg, #0a58ca, #084298);
}

 </style>
</head>

<body>

  @include('admin.partials.header')

   @include('admin.partials.sidebar')

  <main id="main" class="main">
    <div class="container">
    @yield('main')
    </div>
  </main>@include('admin.partials.footer')

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- iziToast JS -->
<script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
  <script src="{{ asset('backend/assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/chart.js/chart.umd.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/quill/quill.min.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ asset('backend/assets/vendor/php-email-form/validate.js') }}"></script>

  <script src="{{ asset('backend/assets/js/main.js') }}"></script>
    @stack('scripts')
  <script>
  (function () {
    var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!token) return;

    // axios (if present)
    if (window.axios && window.axios.defaults) {
      window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
      window.axios.defaults.withCredentials = true;
    }

    // fetch wrapper (optional safety net)
    if (!window.csrfFetch) {
      window.csrfFetch = function (input, init) {
        init = init || {};
        init.headers = init.headers || {};
        if (!('X-CSRF-TOKEN' in init.headers)) init.headers['X-CSRF-TOKEN'] = token;
        // Keep cookies (session) on same-origin calls
        if (!('credentials' in init)) init.credentials = 'same-origin';
        return fetch(input, init);
      };
    }
  })();
</script>

@if ($errors->any())
    <script>
        @foreach ($errors->all() as $error)
            iziToast.error({
                title: 'Error',
                message: "{{ $error }}",
                position: 'topRight',
                timeout: 5000,
                progressBar: true,
            });
        @endforeach
    </script>
@endif

@if (session('success'))
    <script>
        iziToast.success({
            title: 'Success',
            message: "{{ session('success') }}",
            position: 'topRight',
            timeout: 4000,
        });
    </script>
@endif

@if (session('error'))
    <script>
        iziToast.error({
            title: 'Error',
            message: "{{ session('error') }}",
            position: 'topRight',
            timeout: 4000,
        });
    </script>
@endif

@if (session('info'))
    <script>
        iziToast.info({
            title: 'Info',
            message: "{{ session('info') }}",
            position: 'topRight',
            timeout: 4000,
        });
    </script>
@endif

@if (session('warning'))
    <script>
        iziToast.warning({
            title: 'Warning',
            message: "{{ session('warning') }}",
            position: 'topRight',
            timeout: 4000,
        });
    </script>
@endif


</body>

</html>