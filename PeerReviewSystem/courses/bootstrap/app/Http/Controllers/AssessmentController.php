<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Assessment;
use App\Models\Review;
use App\Models\User;
use App\Models\Submission;
use App\Models\Group;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class AssessmentController extends Controller


{


    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createAssessment($id)
    {
        $course = Course::findOrFail($id);
        return view('assessment.createassessment', compact('course'));
    }

    public function storeAssessment(Request $request, $id)
    {


        
    if (auth()->user()->role !== 'Lecturer') {
        return redirect('/home')->with('error', 'You do not have permission to assign scores.');
    }
        $course = Course::findOrFail($id);
    
        // Validate the request inputs
        $validatedData = $request->validate([
            'title' => 'required|max:20',
            'instruction' => 'required',
            'number_of_reviews' => 'required|integer|min:1',
            'max_score' => 'required|integer|between:1,100',
            'due_date' => 'required|date|after:now',
            'type' => 'required|in:student-select,teacher-assign',
            'file' => 'nullable|file|max:2048'
        ]);
    
        // Check if the request has an uploaded file
        if ($request->hasFile('file')) {
            // Store the file and get the file path
            $filePath = $request->file('file')->store('assessments');
            // Add the file path to the validated data
            $validatedData['file_path'] = $filePath;
        }
    
        // Add the course ID to the validated data
        $validatedData['course_id'] = $course->id;
    
        // Create the assessment with all the validated data, including the file path if it exists
        Assessment::create($validatedData);
    
        // Redirect back to the course page with a success message
        return redirect()->route('course.show', $course->id)->with('success', 'Assessment created successfully!');
    }
    
     
    public function viewAssessments($id)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to view assessments.');
        }
    
        // Retrieve the course with assessments based on the course ID
        $course = Course::with('assessments')->findOrFail($id);
    
        // Get the authenticated user
        $user = Auth::user();
    
        // Check if the user is a lecturer or a student enrolled in the course
        if ($user->role === 'Lecturer') {
            // If the user is a lecturer, proceed to show all assessments
            $assessments = $course->assessments;
        } elseif ($user->role === 'student') {
            // If the user is a student, check if they are enrolled in the course
            if (!$user->enrolledCourses->contains($course->id)) {
                return redirect()->back()->with('error', 'You are not enrolled in this course.');
            }
            // Proceed to show all assessments for enrolled students
            $assessments = $course->assessments;
        } else {
            // If the user is neither a lecturer nor a student, deny access
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
    
        // Return the view for assessments with course and assessments data
        return view('assessment.Listassessment', compact('course', 'assessments'));
    }
    
   


    public function viewteacherassignAssessments($id)
    {
        // Fetch the course with only 'teacher-assign' type assessments
        $course = Course::with(['assessments' => function($query) {
            $query->where('type', 'teacher-assign');
        }])->findOrFail($id);
    
        // Get the filtered 'teacher-assign' assessments
        $assessments = $course->assessments;
    
        // Return the view with the filtered assessments
        return view('assessment.teacherassignment', compact('course', 'assessments'));
    }
    

    public function assignStudentsToGroups(Request $request, $id, $assessment_id) 
{
    // Step 1: Fetch the assessment to check the number of reviews required
    $assessment = Assessment::where('id', $assessment_id)->where('type', 'teacher-assign')->firstOrFail();

    // Step 2: Fetch all students enrolled in the course
    $students = User::whereHas('enrolledCourses', function($query) use ($id) {
        $query->where('course_id', $id);
    })->get();

    // Step 3: Shuffle students for random assignment
    $students = $students->shuffle();

    // Number of students
    $studentCount = $students->count();

    // **Step 4: Delete existing groups and group assignments**
    $existingGroups = Group::where('assessment_id', $assessment_id)->get();
    foreach ($existingGroups as $group) {
        $group->users()->detach(); // Detach all students from the group
        $group->delete(); // Delete the group itself
    }

    // Step 5: Compare number of reviews and enrolled students
    if ($assessment->number_of_reviews >= $studentCount) {
        // All students are assigned to one group
        $group = Group::create([
            'name' => 'Group 1',
            'assessment_id' => $assessment->id
        ]);
        $group->users()->attach($students->pluck('id')); // Assign all students to the group

        return redirect()->route('assessment.view_peer_group', ['id' => $id, 'assessment' => $assessment_id])
            ->with('success', 'All students assigned to a single group.');
    }

    // Step 6: Determine the number of groups with min 3 and max 5 students
    $minGroupSize = 3;
    $maxGroupSize = 5;

    $groupCount = floor($studentCount / $minGroupSize); 
    $remainingStudents = $studentCount % $minGroupSize;

    // Adjust group count based on the number of remaining students
    if ($remainingStudents > 0 && ($groupCount * $minGroupSize + $remainingStudents) <= ($groupCount * $maxGroupSize)) {
        $groupCount++;
    }

    // Step 7: Create groups and distribute students
    $groups = collect(); // Collection to store the groups

    for ($i = 1; $i <= $groupCount; $i++) {
        $group = Group::create([
            'name' => 'Group ' . $i,
            'assessment_id' => $assessment->id
        ]);
        $groups->push($group);
    }

    // Step 8: Assign students to the groups (between 3 and 5 per group)
    $studentIndex = 0;
    foreach ($groups as $group) {
        // Calculate the size of this group
        $currentGroupSize = $minGroupSize + ($remainingStudents > 0 ? 1 : 0);
        if ($remainingStudents > 0) {
            $remainingStudents--;
        }

        // Take a slice of students and assign them to the current group
        $studentsForGroup = $students->splice(0, $currentGroupSize);
        $group->users()->attach($studentsForGroup->pluck('id')); // Attach students to the group via the pivot table
    }

    // Step 9: Return success message
    return redirect()->route('assessment.view_peer_group', ['id' => $id, 'assessment' => $assessment_id])
        ->with('success', 'Students assigned to groups successfully.');
}

    





public function viewPeerGroup($id, $assessment)
{
    // Fetch the course
    $course = Course::findOrFail($id);

    // Fetch the assessment
    $assessment = Assessment::findOrFail($assessment);

    // Fetch groups and their related users directly from the pivot table
    $groups = DB::table('groups')
                ->join('group_user', 'groups.id', '=', 'group_user.group_id')
                ->join('users', 'group_user.user_id', '=', 'users.id')
                ->where('groups.assessment_id', $assessment->id)
                ->select('groups.id as group_id', 'groups.name as group_name', 'users.id as user_id', 'users.name as user_name', 'users.s_number')
                ->get();

    // Group users under their respective groups
    $groupedData = $groups->groupBy('group_id');

    // Return the view with the course, assessment, and grouped data
    return view('assessment.viewpeergroup', compact('course', 'assessment', 'groupedData'));
}




    

    public function viewAssessmentDetails($id)
    {
        // Retrieve the assessment with the associated course and students
        $assessment = Assessment::with('course', 'course.students')->findOrFail($id);
    
        // Only allow access if the user is a teacher of this course
        $user = auth()->user();
        if (!$user->taughtCourses->contains($assessment->course_id)) {
            abort(403, 'Unauthorized action.');
        }
    
        // Get the students enrolled in the course
        $students = $assessment->course->students;
    
        return view('assessment.details', compact('assessment', 'students'));
    }

    

    public function showAssessmentGroups($courseId, $assessmentId) 
    {
        // Step 1: Retrieve the course and assessment based on the provided IDs
        $course = Course::findOrFail($courseId);
        $assessment = Assessment::findOrFail($assessmentId);
    
        // Step 2: Check if the user is authenticated (teacher/lecturer)
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to view assessments.');
        }
        $user = Auth::user(); // Get the authenticated user
    
        // Step 3: Check the assessment type
        if ($assessment->type == 'student-select') {
            // For student-select, return the enrolled students
            $students = User::whereHas('enrolledCourses', function($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })->get();
    
            // Return the view with students
            return view('assessment.assessment-group', compact('course', 'assessment', 'students'));
    
        } elseif ($assessment->type == 'teacher-assign') {
            // For teacher-assign, retrieve groups and their students for this assessment
            $groups = Group::with('users')
                           ->where('assessment_id', $assessmentId)
                           ->get();
    
            // Return the view with groups and their students
            return view('assessment.assessment-group', compact('course', 'assessment', 'groups'));
        }
    
        // Fallback in case assessment type is not recognized
        return redirect()->back()->with('error', 'Assessment type is invalid.');
    }
    

    public function editAssessment($course_id, $assessment_id)
{
    $course = Course::findOrFail($course_id);
    $assessment = Assessment::findOrFail($assessment_id);

    return view('assessment.editassessment', compact('course', 'assessment'));
}


public function updateAssessment(Request $request, $course_id, $assessment_id)
{
    $assessment = Assessment::findOrFail($assessment_id);

    // Validate the input fields
    $request->validate([
        'title' => 'required|max:20',
        'instruction' => 'required',
        'number_of_reviews' => 'required|integer|min:1',
        'max_score' => 'required|integer|between:1,100',
        'due_date' => 'required|date|after:now',
        'type' => 'required|in:student-select,teacher-assign'
    ]);

    // Update the assessment with new data
    $assessment->update([
        'title' => $request->title,
        'instruction' => $request->instruction,
        'number_of_reviews' => $request->number_of_reviews,
        'max_score' => $request->max_score,
        'due_date' => $request->due_date,
        'type' => $request->type,
    ]);

    // Redirect back to the course assessment page
    return redirect()->route('assessment.view', $course_id)->with('success', 'Assessment updated successfully!');
}

public function removeStudentFromGroup(Request $request, $course_id, $assessment_id, $student_id)
{
    // Step 1: Detach the student from the current group
    $student = User::findOrFail($student_id);
    $student->groups()->detach(); // Detach the student from all groups (assuming each student belongs to one group at a time)

    // Step 2: Re-run the logic to reshuffle the remaining students into groups
    return $this->assignStudentsToGroups($request, $course_id, $assessment_id);
}



    public function deleteAssessment($id)
    {
        // Find the assessment by its ID, or fail if not found
        $assessment = Assessment::findOrFail($id);
        
        

        // Delete the assessment
        $assessment->delete();

        // Redirect back to the course assessments list with a success message
        return redirect()->back()->with('success', 'Assessment deleted successfully.');
    }


    public function statistics($courseId)
    {
        // Fetch course details
        $course = Course::findOrFail($courseId);

        // Fetch assessments related to the course
        $assessments = Assessment::where('course_id', $courseId)
            ->withCount('submissions') // Count the number of submissions for each assessment
            ->get();

        // Calculate some statistics for the course
        $totalAssessments = $assessments->count();
        $averageScore = $assessments->avg('average_score');
        $topPerformer = $assessments->max('highest_score');
        $lowestScore = $assessments->min('lowest_score');

        return view('assessment_statistics', [
            'course' => $course,
            'totalAssessments' => $totalAssessments,
            'averageScore' => number_format($averageScore, 2),
            'topPerformer' => $topPerformer,
            'lowestScore' => $lowestScore,
            'assessments' => $assessments
        ]);
    }
}
