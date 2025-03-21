@extends('layouts.master')

@section('title', 'Login')

@section('content')
<div class="login-container">
    <div class="login-box">
        <h1>{{ __('Login') }}</h1>
        <p class="mb-4">Please enter your credentials to login.</p>

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

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

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
              <label for="login">Email or Student Number</label>
               <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus>
          </div>

            <!-- Password -->
            <div class="form-group mt-3">
                <input id="password" class="form-control" type="password" name="password" placeholder="{{ __('Password') }}" required>
            </div>

            <!-- Remember Me Checkbox -->
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">
                    {{ __('Remember Me') }}
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100">
                {{ __('Login') }}
            </button>

            <!-- Link to Registration Page -->
            <p class="mt-3">
                Don't have an account? <a href="{{ route('register') }}">Register Here</a>
            </p>
        </form>
    </div>
</div>
@endsection
