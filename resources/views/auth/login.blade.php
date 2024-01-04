{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}


<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Absensi IMP-Studio</title>
    <link href="{{ asset('images/IMP-location.png') }}" rel="shortcut icon">
    <link rel="stylesheet" href="{{ asset('dist/css/app.css') }}"/>
    <style>
        #alert .hidden{
            opacity: 0;
            transition: 3s ease-in;
        }
    </style>
</head>
<body class="login">

    @error('email')
        <div id="alert" class="alert alert-primary show mb-2 absolute right-0 m-4" role="alert">
            Ada Sesuatu yang salah dengan email atau passwordnya
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var notificationContent = document.getElementById('alert');

                setTimeout(function () {
                    notificationContent.classList.add('hidden');
                }, 4000);
            });
        </script>
    @enderror
    @error('password')
        <div id="alert" class="alert alert-primary show mb-2 absolute right-0 m-4" role="alert">
            Ada Sesuatu yang salah dengan email atau passwordnya
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var notificationContent = document.getElementById('alert');

                setTimeout(function () {
                    notificationContent.classList.add('hidden');
                }, 4000);
            });
        </script>
    @enderror
    <div class="container sm:px-10">
        <div class="block xl:grid grid-cols-2 gap-4">
            <div class="hidden xl:flex flex-col min-h-screen">
                <a href="" class="-intro-x flex items-center pt-5">
                    <img alt="IMP Studio Logo" class="w-6" src="{{ asset('images/IMP-location.png') }}">
                    <span class="text-white text-lg ml-3"> IMP-Studio </span>
                </a>
                <div class="my-auto">
                    <img alt="IMP Studio Logo" class="-intro-x w-1/2 -mt-16" src="dist/images/illustration.svg">
                    <div class="-intro-x text-white font-medium text-4xl leading-tight mt-10">
                        A few more clicks to
                        <br>
                        sign in to your account.
                    </div>
                    <div class="-intro-x mt-5 text-lg text-white text-opacity-70 dark:text-slate-400">Manage all your employee's presence in one place</div>
                </div>
            </div>

            <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
                <div class="my-auto mx-auto xl:ml-20 bg-white dark:bg-darkmode-600 xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                    <h2 class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left">
                        Log In
                    </h2>
                    <form action="{{ route('login') }}" method="post">
                        @csrf
                        <div class="intro-x mt-2 text-slate-400 xl:hidden text-center">A few more clicks to sign in to your account. Manage all your employee's presence in one place</div>
                        <div class="intro-x mt-8">
                            <input type="email" id="email" class="intro-x login__input form-control py-3 px-4 block @error('email') is-invalid @enderror" placeholder="Email" name="email" value="{{ old('email') }}" required autocomplete="email">
                            <input type="password" id="password" class="intro-x login__input form-control py-3 px-4 block mt-4 @error('password') is-invalid @enderror" placeholder="Password" name="password" required autocomplete="current-password">
                        </div>
                        <div class="intro-x flex text-slate-600 dark:text-slate-500 text-xs sm:text-sm mt-4">
                            <div class="flex items-center mr-auto">
                                <input id="remember-me" type="checkbox" class="form-check-input border mr-2">
                                <label class="cursor-pointer select-none" for="remember-me">Remember me</label>
                            </div>
                            <a href="">Forgot Password?</a>
                        </div>
                        <div class="intro-x mt-5 xl:mt-8 text-center xl:text-left">
                            <button type="submit" class="btn btn-primary py-3 px-4 w-full xl:w-32 xl:mr-3 align-top">Login</button>
                        </div>
                    </form>

                    <div class="intro-x mt-10 xl:mt-24 text-slate-600 dark:text-slate-500 text-center xl:text-left"> By signin up, you agree to our <a class="text-primary dark:text-slate-200" href="">Terms and Conditions</a> & <a class="text-primary dark:text-slate-200" href="/privacy-policy">Privacy Policy</a> </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

