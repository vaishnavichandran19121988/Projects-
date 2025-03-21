<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_teacher', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('course_id')->constrained()->onDelete('cascade'); // Foreign key to courses table
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->enum('role', ['main', 'assistant']); // Enum for lecturer role: main or assistant
            $table->timestamps(); // created_at and updated_at columns

            // Unique constraint only for the main lecturer
            $table->unique(['course_id', 'role'], 'unique_main_teacher')
                ->where('role', 'main'); // Ensure only one main lecturer per course
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_teacher');
    }
}
