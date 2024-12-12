<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - YANBU'UL QUR'AN 1</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layout.css-header')
    @yield('custom-header-css')
</head>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            @include('layout.sidebar.sidebar')
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>{{ $pageHeading }}</h3>
            </div>

            <div class="page-content">
                @yield('content')
            </div>
            @include('layout.footer')
        </div>
    </div>
    
    @include('layout.script-js')
    @include('layout.toast-alert')
    
    <script>
        let baseUrl = '{{ url('') }}/'
    </script>
    @yield('custom-js')
</body>

</html>