@extends('layouts.master')

@section('title', 'Lecturer Dashboard')

@section('headExtra')
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid mt-4 lecturer-dashboard"> <!-- Updated class name for easier CSS targeting -->
    <div class="row">
        <div class="col-md-12">
            <h2>Welcome, {{ Auth::user()->name }}</h2>
            <p>This is your lecturer dashboard.</p>
            @if($courses->isEmpty())
                <div class="alert alert-warning" role="alert">
                    No courses under your profile.
                </div>
            @else
                <div class="course-container mt-4"> <!-- Added course-container class -->
                    <div class="row">
                        @foreach($courses as $course)
                            <div class="col-md-4 col-sm-6 mb-4"> <!-- Ensure 3 cards per row, using col-md-4 for desktop and col-sm-6 for smaller devices -->
                                <div class="card course-card h-100"> <!-- Added course-card class -->
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $course->course_name }}</h5>
                                        <p class="card-text">Course Code: {{ $course->course_code }}</p>
                                        <a href="{{ route('course.show', $course->id) }}" class="btn btn-primary btn-card">View Course</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Remove duplicate footer include to prevent double footer issue -->
@endsection
