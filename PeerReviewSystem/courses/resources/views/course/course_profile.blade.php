@extends('layouts.master')

@section('title', 'Course Profile')

@section('headExtra')  
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4 course-profile-form"> <!-- Custom class added for CSS targeting -->
    <div class="row">
        <div class="col-md-12">
            <h2>Course: {{ $course->course_name }} ({{ $course->course_code }})</h2>

            <h4 class="mt-4">List of Teachers:</h4>
            <ul class="list-group mt-3 custom-teacher-list"> <!-- Custom class for teachers list -->
                <li class="list-group-item">
                    <strong>Main Lecturer: </strong> 
                    @if($course->mainLecturer->isEmpty())
                        No main lecturer assigned.
                    @else
                        {{ $course->mainLecturer->first()->name }} - {{ $course->mainLecturer->first()->pivot->role }}
                    @endif
                </li>

                @if($course->assistantLecturers->isNotEmpty())
                    <li class="list-group-item">
                        <strong>Assistant Lecturers:</strong>
                        <ul>
                            @foreach($course->assistantLecturers as $assistant)
                                <li>{{ $assistant->name }} - {{ $assistant->pivot->role }}</li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            </ul>

            <h4 class="mt-4">List of Peer Review Assessments:</h4>
            @if($course->assessments->isEmpty())
                <div class="alert alert-warning">
                    No peer review assessments have been added yet.
                </div>
            @else
                <ul class="list-group mt-3 custom-assessment-list"> <!-- Custom class for assessment list -->
                    @foreach($course->assessments as $assessment)
                        <li class="list-group-item">
                            <strong>{{ $assessment->title }}</strong>
                            <p>{{ $assessment->instruction }}</p>
                            <p>Due Date: {{ $assessment->due_date }}</p>
                            <p>Max Score: {{ $assessment->max_score }}</p>
                            <p>Type: {{ $assessment->type }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
