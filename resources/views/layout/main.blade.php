<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><< TITLE >> - YANBU'UL QUR'AN 1</title>

    @include('layout.css-header')
    @yield('custom-header')
</head>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            @include('layout.sidebar.sidebar-admin')
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3><< PAGE HEADING >></h3>
            </div>

            <div class="page-content">
                @yield('content')
            </div>

            @include('layout.footer')
        </div>
    </div>

    @include('layout.script-js')
    @yield('custom-js')
</body>

</html>