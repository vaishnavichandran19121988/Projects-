@extends('layouts.master')

@section('title', 'Peer Review Groups')

@section('headExtra')
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4 peer-review-group-container">
    <h2 class="text-center">Peer Review Groups for {{ $course->course_name }} ({{ $course->course_code }}) - {{ $assessment->title }}</h2> <!-- Course Code and Title -->

    @if($assessment->type === 'student-select')
        <!-- Student-select: Display all students in a single card with white background -->
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 mb-4">
                <div class="card h-100 group-card"> <!-- Apply the same white background class here -->
                    <div class="card-body">
                        <h5 class="card-title text-primary">All Students</h5>
                        <ul class="list-unstyled">
                            @foreach($students->sortBy('id') as $student)
                                <li class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <a href="{{ route('assessment.student.reviews', [$course->id, $assessment->id, $student->id]) }}" class="student-link">{{ $student->name }}</a> ({{ $student->s_number }})
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Teacher-assign: Display each group in separate cards -->
        @if($groups->isEmpty())
            <p>No members have been assigned yet.</p>
        @else
            <div class="row justify-content-center"> <!-- Center the group display -->
                @foreach($groups as $group)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 group-card">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Group: {{ $group->name }}</h5>
                                <ul class="list-unstyled">
                                @foreach($group->students as $student)
                                    <li class="d-flex justify-content-between align-items-center">
                                        <span>
                                            <a href="{{ route('assessment.student.reviews', [$course->id, $assessment->id, $student->id]) }}" class="student-link">{{ $student->name }}</a> ({{ $student->s_number }})
                                        </span>
                                    </li>
                                @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
@endsection
