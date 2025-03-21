@extends('layouts.master')

@section('title', 'Edit Profile')

@section('headExtra')
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4 profile-edit-form">
    <h2>Edit Profile</h2>

    <!-- Profile Edit Form -->
    <form method="POST" action="{{ route('profile.update', $user->id) }}" enctype="multipart/form-data" class="mt-3">
        @csrf
        @method('PUT') <!-- This method is used for updating resources -->
        
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}">
            @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}">
            @error('email')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="role">Role:</label>
            <input type="text" class="form-control" id="role" name="role" value="{{ $user->role }}" readonly>
        </div>

        <!-- Profile Picture -->
        <div class="form-group mt-3">
            <label for="profile_photo">Profile Photo:</label>
            <div class="profile-photo">
                @if($user->profile_photo)
                    <!-- Display the uploaded profile photo if it exists -->
                    <img src="{{ asset('storage/profile_photos/' . $user->profile_photo) }}" alt="Profile Photo" class="img-thumbnail" width="150">
                @else
                    <!-- Display a default user icon if no profile photo is set -->
                    <i class="fas fa-user-circle fa-5x"></i>
                @endif
                
                <!-- File input for uploading a new profile photo -->
                <input type="file" class="form-control mt-2" id="profile_photo" name="profile_photo">
            </div>
            
            <!-- Display error message if there's an issue with the profile photo input -->
            @error('profile_photo')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group button-container mt-3">
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
    </form>
</div>
@endsection
