@extends('layouts.master')

@section('title', 'Edit Assessment')

@section('headExtra')
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4 create-assessment-form">
    <h2>Edit Peer Review Assessment for {{ $course->course_name }}</h2>

    <!-- Edit Assessment Form -->
    <form method="POST" action="{{ route('assessment.update', [$course->id, $assessment->id]) }}" class="mt-3">
        @csrf
        @method('PUT') <!-- To specify the method as PUT for updating -->

        <div class="form-group">
            <label for="title">Assessment Title:</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $assessment->title) }}">
            @error('title')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="instruction">Instruction:</label>
            <textarea class="form-control" id="instruction" name="instruction" rows="3">{{ old('instruction', $assessment->instruction) }}</textarea>
            @error('instruction')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="number_of_reviews">Number of Reviews:</label>
            <input type="number" class="form-control" id="number_of_reviews" name="number_of_reviews" value="{{ old('number_of_reviews', $assessment->number_of_reviews) }}">
            @error('number_of_reviews')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="max_score">Maximum Score:</label>
            <input type="number" class="form-control" id="max_score" name="max_score" value="{{ old('max_score', $assessment->max_score) }}">
            @error('max_score')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="due_date">Due Date:</label>
            <input type="datetime-local" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', \Carbon\Carbon::parse($assessment->due_date)->format('Y-m-d\TH:i')) }}">
            @error('due_date')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="upload_file">Upload File (Optional):</label>
            <input type="file" class="form-control" id="upload_file" name="upload_file">
        </div>

        <div class="form-group mt-3">
            <label for="type">Type of Peer Review:</label>
            <select class="form-control" id="type" name="type">
                <option value="student-select" {{ old('type', $assessment->type) == 'student-select' ? 'selected' : '' }}>Student-Select</option>
                <option value="teacher-assign" {{ old('type', $assessment->type) == 'teacher-assign' ? 'selected' : '' }}>Teacher-Assign</option>
            </select>
            @error('type')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group button-container">
            <button type="submit" class="btn btn-primary">Update Assessment</button>
        </div>
    </form>
</div>
@endsection
