@extends('layouts.master')

@section('title', 'Received and Provided Reviews with Gamification')

@section('headExtra')
    <link href="{{ asset('css/wp.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-4 received-reviews-form">
    <h2 class="display-4 mb-4">Received and Provided Reviews for {{ $assessment->title }} ({{ $assessment->type === 'student-select' ? 'Student-Select' : 'Teacher-Assign' }})</h2>

    <!-- Section 1: Gamification Leaderboard with Badges -->
    <h3 class="mb-3 font-weight-bold text-primary">Top Reviewers</h3>
    <div class="leaderboard mb-4">
        @if(empty($topReviewers))
            <p class="text-muted">No reviewers to display yet.</p>
        @else
            <ul class="list-group">
                @foreach($topReviewers as $topReviewer)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold">{{ $topReviewer->name }}</span>
                        
                        <!-- Display badges based on reviewer points or rating -->
                        @if($topReviewer->points >= 100)
                            <span class="badge badge-warning badge-pill">⭐ Top Reviewer</span>
                        @elseif($topReviewer->average_rating >= 4.5)
                            <span class="badge badge-primary badge-pill">🏆 5-Star Contributor</span>
                        @endif
                        
                        <span class="badge badge-success badge-pill">Points: {{ $topReviewer->points }}</span>
                        <span class="badge badge-info badge-pill">Avg. Rating: {{ round($topReviewer->average_rating, 2) }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Section 2: Reviews You Have Received with Analysis -->
    <h3 class="mt-4 font-weight-bold text-primary">Reviews You Have Received</h3>
    @if(empty($receivedReviews))
        <p class="text-muted">No reviews received yet.</p>
    @else
        <ul class="list-group mb-4">
            @foreach($receivedReviews as $reviewData)
                <li class="list-group-item review-item mb-3 p-3 shadow-sm border rounded">
                    <div class="review-header d-flex justify-content-between align-items-center">
                        <h5 class="font-weight-bold">{{ $reviewData['review']->reviewer->name }}'s Review for You</h5>
                        @if($reviewData['review']->score >= 4.5)
                            <span class="badge badge-primary">🌟 High Scorer</span>
                        @endif
                    </div>

                    <p class="lead">{{ $reviewData['review']->comments }}</p>
                    <small class="text-muted">Score: {{ $reviewData['review']->score }}</small>

                    <!-- Analysis Section -->
                    <div class="analysis-section mt-3">
                        <h6 class="font-weight-bold">Review Analysis:</h6>

                        <p><strong>Quality:</strong> 
                            Positive: {{ isset($reviewData['analysis']['quality']['done_well']) ? implode(', ', $reviewData['analysis']['quality']['done_well']) : 'No positive feedback available' }},
                            Negative: {{ isset($reviewData['analysis']['quality']['could_be_better']) ? implode(', ', $reviewData['analysis']['quality']['could_be_better']) : 'No negative feedback available' }}
                        </p>

                        <p><strong>Explanation:</strong> 
                             Clear: {{ $reviewData['analysis']['explanation']['clear'] ?? false ? 'Yes' : 'No' }},
                             Confusing: {{ $reviewData['analysis']['explanation']['confusing'] ?? false ? 'Yes' : 'No' }},
                             Feedback: {{ $reviewData['analysis']['explanation']['feedback'] ?? 'No feedback provided' }}
                        </p>

                        <p><strong>Constructive Comments:</strong> 
                            {{ isset($reviewData['analysis']['constructive_comments']['constructive']) ? implode(', ', $reviewData['analysis']['constructive_comments']['constructive']) : 'No constructive feedback available' }}
                        </p>

                        <p><strong>Praise:</strong> 
                            {{ isset($reviewData['analysis']['constructive_comments']['praise']) ? implode(', ', $reviewData['analysis']['constructive_comments']['praise']) : 'No praise available' }}
                        </p>

                        <!-- Review Rating System -->
                        <div class="rating-section mt-3">
                            <form action="{{ route('review.rate', [$courseId, $assessment->id, $reviewData['review']->submission_id]) }}" method="POST">
                                @csrf
                                <label for="rating" class="font-weight-bold">Rate this review:</label>
                                <select name="rating" id="rating" class="form-control w-50">
                                    <option value="1">1 - Poor</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="3">3 - Good</option>
                                    <option value="4">4 - Very Good</option>
                                    <option value="5">5 - Excellent</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary mt-2">Rate</button>
                            </form>
                        </div>

                        <!-- Display comments for this review -->
                        @if(isset($reviewData['review']->comments) && $reviewData['review']->comments instanceof Illuminate\Database\Eloquent\Collection && $reviewData['review']->comments->isNotEmpty())
                            <div class="comments-section mt-3">
                                <h6 class="font-weight-bold">Comments:</h6>
                                <ul class="list-unstyled">
                                    @foreach($reviewData['review']->comments as $comment)
                                        <li class="mb-2">{{ $comment->user->name }}: {{ $comment->content }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Form to add a new comment -->
                        <form action="{{ route('review.comment', [$courseId, $assessment->id, $reviewData['review']->id]) }}" method="POST">
                            @csrf
                            <div class="form-group mt-3">
                                <textarea name="comment" class="form-control" rows="2" placeholder="Leave a comment..." required></textarea>
                            </div>
                            <div class="form-group button-container">
                                <button type="submit" class="btn btn-sm btn-info">Add Comment</button>
                            </div>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <!-- Section 3: Reviews You Have Provided -->
    <h3 class="font-weight-bold text-primary">Reviews You Have Provided</h3>
    @if(empty($providedReviews))
        <p>No reviews provided yet.</p>
    @else
        <ul class="list-group">
            @foreach($providedReviews as $review)
                <li class="list-group-item review-item mb-3 p-3 shadow-sm border rounded">
                    <div class="review-header d-flex justify-content-between align-items-center">
                        <h5 class="font-weight-bold">Your Review for {{ $review->reviewee->name }}</h5>

                        <!-- Add badge for reviews with high scores -->
                        @if($review->score >= 4.5)
                            <span class="badge badge-primary">🌟 High Scorer</span>
                        @endif
                    </div>

                    <p class="lead">{{ $review->comments }}</p>
                    <small class="text-muted">Score Given: {{ $review->score }}</small>

                    <!-- Display comments for this review -->
                    @if(isset($review->comments) && $review->comments instanceof Illuminate\Database\Eloquent\Collection && $review->comments->isNotEmpty())
                        <div class="comments-section mt-3">
                            <h6 class="font-weight-bold">Comments:</h6>
                            <ul class="list-unstyled">
                                @foreach($review->comments as $comment)
                                    <li class="mb-2">{{ $comment->user->name }}: {{ $comment->content }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form to add a new comment -->
                    <form action="{{ route('review.comment', [$courseId, $assessment->id, $review->id]) }}" method="POST">
                        @csrf
                        <div class="form-group mt-3">
                            <textarea name="comment" class="form-control" rows="2" placeholder="Leave a comment..." required></textarea>
                        </div>
                        <div class="form-group button-container">
                            <button type="submit" class="btn btn-sm btn-info">Add Comment</button>
                        </div>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
