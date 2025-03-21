<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourseTeacherController extends Controller
{
    public function assignLecturer(Request $request)
{
    $assignment = new CourseTeacher();
    $assignment->user_id = $request->user_id; // lecturer id
    $assignment->course_id = $request->course_id;
    $assignment->save();

    return redirect()->back()->with('success', 'Lecturer assigned successfully!');
}



}
