<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SubmissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/home', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
     ->middleware(['auth'])
     ->name('dashboard');

require __DIR__.'/auth.php';

//Student functions 

Route::get('/enroll-course', [CourseController::class, 'showAvailableCourses'])->name('enroll-course');
Route::post('/enroll-course', [CourseController::class, 'enroll'])->name('enroll');
// route to show the  form 
Route::get('/course/{courseId}/assessment/{assessmentId}/submission-form', [SubmissionController::class, 'showSubmissionDetails'])->name('assessment.submission.form');


// For submitting reviews
Route::post('/course/{courseId}/assessment/{assessmentId}/submit', [SubmissionController::class, 'submitReview'])->name('assessment.submit');

// Route for viewing reviews for the user

Route::get('/course/{courseId}/assessment/{assessmentId}/allreviews', [SubmissionController::class, 'viewAllReviews'])->name('assessment.allreviews');





// Route to add a comment to a review
Route::post('/course/{courseId}/assessment/{assessmentId}/allreviews-addcomment', [SubmissionController::class, 'addComment'])->name('review.comment');


// Route to add a rating to a review, based on submission_id
Route::post('/course/{courseId}/assessment/{assessmentId}/allreviews-rate/{submissionId}', [SubmissionController::class, 'addRating'])->name('review.rate');



Route::get('/course/{id}', [CourseController::class, 'show'])->name('course.show');


// ----------------- Assessment Routes -----------------

// View all assessments for a specific course (now handled in AssessmentController)
Route::get('/course/{id}/assessments', [AssessmentController::class, 'viewAssessments'])->name('assessment.view');
// Create a new assessment (for lecturers only, controlled inside the AssessmentController)
Route::get('/course/{id}/create-assessment', [AssessmentController::class, 'createAssessment'])->name('assessment.create');
// Store a new assessment (for lecturers only, controlled inside the AssessmentController)
Route::post('/course/{id}/store-assessment', [AssessmentController::class, 'storeAssessment'])->name('assessment.store');
// Delete an assessment (for lecturers only)
Route::delete('/assessment/{id}', [AssessmentController::class, 'deleteAssessment'])->name('assessment.delete');
// Route to display the edit form for a specific assessment
Route::get('/course/{course_id}/assessment/{assessment_id}/edit', [AssessmentController::class, 'editAssessment'])->name('assessment.edit');
// Route to handle the form submission for updating the assessment
Route::put('/course/{course_id}/assessment/{assessment_id}/update', [AssessmentController::class, 'updateAssessment'])->name('assessment.update');
// Route to handle the form submission for viewing assesment details  
Route::get('/course/{course_id}/assessment/{assessment_id}/details', [AssessmentController::class, 'viewAssessmentDetails'])->name('assessment.details');
//Route to  view assement group to grade 
Route::get('/course/{courseId}/assessment/{assessmentId}/group-details', [AssessmentController::class, 'showAssessmentGroups'])->name('assessment.groupdetails');

// View student reviews for a specific assessment
Route::get('/course/{course_id}/assessment/{assessment_id}/student/{student_id}/reviews', [SubmissionController::class, 'viewStudentReviews'])->name('assessment.student.reviews');


// Assign a score to a student for an assessment

Route::patch('/assessment/{assessment_id}/student/{student_id}/assign-score', [SubmissionController::class, 'assignScore'])->name('assessment.grade_student');


// Route to view teacher-assign assessments for a course
Route::get('/course/{id}/CreatePeergroup', [AssessmentController::class, 'viewTeacherAssignAssessments'])
    ->name('course.teacher_assign_assessments');

  // route to   create peer reivew  grou  
 Route::get('/course/{id}/assessment/{assessment}/CreatePeergroup', [AssessmentController::class, 'assignStudentsToGroups'])
    ->name('assessment.create_group');

// Route to view peer groups for a specific course and assessment
Route::get('/course/{id}/assessment/{assessment}/viewPeerGroup', [AssessmentController::class, 'viewPeerGroup'])
    ->name('assessment.view_peer_group');
    
// Route to remove a student from a group and reshuffle the groups
    Route::post('/course/{id}/assessment/{assessment}/group/{student}/remove', [AssessmentController::class,'removeStudentFromGroup' ])
    ->name('assessment.remove_student_group');

    


// Send announcements (for lecturers only, controlled inside the controller)
Route::get('/course/{id}/send-announcement', [CourseController::class, 'sendAnnouncement'])->name('course.send.announcement');

// Manage students in the course (for lecturers only, controlled inside the controller)
Route::get('/course/{id}/students', [CourseController::class, 'viewStudents'])->name('course.view.students');

// File upload form route (for lecturers)
Route::get('/CreateCourse', [CourseController::class,'uploadForm'])->name('create-course');

// File upload processing route (for lecturers)
Route::post('/CreateCourse', [CourseController::class,'processUpload'])->name('course.upload.process');

Route::delete('/course/{course_id}/remove-student/{student_id}', [CourseController::class, 'removeStudent'])->name('course.remove_student');

Route::get('/profile', [DashboardController::class, 'showProfile'])->name('profile');
Route::put('/profile/{id}', [DashboardController::class, 'updateProfile'])->name('profile.update');

Route::get('/course/{id}/statistics', [AssessmentController::class, 'statistics'])->name('course.statistics');

Route::get('/course/{id}/profile', [CourseController::class, 'courseProfile'])->name('course.profile');

Route::post('/course/{course_id}/enroll-student/{student_id}', [CourseController::class, 'enrollStudent'])->name('course.enroll_student');

