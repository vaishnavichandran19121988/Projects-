@extends('layouts.master')

@section('title', 'Student Reviews for Assessment')

@section('headExtra')
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4 custom-review-container">
    <h2>Student Reviews for {{ $assessment->title }} ({{ $assessment->course->course_name }})</h2>
    <h4>Student: {{ $student->name }}</h4>

    <!-- Show assigned grade if available -->
    <div class="mt-4">
        <h5>Assigned Grade</h5>
        @if(isset($submission->grade))
            <p>The assigned grade for this student is: <strong>{{ $submission->grade }}</strong></p>
        @else
            <p>No grade assigned yet.</p>
        @endif
    </div>

    <!-- Reviews received by the student -->
    <div class="mt-4 custom-reviews-received">
        <h5>Reviews Received</h5>
        @if($receivedReviews->isEmpty())
            <p>No reviews received yet.</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Reviewer</th>
                        <th>Review Comments</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receivedReviews as $review)
                        <tr>
                            <td>{{ $review->reviewer->name }}</td>
                            <td>{{ $review->comments }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Reviews submitted by the student -->
    <div class="mt-4 custom-reviews-submitted">
        <h5>Reviews Written</h5>
        @if($submittedReviews->isEmpty())
            <p>No reviews written yet.</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Reviewee</th>
                        <th>Review Comments</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submittedReviews as $review)
                        <tr>
                            <td>{{ $review->reviewee->name }}</td>
                            <td>{{ $review->comments }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Assign Score Form -->
    <div class="mt-4 custom-assign-score">
        <h5>Assign Grade</h5>
        <form action="{{ route('assessment.grade_student', [$assessment->id, $student->id]) }}" method="POST">
            @csrf
            @method('PATCH') <!-- Use PATCH for updates -->
            <div class="input-group custom-score-input">
                <!-- Display assigned score in the input field for further edits -->
                <input type="number" name="score" class="form-control" placeholder="Assign Score" min="0" max="{{ $assessment->max_score }}" value="{{ old('score', $submission->grade ?? '') }}"> <!-- Show the assigned score if it exists -->
                <button type="submit" class="btn btn-success">Assign</button>
            </div>
        </form>
    </div>

    <!-- Display success message if present -->
    @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
</div>
@endsection
