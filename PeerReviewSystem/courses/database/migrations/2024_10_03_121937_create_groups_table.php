<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id(); // Primary key for groups
            $table->string('name'); // Name of the group
            $table->unsignedBigInteger('assessment_id')->nullable(); // Foreign key for assessments
            
            // Define the foreign key constraint (added during creation)
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            
            $table->timestamps(); // Created_at and Updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
