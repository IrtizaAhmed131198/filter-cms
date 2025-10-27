@section('title','Register')
@extends('layouts.main')
@section('css')
<style>
    .form-container.sign-in-container.col-md-6 {margin: 0 auto;}
</style>
@endsection
@section('content')
<section class="banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="banner-wrapper inner-banner-wrapper">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <div class="section-heading text-center">
                                <h1>My Account</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="account">
    <div class="container" id="from-wrapper">
        {{-- <div class="form-container sign-up-container">
            <form  method="POST" action="{{ route('register') }}">
                @csrf
                <h1>Sign Up</h1>
                <div class="form-group">
                    <label>Username*</label>
                    <input type="text" class="form-control {{ $errors->registerForm->has('name') ? ' is-invalid' : '' }}" name="name" id="name"required>
                    @if ($errors->registerForm->has('name'))
                    <small class="alert alert-danger w-100 d-block p-2 mt-2">{{ $errors->registerForm->registerForm->first('name') }}</small>
                    @endif
                </div>
                <div class="form-group">
                    <label>Email Address*</label>
                    <input type="email" class="form-control {{ $errors->registerForm->has('email') ? ' is-invalid' : '' }}" name="email" id="signup-email" required>
                    @if ($errors->registerForm->has('email'))
                    <small class="alert alert-danger w-100 d-block p-2 mt-2">{{ $errors->registerForm->first('email') }}</small>
                    @endif
                </div>
                <div class="form-group">
                    <label>Phone*</label>
                    <input type="text" class="form-control {{ $errors->registerForm->has('phone') ? ' is-invalid' : '' }}" id="phone" name="phone" required>
                    @if ($errors->registerForm->has('phone'))
                    <small class="alert alert-danger w-100 d-block p-2 mt-2">{{ $errors->registerForm->first('phone') }}</small>
                    @endif
                </div>
                <div class="form-group">
                    <label>Address*</label>
                    <input type="text" class="form-control {{ $errors->registerForm->has('address') ? ' is-invalid' : '' }}" name="address" id="address" required>
                    @if ($errors->registerForm->has('address'))
                    <small class="alert alert-danger w-100 d-block p-2 mt-2">{{ $errors->registerForm->first('address') }}</small>
                    @endif
                </div>
                <div class="form-group">
                    <label>Password*</label>
                    <input type="password" class="form-control {{ $errors->registerForm->has('password') ? ' is-invalid' : '' }}" name="password" id="signup-password" required>
                    @if ($errors->registerForm->has('password'))
                    <small class="alert alert-danger w-100 d-block p-2 mt-2">{{ $errors->registerForm->first('password') }}</small>
                    @endif
                </div>
                <div class="form-group">
                    <label>Confirm Password*</label>
                    <input type="password" class="form-control" name="password_confirmation" id="signup-password" required>
                    @if ($errors->registerForm->has('password_confirmation'))
                    <small class="alert alert-danger w-100 d-block p-2 mt-2">{{ $errors->registerForm->first('password_confirmation') }}</small>
                    @endif
                </div>
                <button class="btn btn-yellow" type="submit">Sign Up</button>
            </form>
        </div> --}}
        <div class="form-container sign-in-container col-md-6">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <h1>Login</h1>
                <div class="form-group ">
                    <label>Username or email address*</label>
                    <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
                    @if ($errors->has('email'))
                    <small class="alert alert-danger w-100 d-block p-2 mt-2">{{ $errors->first('email') }}</small>
                    @endif
                </div>
                <div class="form-group">
                    <label>Password*</label>
                    <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">
                    @if ($errors->has('password'))
                    <small class="alert alert-danger w-100 d-block p-2 mt-2">{{ $errors->first('password') }}</small>
                    @endif
                </div>
                <div class="form-group">
                    <label class="remember"><input type="checkbox"> Remember me </label>
                    <a href="{{ url('password/reset') }}" class="pull-right forg_text"> Forgot password? </a>
                </div>
                <button class="btn btn-yellow" type="submit">Login</button>
                <!-- <span>or</span>
                <div class="social-group">
                    <button class="loginBtn loginBtn--facebook">Login with Facebook</button>
                    <button class="loginBtn loginBtn--google">Login with Google</button>
                </div> -->
            </form>
        </div>

    </div>
</section>
@endsection
@section('js')
<script>
    $("#phone").on("keypress keyup blur",function (event) {
       $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
</script>
@endsection
