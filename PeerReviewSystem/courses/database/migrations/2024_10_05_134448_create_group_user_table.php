<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('group_user', function (Blueprint $table) {
        $table->id(); // Primary key
        $table->unsignedBigInteger('group_id'); // Foreign key for groups
        $table->unsignedBigInteger('user_id'); // Foreign key for users
        $table->timestamps(); // Timestamps for tracking when records were created or updated

        // Foreign key constraints
        $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade'); // Deletes records if group is deleted
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Deletes records if user is deleted
    });
}

public function down()
{
    Schema::dropIfExists('group_user'); // Drop the pivot table if rolling back the migration
}

}
