@include('admin.layouts.header')
@vite(['resources/css/app.css', 'resources/js/app.js'])

<!-- Nội dung trang -->
<div class="container-fluid">
    @yield('content')
</div>

@include('admin.layouts.footer')
