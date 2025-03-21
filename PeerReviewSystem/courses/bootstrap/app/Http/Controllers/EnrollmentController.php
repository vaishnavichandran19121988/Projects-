<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function enrollStudent(Request $request)
{
    $enrollment = new Enrollment();
    $enrollment->user_id = $request->user_id; // student id
    $enrollment->course_id = $request->course_id;
    $enrollment->save();

    return redirect()->back()->with('success', 'Student enrolled successfully!');
}

}
