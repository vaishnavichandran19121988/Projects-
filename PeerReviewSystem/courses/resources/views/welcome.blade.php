@extends('layouts.master')

@section('title', 'Welcome')

@section('headExtra')
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="jumbotron text-center">
    <h1 class="display-4">Course Management System</h1>
    <p class="lead">An easy-to-use system for students and lecturers for learning.</p>
    <hr class="my-4">
    <p>Login or register to get started.</p>
    <p class="lead">
        <a class="btn btn-primary btn-lg" href="{{ route('login') }}" role="button">Login</a>
        <a class="btn btn-secondary btn-lg" href="{{ route('register') }}" role="button">Register</a>
    </p>
</div>
@endsection
