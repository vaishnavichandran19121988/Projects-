@extends('layouts.master')

@section('title', 'Course Assessments')

@section('headExtra')
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4 teacherassign-form">
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

                            @if($assessment->type === 'teacher-assign')
                                <!-- Add Create Teacher-Assign Group button -->
                                <div class="form-group teacherassign-button">
                                    <a href="{{ route('assessment.create_group', [$course->id, $assessment->id]) }}" class="btn btn-warning">Create Teacher-Assign Group</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
