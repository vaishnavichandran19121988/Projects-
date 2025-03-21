<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('title', 20);  // Assessment title (up to 20 characters)
            $table->text('instruction');  // Free text instruction
            $table->integer('number_of_reviews')->default(1);  // Number of reviews (1 or above)
            $table->integer('max_score')->default(1);  // Max score between 1 and 100
            $table->dateTime('due_date');  // Due date and time
            $table->enum('type', ['student-select', 'teacher-assign']);  // Type of peer review
            $table->string('file_path')->nullable();  // Optional file path for lecturers to provide a file
            $table->foreignId('course_id')->constrained()->onDelete('cascade');  // Course reference
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessments');
    }
}
