@extends('layouts.master2')

@section('title')
إنشاء حساب - مورا سوفت للادارة القانونية
@stop

@section('css')
<link href="{{URL::asset('assets/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row no-gutter">

        <!-- Right Side Form -->
        <div class="col-md-6 col-lg-6 col-xl-5 bg-white">
            <div class="login d-flex align-items-center py-2">
                <div class="container p-0">
                    <div class="row">
                        <div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
                            <div class="card-sigin">

                                <!-- Logo -->
                                <div class="mb-5 d-flex">
                                    <a href="{{ url('/Home') }}">
                                        <img src="{{URL::asset('assets/img/brand/favicon.png')}}" class="sign-favicon ht-40">
                                    </a>
                                    <h1 class="main-logo1 ml-1 mr-0 my-auto tx-28">
                                        Mora<span>So</span>ft
                                    </h1>
                                </div>

                                <div class="main-signup-header">
                                    <h2>مرحبا بك</h2>
                                    <h5 class="font-weight-semibold mb-4">إنشاء حساب جديد</h5>

                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf

                                        <!-- Name -->
                                        <div class="form-group">
                                            <label>الاسم</label>
                                            <input id="name" type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                name="name" value="{{ old('name') }}" required autofocus>

                                            @error('name')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Email -->
                                        <div class="form-group">
                                            <label>البريد الالكتروني</label>
                                            <input id="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                name="email" value="{{ old('email') }}" required>

                                            @error('email')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Password -->
                                        <div class="form-group">
                                            <label>كلمة المرور</label>
                                            <input id="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                name="password" required>

                                            @error('password')
                                                <span class="invalid-feedback">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="form-group">
                                            <label>تأكيد كلمة المرور</label>
                                            <input id="password-confirm" type="password"
                                                class="form-control"
                                                name="password_confirmation" required>
                                        </div>

                                        <!-- Button -->
                                        <button type="submit" class="btn btn-main-primary btn-block">
                                            تسجيل حساب
                                        </button>

                                        <!-- Login Link -->
                                        <div class="text-center mt-3">
                                            <p>لديك حساب بالفعل؟
                                                <a href="{{ route('login') }}">تسجيل الدخول</a>
                                            </p>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Left Side Image -->
        <div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
            <div class="row wd-100p mx-auto text-center">
                <div class="col-md-12 my-auto mx-auto wd-100p">
                    <img src="{{URL::asset('assets/img/brand/favicon.png')}}"
                        class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto">
                </div>
            </div>
        </div>

    </div>
</div>
@endsection