@extends('layouts.master')

@section('title', 'Upload Course File')

@section('headExtra')
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4 create-assessment-form">
    <h2>Upload Course Information</h2>
    <p>Please select the file you want to upload. This feature creates a new course.</p>

    <!-- Display Error Message from Backend -->
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- File Upload Form -->
    <form method="POST" action="{{ route('course.upload.process') }}" enctype="multipart/form-data" class="mt-3">
        @csrf

        <!-- Course File Input -->
        <div class="form-group">
            <label for="file">Course File:</label>
            <input type="file" class="form-control" id="file" name="file" required>
            @error('file')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="form-group button-container">
            <button type="submit" class="btn btn-primary">Upload Course File</button>
        </div>
    </form>
</div>
@endsection
