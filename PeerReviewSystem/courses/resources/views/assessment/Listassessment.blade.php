@extends('layouts.master')

@section('title', 'Course Assessments')

@section('headExtra')
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4 manage-assessments-form">
    <h2>Assessments for {{ $course->course_name }} ({{ $course->course_code }})</h2>

    @if($assessments->isEmpty())
        <p>No assessments have been created for this course yet.</p>
    @else
        <div class="row">
            @foreach($assessments as $assessment)
                <div class="col-lg-4 col-md-6 mb-4"> <!-- Three cards per row on large screens, two cards per row on medium screens -->
                    <div class="card h-100"> <!-- Ensure all cards have equal height -->
                        <div class="card-body">
                            <h5 class="card-title">{{ $assessment->title }}</h5>
                            <p class="card-text"><strong>Instruction:</strong> {{ $assessment->instruction }}</p>
                            <p class="card-text"><strong>Max Score:</strong> {{ $assessment->max_score }}</p>
                            <p class="card-text"><strong>Number of Reviews:</strong> {{ $assessment->number_of_reviews }}</p>
                            <p class="card-text"><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($assessment->due_date)->format('d M Y, H:i') }}</p>
                            <p class="card-text"><strong>Type:</strong> {{ ucfirst($assessment->type) }}</p>

                            <div class="form-group button-container d-flex justify-content-between">
                                <!-- Conditionally show buttons based on user role -->
                                @if(Auth::user()->role === 'student')
                                    <!-- Show only View button for students (submission form) -->
                                    <a href="{{ route('assessment.submission.form', [$course->id, $assessment->id]) }}" class="btn btn-primary">Submit Assessment</a>
                                @elseif(Auth::user()->role === 'Lecturer')
                                    <!-- Show additional buttons for lecturers -->
                                    <a href="{{ route('assessment.edit', [$course->id, $assessment->id]) }}" class="btn btn-primary">Edit</a>
                                    <a href="{{ route('assessment.groupdetails', [$course->id, $assessment->id]) }}" class="btn btn-info">Group Details</a>
                                    
                                    <!-- Delete Assessment form -->
                                    <form action="{{ route('assessment.delete', $assessment->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
