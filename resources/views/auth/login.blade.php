<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - YANBU'UL QUR'AN 1</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('') }}/assets/css/bootstrap.css">
    <link rel="stylesheet" href="{{ url('') }}/assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ url('') }}/assets/css/app.css">
    <link rel="stylesheet" href="{{ url('') }}/assets/css/pages/auth.css">
    @include('layout.css-header')

</head>

<body>
    <div id="auth">

        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo">
                        {{-- <a href="#"><img src="assets/images/logo/logo.png" alt="Logo"></a> --}}
                        <p>YANBU'UL QUR'AN 1</p>
                    </div>
                    <h1 class="auth-title">Log in.</h1>

                    <form action="{{ route('login-proses') }}" method="POST">
                        @csrf
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" class="form-control form-control-xl" name="username" placeholder="Username" value="{{ @old('username') }}">
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl" name="password" placeholder="Password">
                            <div class="form-control-icon"> 
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log in</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right" style="background-image: url('{{ asset('3.png') }}'); 
               background-size: cover; 
               background-position: center; 
               background-repeat: no-repeat;">
                           >

    </div>
</div>


                </div>
            </div>
        </div>

        @include('layout.script-js')
        @include('layout.toast-alert')
    </div>
</body>

</html>