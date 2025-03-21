@extends('layouts.master')

@section('title', 'Assessment Details')

@section('headExtra')
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4 peer-review-form">
    <h2>{{ $assessment->title }} ({{ $assessment->type === 'student-select' ? 'Student-Select' : 'Teacher-Assign' }})</h2>
    <p><strong>Instructions:</strong> {{ $assessment->instruction }}</p>
    <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($assessment->due_date)->format('d M Y, H:i') }}</p>
    <p><strong>Max Reviews:</strong> {{ $assessment->number_of_reviews }}</p>

    <!-- Flash Messages for Success/Error -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Validation Errors (Laravel automatically flashes validation errors to the session) -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($assessment->type === 'student-select')
        <!-- Student Select Type -->
        <form action="{{ route('assessment.submit', [$course->id, $assessment->id]) }}" method="POST" class="review-form-wrapper">
            @csrf
            <div class="form-group">
                <label for="reviewee">Select a student to review:</label>
                <select name="reviewee_id" id="reviewee" class="form-control">
                    <option value="">-- Select a student --</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('reviewee_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
                @error('reviewee_id')
                    <div class="alert alert-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="review">Your Review (Minimum 5 words):</label>
                <textarea name="review" id="review" class="form-control" rows="4">{{ old('review') }}</textarea>
                @error('review')
                    <div class="alert alert-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group button-container">
                <button type="submit" class="btn btn-primary">Submit Review</button>
            </div>
        </form>
    @else
        <!-- Teacher Assign Type -->
        <h3>Your Assigned Group Members</h3>
        <form action="{{ route('assessment.submit', [$course->id, $assessment->id]) }}" method="POST" class="review-form-wrapper">
            @csrf
            <div class="form-group">
                @foreach($groupMembers as $member)
                    <h5>{{ $member->name }}</h5>
                    <div class="form-group">
                        <label for="review_{{ $member->id }}">Your Review for {{ $member->name }}:</label>
                        <textarea name="review_{{ $member->id }}" id="review_{{ $member->id }}" class="form-control" rows="4">{{ old('review_' . $member->id) }}</textarea>
                        @error('review_' . $member->id)
                            <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="form-group button-container">
                <button type="submit" class="btn btn-primary">Submit Group Reviews</button>
            </div>
        </form>
    @endif

    <!-- View Reviews Button for both types -->
    <!-- View Reviews Button for both types -->
    <div class="form-group button-container">
    <form action="{{ route('assessment.allreviews', [$course->id, $assessment->id]) }}" method="GET">
        <button type="submit" class="btn btn-primary">View Reviews</button>
    </form>
</div>

</div>
@endsection
