<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Assessment;
use App\Models\Group;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;




class CourseController extends Controller


{


public function __construct()
    {
        // Ensure the user is authenticated for all actions
        $this->middleware('auth');
    }

    public function showAvailableCourses()
{
    // Get the currently authenticated user
    $user = Auth::user();

    // Check if the user's role is 'student', as only students can enroll in courses
    if ($user->role !== 'student') {
        // If the user is not a student, redirect them back with an error message
        return redirect()->back()->with('error', 'Unauthorized access. Only students can enroll in courses.');
    }

    // Fetch all courses where the student is not already enrolled
    // The `whereDoesntHave` method checks for courses where the user is not part of the 'students' relationship
    $availableCourses = Course::whereDoesntHave('students', function($query) use ($user) {
        $query->where('user_id', $user->id); // Ensure the course does not have the current user as a student
    })->get();

    // Return the view with the available courses, passing the courses as data
    return view('course.enroll-course', ['availableCourses' => $availableCourses]);
}

// Enroll the authenticated student in a selected course
public function enroll(Request $request)
{
    // Get the currently authenticated user
    $user = Auth::user();

    // Ensure the user has the role of 'student'
    if ($user->role !== 'student') {
        // Redirect back with an error if the user is not a student
        return redirect()->back()->with('error', 'Unauthorized access. Only students can enroll in courses.');
    }

    // Get the course ID from the request
    $courseId = $request->input('course_id');

    // Check if the student is already enrolled in this course
    $alreadyEnrolled = $user->enrolledCourses()->where('course_id', $courseId)->exists();

    if ($alreadyEnrolled) {
        // If the student is already enrolled, redirect back with an error message
        return redirect()->back()->with('error', 'You are already enrolled in this course.');
    }

    // Enroll the student in the course by attaching the course to the user's enrolledCourses relationship
    $user->enrolledCourses()->attach($courseId);

    // Redirect back with a success message indicating that enrollment was successful
    return redirect()->back()->with('success', 'You have successfully enrolled in the course.');

}

public function show($id)
    {
        $course = Course::findOrFail($id);
        return view('course.Menu', compact('course'));
    }


public function viewStudents($id)
    {

    $user = Auth::user();

        $course = Course::findOrFail($id);
    
        // Fetch all students with an indicator if they are enrolled in the course
        $allStudents = User::where('role', 'student')
            ->leftJoin('enrollments', function($join) use ($course) {
                $join->on('users.id', '=', 'enrollments.user_id')
                     ->where('enrollments.course_id', '=', $course->id);
            })
            ->select('users.*', DB::raw('enrollments.course_id IS NOT NULL AS enrolled'))
            ->paginate(10);
            $enrolledCount = $course->students()->count();        
    
        return view('course.manage_enrollment', compact('course', 'allStudents','enrolledCount'));
    }
    
    

public function enrollStudent($course_id, $student_id)
{
    $course = Course::findOrFail($course_id);
    $student = User::findOrFail($student_id);

    // Check if the student is already enrolled in the course
    if (!$course->students()->where('user_id', $student_id)->exists()) {
        // Enroll the student in the course
        $course->students()->attach($student_id);
        return redirect()->back()->with('success', 'Student enrolled successfully.');
    } else {
        return redirect()->back()->with('error', 'Student is already enrolled.');
    }
}


    

    

    public function sendAnnouncement($id)
    {
        $course = Course::findOrFail($id);
        return view('course.send_announcement', compact('course'));
    }

   

public function uploadForm()
    {
        return view('course.upload');
    }

 
    public function processUpload(Request $request)

{


    $user = Auth::user();
      

    // Validate the file is present and is a file type
    $request->validate(['file' => 'required|file']);
    
    // Handle the file upload and save it to a specific folder
    $file = $request->file('file');
    $fileName = time() . '-' . htmlspecialchars($file->getClientOriginalName(), ENT_QUOTES, 'UTF-8');
    $destinationPath = public_path('Course_filesuploaded');
    $file->move($destinationPath, $fileName);
    
    // Open the file to read
    $path = $destinationPath . '/' . $fileName;
    $fileHandle = fopen($path, 'r');
    $data = $this->parseFile($fileHandle);
    fclose($fileHandle);
    
    try {
        DB::transaction(function () use ($data, &$course, $request) {
            // Check if the course already exists
            $existingCourse = Course::where('course_code', $data['course']['course_code'])->first();
            if ($existingCourse) {
                throw new \Exception('Course already exists.');
            }

            // Create the course within the transaction
            $course = Course::create([
                'course_code' => $data['course']['course_code'],
                'course_name' => $data['course']['course_name']
            ]);

            // Attach the logged-in teacher (uploader) as the main lecturer
            $userId = auth()->id();
            $course->mainLecturer()->attach($userId, ['role' => 'main']);  // Attach as main lecturer

            foreach ($data['teachers'] as $teacherData) {
                // Check if the teacher exists by email
                $teacher = User::where('email', $teacherData['email'])->first();
            
                // If the teacher does not exist, create them with the role 'Lecturer'
                if (!$teacher) {
                    $teacher = User::create([
                        's_number' => $teacherData['s_number'],
                        'name' => $teacherData['name'],
                        'email' => $teacherData['email'],
                        'password' => bcrypt('defaultPassword'), // Default password
                        'role' => 'Lecturer' // Role is Lecturer for all teachers
                    ]);
                } else {
                    // If the teacher exists but the role is not Lecturer, update the role
                    if ($teacher->role !== 'Lecturer') {
                        $teacher->update(['role' => 'Lecturer']);
                    }
                }
            
                // Check if the teacher is already attached to the course
                $existingLecturer = $course->assistantLecturers()->where('user_id', $teacher->id)->first();
                
                if ($existingLecturer) {
                    // Update the role if the teacher is already attached
                    $course->assistantLecturers()->updateExistingPivot($teacher->id, ['role' => 'assistant']);
                } else {
                    // Attach the teacher as an assistant lecturer if not already attached
                    $course->assistantLecturers()->attach($teacher->id, ['role' => 'assistant']);
                }
            }
                    // Enroll students from the file
                    foreach ($data['students'] as $studentData) {
                        // Check if the student exists by student number
                        $student = User::firstOrCreate(
                            ['s_number' => $studentData['s_number']],
                            [
                                'email' => $studentData['email'],
                                'password' => bcrypt('defaultPassword'), 
                                'name' => $studentData['name'],
                                'role' => 'student' // Role is student
                            ]
                        );
        
                        // Attach the student to the course if not already enrolled
                        if (!$course->students()->where('user_id', $student->id)->exists()) {
                            $course->students()->attach($student->id);
                        }
                    }
        
                    // Add assessments from the file
                    foreach ($data['assessments'] as $assessmentData) {
                        $existingAssessment = $course->assessments()->where('title', $assessmentData['title'])->first();
                        if (!$existingAssessment) {
                            $course->assessments()->create($assessmentData);
                        }
                    }
                });
        
                // Redirect after successful upload
                return redirect()->route('course.show', $course->id)->with('success', 'Course uploaded successfully!');
            } catch (\Exception $e) {
                // Handle any errors that occurred during the transaction
                return back()->with('error', $e->getMessage());
            }
        }
        

    

    

     
             
    private function parseFile($fileHandle)


{


    $user = Auth::user();

    $result = ['course' => [], 'teachers' => [], 'students' => [], 'assessments' => []];
    $section = '';
    
    while (!feof($fileHandle)) {
        $line = trim(fgets($fileHandle));

        // Skip empty lines
        if (empty($line)) {
            continue;
        }

        // Detect section in the file based on keywords
        if (str_contains($line, 'Course Code')) {
            $section = 'course';
            continue;
        } elseif (str_contains($line, 'Teachers')) {
            $section = 'teachers';
            continue;
        } elseif (str_contains($line, 'Students')) {
            $section = 'students';
            continue;
        } elseif (str_contains($line, 'Assessment')) {
            $section = 'assessments';
            continue;
        }

        $dataParts = array_map('trim', explode(',', $line));

        switch ($section) {
            case 'course':
                if (count($dataParts) >= 2) {
                    $result['course'] = [
                        'course_code' => $dataParts[0],
                        'course_name' => $dataParts[1],
                    ];
                }
                break;

            case 'teachers':
                if (count($dataParts) == 3) {
                    $result['teachers'][] = [
                        's_number' => $dataParts[0],  // Teacher ID
                        'name' => $dataParts[1],      // Teacher Name
                        'email' => $dataParts[2],
                             
                    ];
                }
                break;

            case 'students':
                foreach ($dataParts as $studentNumber) {
                    $result['students'][] = [
                        's_number' => $studentNumber,
                        'email' => $studentNumber . '@example.com', // Generating an email based on student number
                        'name' => 'Student Name', // Default name, can be adjusted if necessary
                        'role' => 'student',
                        'password' => bcrypt('defaultPassword'), // Default password
                    ];
                }
                break;

            case 'assessments':
                if (count($dataParts) >= 6) {
                    $result['assessments'][] = [
                        'title' => $dataParts[0],              // Assessment title
                        'instruction' => $dataParts[1],        // Assessment instruction
                        'number_of_reviews' => $dataParts[2],  // Number of reviews
                        'max_score' => $dataParts[3],          // Maximum score
                        'due_date' => $dataParts[4],           // Due date
                        'type' => $dataParts[5],               // Assessment type (e.g., Exam, Assignment)
                    ];
                }
                break;
        }
    }

    return $result;
}

public function removeStudent($course_id, $student_id)
{
    // Step 1: Find the course and remove the student from the course
    $course = Course::findOrFail($course_id);
    $course->students()->detach($student_id); // Detach the student from the course

    // Step 2: Remove the student from the group
    $student = User::findOrFail($student_id);
    if ($student->group_id) {
        // Check if the group has other members
        $group = Group::find($student->group_id);
        $student->group_id = null; // Remove the student from the group
        $student->save();

        // If the group has no other members, delete the group
        if ($group->students()->count() === 0) {
            $group->delete(); // Delete the group if it's now empty
        }
    }

    // Step 3: Remove all submissions for the student in this course's assessments
    $assessments = $course->assessments;
    foreach ($assessments as $assessment) {
        Submission::where('assessment_id', $assessment->id)
                  ->where('user_id', $student_id)
                  ->delete(); // Delete all submissions by the student for this assessment
    }

    // Return with a success message
    return redirect()->back()->with('success', 'Student removed successfully, along with group and assessment data.');
}



public function courseProfile($id)
{
    // Retrieve the course with peer review assessments, main lecturer, and assistant lecturers
    $course = Course::with(['assessments', 'mainLecturer', 'assistantLecturers'])->findOrFail($id);

    return view('course.course_profile', compact('course'));
}


}