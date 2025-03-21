@extends('layouts.master')

@section('title', 'Student Dashboard')

@section('content')
<div class="container-fluid mt-4 student-dashboard"> <!-- Updated class name for easier CSS targeting -->
    <div class="row">
        <div class="col-md-12">
            <h1>Welcome, {{ $user->name }}</h1>
            <p>This is your student dashboard.</p>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <h3>Courses Enrolled</h3>

            @if($user->enrolledCourses->isEmpty())
                <div class="alert alert-warning" role="alert">
                    You are not enrolled in any courses.
                </div>
            @else
                <div class="course-container mt-4"> <!-- Added course-container class for consistent styling -->
                    <div class="row">
                        @foreach($user->enrolledCourses as $course)
                            <div class="col-md-4 col-sm-6 mb-4"> <!-- Ensure 3 cards per row, similar to lecturer view -->
                                <div class="card course-card h-100"> <!-- Added course-card class -->
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $course->course_name }}</h5>
                                        <p class="card-text">Course Code: {{ $course->course_code }}</p>
                                        <a href="{{ route('assessment.view', $course->id) }}" class="btn btn-primary btn-card">View Course</a>
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
@endsection
