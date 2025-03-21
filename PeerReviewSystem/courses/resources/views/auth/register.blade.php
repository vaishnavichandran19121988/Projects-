@extends('layouts.master')

@section('title', 'Register')

@section('content')
<div class="login-container">
    <div class="login-box">
        <h1>{{ __('Register') }}</h1>
        <p class="mb-4">Please fill in the form to create an account.</p>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="form-group">
                <input id="name" class="form-control" type="text" name="name" placeholder="{{ __('Name') }}" value="{{ old('name') }}" required autofocus>
            </div>

            <!-- Email Address -->
            <div class="form-group mt-3">
                <input id="email" class="form-control" type="email" name="email" placeholder="{{ __('Email') }}" value="{{ old('email') }}" required>
            </div>

            <!-- S-Number -->
            <div class="form-group mt-3">
                <input id="s_number" class="form-control" type="text" name="s_number" placeholder="{{ __('S-Number') }}" value="{{ old('s_number') }}" required>
                @error('s_number')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group mt-3">
                <input id="password" class="form-control" type="password" name="password" placeholder="{{ __('Password') }}" required>
            </div>

            <!-- Confirm Password -->
            <div class="form-group mt-3">
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" required>
            </div>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary w-100">
                    {{ __('Register') }}
                </button>
                <p class="mt-3">
                    Already registered? <a href="{{ route('login') }}">Login Here</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
