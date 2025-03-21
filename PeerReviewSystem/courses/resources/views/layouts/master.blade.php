<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - Course Management System</title>
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="{{ asset('css/wp.css') }}" rel="stylesheet">
    @yield('headExtra') <!-- This will render additional links or scripts defined in child views -->
</head>
<body>
@if(!Request::is('/')) <!-- Do not show the sidebar on the welcome page -->
    @auth <!-- Only show the sidebar if the user is authenticated -->
        <div class="sidebar">
            <h4>Navigation</h4>
            <ul class="nav-list">
                @if(Auth::user()->role === 'Lecturer')
                    <li>
                        <a href="{{ url('/dashboard') }}" class="nav-item">
                            <i class="fas fa-home"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profile') }}" class="nav-item">
                            <i class="fas fa-user-graduate"></i>
                            Profile
                        </a>
                    </li>
                    <li>
                        <span class="nav-item">
                            <i class="fas fa-chart-line"></i>
                            View Course Assessment Statistics
                        </span>
                    </li>
                    <li>
                        <a href="#" class="nav-item">
                            <i class="fas fa-bullhorn"></i>
                            Send Announcement
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('create-course') }}" class="nav-item">
                            <i class="fas fa-plus-circle"></i>
                            Create Course
                        </a>
                    </li>
                @endif

                @if(Auth::user()->role === 'student')
                    <li>
                        <a href="{{ url('/dashboard') }}" class="nav-item">
                            <i class="fas fa-home"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profile') }}" class="nav-item">
                            <i class="fas fa-user-graduate"></i>
                            Profile
                        </a>
                    </li>
                    <li>
                        <a href="#" class="nav-item">
                            <i class="fas fa-bullhorn"></i>
                            View Announcements
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('enroll-course') }}" class="nav-item">
                            <i class="fas fa-book-open"></i>
                            Enroll in Course
                        </a>
                    </li>
                @endif

                <li>
                    <a class="nav-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    @endauth
@endif

<div class="main-content">
    @yield('content')
</div>

@include('layouts.footer')
</body>
</html>
