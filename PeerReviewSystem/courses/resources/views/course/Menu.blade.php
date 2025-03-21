@extends('layouts.master')

@section('title', 'Course Management')

@section('headExtra')  
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4 course-management">
    <div class="row">
        <div class="col-md-12">
            <h2>Course: {{ $course->course_name }} ({{ $course->course_code }})</h2>
            <p>Things To Do:</p>
        </div>
    </div>

    <!-- Course Management Menu as Small Horizontal Cards -->
    <div class="row mt-4">
        <div class="col-12 d-flex flex-row flex-wrap justify-content-start">
            <!-- Card for Course Profile -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users fa-2x mr-2"></i>
                        <div>
                            <h5 class="card-title">Course Profile</h5>
                            <a href="{{ route('course.profile', $course->id) }}" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card for View Course Assessments -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-alt fa-2x mr-2"></i>
                        <div>
                            <h5 class="card-title">View Course Assessments</h5>
                            <a href="{{ route('assessment.view', $course->id) }}" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card for Create Peer Review Group -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-alt fa-2x mr-2"></i>
                        <div>
                            <h5 class="card-title">Create Peer Review Group</h5>
                            <a href="{{ route('course.teacher_assign_assessments', $course->id) }}" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card for Manage Enrollment -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-square fa-2x mr-2"></i>
                        <div>
                            <h5 class="card-title">Manage Enrollment</h5>
                            <a href="{{ route('course.view.students', $course->id) }}" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card for Create New Assessment -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-edit fa-2x mr-2"></i>
                        <div>
                            <h5 class="card-title">Create New Assessment</h5>
                            <a href="{{ route('assessment.create', $course->id) }}" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
