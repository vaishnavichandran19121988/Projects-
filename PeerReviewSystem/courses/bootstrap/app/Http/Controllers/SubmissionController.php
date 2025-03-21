<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Assessment;
use App\Models\Review;
use App\Models\Submission;
use App\Models\Group;
use App\Models\Comment;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;



class SubmissionController extends Controller{




    public function __construct()
    {
        // Ensure the user is authenticated for all actions
        $this->middleware('auth');
    }

    public function showSubmissionDetails($courseId, $assessmentId)
{
    // Step 1: Fetch the course and assessment
    $course = Course::findOrFail($courseId);
    $assessment = Assessment::findOrFail($assessmentId);
    $user = Auth::user();

    // Step 2: Check if it's a student-select assessment
    if ($assessment->type === 'student-select') {
        // Fetch all students in the course except the logged-in user
        $students = $course->students()
            ->where('users.id', '!=', $user->id)  // Exclude the logged-in user
            ->get();

        return view('assessment.assessment-form', compact('course', 'assessment', 'students'));
    
    } else {
        // Teacher-assign: Handle reassignment and deletion of old submissions and reviews

        

        // Step 6: Fetch the new group(s) the user is in for this assessment
        $groups = $user->groups()
            ->where('assessment_id', $assessment->id)  // Filter by assessment
            ->with('users')  // Eager load the users in the group
            ->get();

        // Collect all group members except the logged-in user
        $groupMembers = $groups->flatMap(function ($group) use ($user) {
            return $group->users->where('id', '!=', $user->id);
        });

        return view('assessment.assessment-form', compact('course', 'assessment', 'groupMembers'));
    }
}

    
    



    public function showAssesmentGroups($courseId, $assessmentId)
    {
          
    if (auth()->user()->role !== 'Lecturer') {
        return redirect('/home')->with('error', 'You do not have permission to assign scores.');
    }
      
        $course = Course::findOrFail($courseId);
        $assessment = Assessment::findOrFail($assessmentId);
        $user = Auth::user();
        // add logic here to check  user role Lecturer

        $groupMembers = $user->groupMembers($courseId);
    
         // get students in each group to add as card  
           
            $students = $course->students()
    ->select('users.*')  // Ensure you're selecting the users table columns explicitly
    ->where('users.id', '!=', $user->id)  // Fully qualify the 'id' column with the table name
    ->get();

      return view('assessment.viewgroup', compact('course', 'assessment', 'students'));
        
    }



    public function submitReview(Request $request, $courseId, $assessmentId)
{
    $assessment = Assessment::findOrFail($assessmentId);
    $user = Auth::user();

    // Step 1: Fetch the group the user is currently assigned to for this assessment
    $currentGroupUsers = User::whereHas('groups', function ($query) use ($assessment) {
        $query->where('assessment_id', $assessment->id);  // Ensure it's for the correct assessment
    })->pluck('id');  // Get user IDs of all users in the current reassigned group
    

    // Check if the assessment is student-select or teacher-assign
    if ($assessment->type === 'student-select') {

        // Step 4: Validate the review content (min 5 words) and selected reviewee
        $request->validate([
            'review' => ['required', function($attribute, $value, $fail) {
                if (str_word_count($value) < 5) {
                    $fail('The ' . $attribute . ' must contain at least 5 words.');
                }
            }],
            'reviewee_id' => 'required|exists:users,id'
        ]);

        // Step 4: Check if the student has submitted the required number of reviews
$maxReviewsAllowed = $assessment->number_of_reviews; // Get the number of required reviews from the assessment
$submittedReviews = Review::where('reviewer_id', $user->id)
                          ->where('assessment_id', $assessmentId)
                          ->count();

if ($submittedReviews >= $maxReviewsAllowed) {
    return redirect()->back()->withErrors(['You have already submitted the required number of reviews for this assessment.']);
}


// Step 5: Check if the reviewer has already reviewed the selected reviewee for this assessment
$existingReview = Review::where('reviewer_id', $user->id)
                        ->where('reviewee_id', $request->input('reviewee_id'))
                        ->where('assessment_id', $assessmentId)
                        ->first();
        // Step 6: Create a new submission for the current user and assessment
        $submission = Submission::create([
            'user_id' => $user->id,
            'assessment_id' => $assessmentId,
            'submitted_at' => now(),
            'grade' => null,
        ]);

        // Step 6: Create the new review for the selected student (reviewee)
        Review::create([
            'reviewer_id' => $user->id,
            'reviewee_id' => $request->input('reviewee_id'),
            'assessment_id' => $assessmentId,
            'submission_id' => $submission->id,
            'comments' => $request->input('review'),
            'score' => 0,
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully.');

    } else {
        // Handling Teacher-Assign logic for multiple group members
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'review_') !== false) {
                $studentId = explode('_', $key)[1]; // Extract the student ID from the input name

                // Validate each review content (minimum 5 words)
                $request->validate([
                    $key => ['required', function($attribute, $value, $fail) {
                        if (str_word_count($value) < 5) {
                            $fail('The ' . $attribute . ' must contain at least 5 words.');
                        }
                    }]
                ]);

              // Step 2: Check if the user has already reviewed this group member for this assessment
        $existingReview = Review::where('reviewer_id', $user->id)
        ->where('reviewee_id', $studentId)
        ->where('assessment_id', $assessmentId)
        ->first();

if ($existingReview) {
return redirect()->back()->withErrors(['You have already reviewed a group member with ID: ' . $studentId . ' for this assessment.']);
}

                // Step 7: Create a new submission for the current user and assessment
                $submission = Submission::create([
                    'user_id' => $user->id,
                    'assessment_id' => $assessmentId,
                    'submitted_at' => now(),
                    'grade' => null,
                ]);

                // Create a new review for this group member
                Review::create([
                    'reviewer_id' => $user->id,
                    'reviewee_id' => $studentId,
                    'assessment_id' => $assessmentId,
                    'submission_id' => $submission->id,
                    'comments' => $value,
                    'score' => 0,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Group reviews submitted successfully.');
    }
}

    
    

    public function assignScore(Request $request, $assessment_id, $student_id)
{


    $user = Auth::user();
    if (auth()->user()->role !== 'Lecturer') {
        return redirect('/home')->with('error', 'You do not have permission to assign scores.');
    }


    // Validate the score input
    $validatedData = $request->validate([
        'score' => 'required|numeric|min:0', // Validate score input
    ]);

    // Fetch the submission for the specific assessment and student
    $submission = Submission::where('assessment_id', $assessment_id)
                            ->where('user_id', $student_id) // Note: 'user_id' is used instead of 'student_id'
                            ->first();

    // If no submission exists, leave it unassigned and notify the user
    if (!$submission) {
        return redirect()->back()->with('error', 'No submission found for this student. Score remains unassigned.');
    }

    // Fetch the assessment to check the maximum score
    $assessment = Assessment::findOrFail($assessment_id);
    if ($validatedData['score'] > $assessment->max_score) {
        return redirect()->back()->with('error', 'Score exceeds the maximum allowed score.');
    }

    // Update the submission with the new score (grade)
    $submission->grade = $validatedData['score'];
    $submission->save();

    // Redirect back with success message
    return redirect()->back()->with('success', 'Score assigned successfully.');
}


public function viewAllReviews($courseId, $assessmentId)
{
    $user = Auth::user();  // Get the logged-in user


    // Fetch the assessment type
    $assessment = Assessment::findOrFail($assessmentId);
    
    // Check if the logged-in user is a student
    if ($user->role == 'student') {
        // Check if the assessment is 'teacher-assign' or 'student-select'
        if ($assessment->type == 'teacher-assign') {
            // Logic for teacher-assign (with groups)
            
            // Step 1: Fetch the groups the user belongs to for this assessment
            $groupIds = DB::table('group_user')
                ->join('groups', 'group_user.group_id', '=', 'groups.id')
                ->where('group_user.user_id', $user->id)
                ->where('groups.assessment_id', $assessmentId)
                ->pluck('group_user.group_id');

            if ($groupIds->isEmpty()) {
                return redirect()->back()->with('error', 'You are not assigned to any group for this assessment.');
            }

            // Step 2: Fetch reviews received by the logged-in user
            $receivedReviews = Review::where('assessment_id', $assessmentId)
                ->where('reviewee_id', $user->id)
                ->with(['reviewer', 'comments'])
                ->get();

            // Analyze reviews
            $reviewsWithAnalysis = [];
            foreach ($receivedReviews as $review) {
                $entities = $this->extractEntities($review->comments);
                $relations = $this->extractRelations($review->comments);

                $reviewsWithAnalysis[] = [
                    'review' => $review,
                    'analysis' => [
                        'quality' => !empty($entities) ? $this->analyzeQuality($entities) : 'No entities found',
                        'explanation' => !empty($entities) ? $this->analyzeExplanation($entities) : 'No entities found',
                        'constructive_comments' => !empty($relations) ? $this->analyzeConstructiveComments($relations) : 'No relations found'
                    ]
                ];
            }

            // Step 3: Fetch reviews provided by the logged-in user
            $providedReviews = Review::where('assessment_id', $assessmentId)
                ->where('reviewer_id', $user->id)
                ->with(['reviewee', 'comments.user'])
                ->get();

        } elseif ($assessment->type == 'student-select') {
            // Logic for student-select (no groups, just fetch written and provided reviews)

            // Step 1: Fetch reviews received by the logged-in user
            $receivedReviews = Review::where('assessment_id', $assessmentId)
                ->where('reviewee_id', $user->id)
                ->with(['reviewer', 'comments'])
                ->get();

            // Step 2: Fetch reviews provided by the logged-in user
            $providedReviews = Review::where('assessment_id', $assessmentId)
                ->where('reviewer_id', $user->id)
                ->with(['reviewee', 'comments.user'])
                ->get();

            // No group analysis needed for student-select type
            $reviewsWithAnalysis = [];
            foreach ($receivedReviews as $review) {
                $entities = $this->extractEntities($review->comments);
                $relations = $this->extractRelations($review->comments);

                $reviewsWithAnalysis[] = [
                    'review' => $review,
                    'analysis' => [
                        'quality' => !empty($entities) ? $this->analyzeQuality($entities) : 'No entities found',
                        'explanation' => !empty($entities) ? $this->analyzeExplanation($entities) : 'No entities found',
                        'constructive_comments' => !empty($relations) ? $this->analyzeConstructiveComments($relations) : 'No relations found'
                    ]
                ];
            }
        }
        
        // Step 4: Top reviewers (for gamification)
        $topReviewers = DB::table('users')
            ->join('reviews', 'users.id', '=', 'reviews.reviewer_id')
            ->join('ratings', 'ratings.submission_id', '=', 'reviews.submission_id')
            ->select('users.id', 'users.name',
                     DB::raw('AVG(ratings.rating) as average_rating'),
                     DB::raw('SUM(ratings.rating) as points'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('points')
            ->take(5)
            ->get();

            // Step 5: Return the view with received, provided reviews, and top reviewers for gamification
              $assessment = Assessment::findOrFail($assessmentId);



        // Step 5: Return the view with received, provided reviews, and top reviewers
        return view('assessment.allreviews', [
            'courseId' => $courseId,
            'assessment' => $assessment,
            'receivedReviews' => $reviewsWithAnalysis,
            'providedReviews' => $providedReviews,
            'topReviewers' => $topReviewers,
        ]);

    } else {
        // If the user is not a student, handle accordingly (if required)
        return redirect()->back()->with('error', 'You do not have permission to view reviews.');
    }
}




public function viewStudentReviews($courseId, $assessment_id, $student_id) 
{

    $user = Auth::user();
    
    
    if (auth()->user()->role !== 'Lecturer') {
        return redirect('/home')->with('error', 'You do not have permission to assign scores.');
    }

    // Retrieve the course using courseId
    $course = Course::findOrFail($courseId);

    // Retrieve the assessment based on assessment_id and ensure it belongs to the correct course
    $assessment = Assessment::where('id', $assessment_id)
                            ->where('course_id', $courseId)
                            ->firstOrFail();

    // Retrieve the student using student_id
    $student = User::findOrFail($student_id);

    // Fetch reviews submitted by the student for this assessment
    $submittedReviews = $student->reviewsWritten()->where('assessment_id', $assessment_id)->get();

    // Fetch reviews received by the student for this assessment
    $receivedReviews = Review::where('assessment_id', $assessment_id)
                             ->where('reviewee_id', $student_id)  // Assuming `reviewee_id` refers to the student receiving the review
                             ->get();
                             
    // Fetch the submission to get the assigned grade, if it exists
    $submission = Submission::where('assessment_id', $assessment_id)
                            ->where('user_id', $student_id)  // Assuming `user_id` refers to the student
                            ->first();
    
    // Return the view with course, assessment, student, reviews, and the submission for grading
    return view('assessment.Assignscore', compact('course', 'student', 'assessment', 'submittedReviews', 'receivedReviews', 'submission'));
}

 
public function addComment(Request $request,$assessmentId, $reviewId)
{
    $request->validate([
        'comment_text' => 'required|min:5',
    ]);

    Comment::create([
        'review_id' => $reviewId,
        'user_id' => Auth::id(),
        'comment_text' => $request->input('comment_text'),
    ]);

    return redirect()->back()->with('success', 'Comment added successfully.');
}

public function addRating(Request $request, $courseId, $assessmentId, $submissionId) 
{
    // Validate the incoming rating value (ensure it's between 1 and 5)
    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
    ]);

    // Check if the submission exists
    $submission = Submission::find($submissionId);

    if (!$submission) {
        return redirect()->back()->with('error', 'Submission not found.');
    }

    // Check if the user has already rated this submission
    $existingRating = Rating::where('submission_id', $submissionId)
                            ->where('user_id', Auth::id())
                            ->first();

    if ($existingRating) {
        

        // Option 2: Update the existing rating with the new one (this is the current behavior):
        $existingRating->update([
            'rating' => $request->input('rating'),
        ]);

        return redirect()->back()->with('success', 'Rating updated successfully.');
    } else {
        // Create a new rating for the submission if no previous rating exists
        Rating::create([
            'submission_id' => $submissionId,
            'user_id' => Auth::id(),
            'rating' => $request->input('rating'),
        ]);

        return redirect()->back()->with('success', 'Rating submitted successfully.');
    }

    // Update the review score by calculating the new average of all ratings
    $averageRating = Rating::where('submission_id', $submissionId)->avg('rating');

    // Fetch the related review and update its score
    $review = Review::where('submission_id', $submissionId)->first();

    if ($review) {
        $review->update(['score' => round($averageRating, 2)]);
    }

    return redirect()->back()->with('success', 'Rating processed successfully.');
}

public function extractEntities($comments)
{
    // Create a Guzzle client for API request
    $client = new \GuzzleHttp\Client();
    $entities = [];

    try {
        // Send a request to TextRazor API
        $response = $client->request('POST', 'https://api.textrazor.com', [
            'headers' => [
                'x-textrazor-key' => '4538c1967b4c7417093dbee36957d61a27726bb6d2245e5f7b176993',  // Replace with your TextRazor API key
            ],
            'form_params' => [
                'text' => $comments,
                'extractors' => 'entities',
            ],
        ]);

        // Decode the API response
        $result = json_decode($response->getBody(), true);

        // Log the entire API response for debugging
        Log::info('TextRazor API Response:', $result);

        // Extract entities from the response and log them
        $entities = $result['response']['entities'] ?? [];
        Log::info('Extracted Entities: ', $entities);

    } catch (\Exception $e) {
        // Log any exceptions
        Log::error('Error in extractEntities: ' . $e->getMessage());
    }

    return $entities;
}

public function extractRelations($comments)
{
    $relations = [];

    // Manually look for relations in the comment text, such as "improve", "suggest", "praise"
    if (stripos($comments, 'improve') !== false) {
        $relations[] = ['predicate' => ['lemma' => 'improve'], 'confidenceScore' => 0.9];
    }

    if (stripos($comments, 'suggest') !== false) {
        $relations[] = ['predicate' => ['lemma' => 'suggest'], 'confidenceScore' => 0.8];
    }

    if (stripos($comments, 'praise') !== false) {
        $relations[] = ['predicate' => ['lemma' => 'praise'], 'confidenceScore' => 0.85];
    }

    if (stripos($comments, 'good job') !== false || stripos($comments, 'excellent') !== false) {
        $relations[] = ['predicate' => ['lemma' => 'praise'], 'confidenceScore' => 0.95];
    }

    // Log the relations extracted for debugging
    Log::info('Extracted Relations: ', $relations);

    return $relations;
}



public function analyzeReview($comments)
{
    // Assuming this function extracts entities, relations, and provides analysis
    $entities = $this->extractEntities($comments);
    $relations = $this->extractRelations($comments);

    // Ensure that there are entities and relations being captured
    Log::info('Entities Captured: ', $entities);
    Log::info('Relations Captured: ', $relations);

    // Now perform the analysis based on those entities and relations
    $quality = $this->analyzeQuality($entities);
    $explanation = $this->analyzeExplanation($entities);
    $constructiveComments = $this->analyzeConstructiveComments($relations);

    // Log the result of the analysis for debugging
    Log::info('Quality Analysis: ', $quality);
    Log::info('Explanation Analysis: ', $explanation);
    Log::info('Constructive Comments Analysis: ', $constructiveComments);

    // Return the analysis result
    return [
        'quality' => $quality,
        'explanation' => $explanation,
        'constructive_comments' => $constructiveComments
    ];
}



/**
     * Analyze the quality of the submission by looking for positive and negative sentiments in the review.
     *
     * @param array $entities The entities extracted by TextRazor.
     * @return array An array containing 'positive' and 'negative' sentiments.
     */
  
  

     public function analyzeQuality($entities)
     {
         $positive = [];
         $negative = [];
     
         foreach ($entities as $entity) {
             // Use confidenceScore to determine positive or negative
             if (isset($entity['confidenceScore']) && $entity['confidenceScore'] > 1.0) {
                 if (!is_numeric($entity['matchedText']) && strlen($entity['matchedText']) > 3) {
                     $positive[] = $entity['matchedText'];  // 'Done Well' items
                 }
             } else {
                 if (!is_numeric($entity['matchedText']) && strlen($entity['matchedText']) > 3) {
                     $negative[] = $entity['matchedText'];  // 'Could be Better' items
                 }
             }
         }
     
         // Remove duplicates
         $positive = array_unique($positive);
         $negative = array_unique($negative);

         if (empty($negative)) {
            $negative[] = 'No negative feedback';
        }
     
         return [
             'done_well' => $positive,
             'could_be_better' => $negative
         ];
     }
     




public function analyzeExplanation($entities)
{
    $clear = false;
    $confusing = false;

    foreach ($entities as $entity) {
        // Ensure entityId is a string before running in_array
        $entityId = strtolower($entity['entityId'] ?? '');

        // Check if the entity is related to clarity
        if (in_array($entityId, ['clear', 'easy', 'understand'])) {
            $clear = true;
        } 
        // Check if the entity is related to confusion
        elseif (in_array($entityId, ['confusing', 'difficult'])) {
            $confusing = true;
        }
    }


    // Avoid conflicting feedback by ensuring only one of the two flags is true
    if ($clear && $confusing) {
       
        $clear = false; // Prioritize "confusing" if both are found
    }

 



    return [
        'clear' => $clear,
        'confusing' => $confusing,
        'feedback' => $clear ? 'The explanation was clear.' 
        : ($confusing 
            ? 'The explanation was confusing.' 
            : 'No clear feedback on explanation.')
    ];
}



    /**
     * Analyze whether the review mentions if the explanation is clear or confusing.
     *
     * @param array $entities The entities extracted by TextRazor.
     * @return array An array with 'clear' or 'confusing' flags set to true or false.
     */
   

     public function analyzeConstructiveComments($relations)
     {
         $constructive = [];
         $praise = [];
         
         // Define keywords to expand the detection of constructive feedback
         $keywords = ['improve', 'fix', 'clarify', 'enhance', 'add', 'expand'];
     
         foreach ($relations as $relation) {
             // Ensure 'lemma', 'confidenceScore', and 'relevanceScore' exist before checking
             if (isset($relation['predicate']['lemma']) && isset($relation['confidenceScore']) && isset($relation['relevanceScore'])) {
                 
                 // Filter based on confidence and relevance scores
                 if ($relation['confidenceScore'] > 0.75 && $relation['relevanceScore'] > 0.5) {
                     
                     // Identify constructive comments related to improvement or related keywords
                     if (in_array($relation['predicate']['lemma'], $keywords)) {
                         $constructive[] = $relation['predicate']['lemma'];
                         
                         // Log constructive comment found with high confidence and relevance
                         Log::info('High Confidence Constructive Comment Found: ' . $relation['predicate']['lemma']);
                     }
     
                     // Identify praise or positive remarks
                     elseif ($relation['predicate']['lemma'] === 'praise' || 
                             (isset($relation['arguments'][0]['entities']) && in_array('good', $relation['arguments'][0]['entities']))) {
                         $praise[] = $relation['predicate']['lemma'];
     
                         // Log praise found with high confidence and relevance
                         Log::info('High Confidence Praise Found: ' . $relation['predicate']['lemma']);
                     }
                 }
             }
         }
     
         // Return the structured feedback
         return [
             'constructive' => $constructive,
             'praise' => $praise,
             'feedback' => [
                 'constructive_feedback' => empty($constructive) ? 'No constructive feedback given.' : implode(', ', $constructive),
                 'praise' => empty($praise) ? 'No praise provided.' : implode(', ', $praise)
             ]
         ];
     }
     


    /**
     * Fetch the review by ID, analyze it, and pass the analysis to the view for rendering.
     *
     * @param int $reviewId The ID of the review to be analyzed.
     * @return \Illuminate\View\View The view with the analyzed data.
     */
   

     public function showReviewAnalysis($reviewId)
{
    // Fetch the review by its ID
    $review = Review::findOrFail($reviewId);

    // Check if there are comments to analyze
    if (!empty($review->comments)) {
        // Analyze the review using the TextRazor API or your custom method
        $result = $this->analyzeReview($review->comments);

        // Log the result for debugging
        Log::info('Review Analysis Result:', ['reviewId' => $reviewId, 'analysis' => $result]);
    } else {
        // Handle case where there are no comments
        $result = ['message' => 'No comments available for analysis.'];
    }

    // Return the view with the review and analysis result
    return view('reviews.analysis', [
        'review' => $review,
        'analysis' => $result,
    ]);
}
}