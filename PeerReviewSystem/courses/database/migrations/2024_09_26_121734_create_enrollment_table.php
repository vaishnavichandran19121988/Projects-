<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrollmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
    $table->foreignId('user_id');
    $table->foreignId('course_id');
    $table->timestamps();

    // Setting up foreign keys with explicit definitions
    $table->foreign('user_id')
          ->references('id')->on('users')
          ->onDelete('cascade');

    $table->foreign('course_id')
          ->references('id')->on('courses')
          ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enrollments');
    }
}