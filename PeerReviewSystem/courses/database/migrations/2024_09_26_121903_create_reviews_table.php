<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->onDelete('cascade'); // Links to the submission being reviewed
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade'); // Links to the user (reviewer) who is making the review
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade'); // Links to the assessment being reviewed
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade'); // Links to the user (reviewee) being reviewed
            $table->text('comments'); // Comments for the review
            $table->integer('score'); // Score or rating given by the reviewer
            $table->timestamps(); // Timestamps for when the review was created and updated
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
