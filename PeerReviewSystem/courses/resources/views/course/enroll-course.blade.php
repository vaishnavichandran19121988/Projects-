@extends('layouts.master')

@section('title', 'Enroll in a Course')

@section('headExtra')  
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="enroll-course-form">
        <h1>Available Courses</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <ul>
            @forelse($availableCourses as $course)
                <li>
                    {{ $course->course_code }} - {{ $course->course_name }}
                    <form action="{{ route('enroll') }}" method="POST">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                        <button type="submit" class="btn btn-primary">Enroll</button>
                    </form>
                </li>
            @empty
                <li>No courses available for enrollment.</li>
            @endforelse
        </ul>
    </div>
@endsection
