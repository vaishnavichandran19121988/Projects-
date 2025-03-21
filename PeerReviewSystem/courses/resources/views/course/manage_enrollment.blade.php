@extends('layouts.master')

@section('title', 'Manage Enrollments')

@section('headExtra')
<link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4 manage-enrollments-form">
    <h2>Manage Enrollments for {{ $course->course_name }} ({{ $course->course_code }})</h2>
    <p>Total Students Enrolled: {{ $enrolledCount }}</p>

    <!-- Section for all students -->
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Student S Number</th>
                <th>Student Email</th>
                <th>Enrollment Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allStudents as $student)
                <tr>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->s_number }}</td>
                    <td>{{ $student->email }}</td>
                    <td>
                        @if($student->enrolled)
                            <span class="text-success">Enrolled</span>
                        @else
                            <span class="text-danger">Not Enrolled</span>
                        @endif
                    </td>
                    <td>
                        @if($student->enrolled)
                            <!-- Remove button for enrolled students -->
                            <form action="{{ route('course.remove_student', [$course->id, $student->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Remove</button>
                            </form>
                        @else
                            <!-- Enroll button for unenrolled students -->
                            <form action="{{ route('course.enroll_student', [$course->id, $student->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Enroll</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $allStudents->links() }}
    </div>
</div>
@endsection
