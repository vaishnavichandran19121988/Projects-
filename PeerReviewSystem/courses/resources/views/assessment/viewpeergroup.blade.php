@extends('layouts.master') 

@section('title', 'Peer Review Groups')

@section('headExtra')
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4 peer-review-group-form">
    <h2 class="text-center">Peer Review Groups for {{ $course->course_name }} ({{ $course->course_code }}) - {{ $assessment->title }}</h2> <!-- Course Code and Title -->

    @if($groupedData->isEmpty())
        <p>No groups have been assigned yet.</p>
    @else
        <div class="row justify-content-center"> <!-- Center the group display -->
            @foreach($groupedData as $groupId => $groupUsers)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 group-card">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Group: {{ $groupUsers->first()->group_name }}</h5>
                            <ul class="list-unstyled">
                                @foreach($groupUsers as $user)
                                    <li>
                                        <a href="{{ route('assessment.student.reviews', [$course->id, $assessment->id, $user->user_id]) }}" class="student-link">{{ $user->user_name }}</a> ({{ $user->s_number }})

                                        <!-- Delete button to remove student from the group -->
                                        <form action="{{ route('assessment.remove_student_group', ['id' => $course->id, 'assessment' => $assessment->id, 'student' => $user->user_id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
