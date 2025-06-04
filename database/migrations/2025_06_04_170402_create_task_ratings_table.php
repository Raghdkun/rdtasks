<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_ratings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('task_id');
            $table->unsignedInteger('rated_by');
            $table->tinyInteger('code_quality')->comment('1-10 rating for Code/Task Quality');
            $table->tinyInteger('delivery_output')->comment('1-10 rating for Delivery Output');
            $table->tinyInteger('time_score')->comment('1-10 rating for Time Score');
            $table->tinyInteger('collaboration')->comment('1-10 rating for Collaboration');
            $table->tinyInteger('complexity_urgency')->comment('1-10 rating for Complexity & Urgency');
            $table->text('comments')->nullable();
            $table->unsignedInteger('edited_by')->nullable()->comment('User who last edited the rating');
            $table->timestamp('edited_at')->nullable()->comment('When the rating was last edited');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('rated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('set null');
            
            // Ensure one rating per task
            $table->unique('task_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('task_ratings');
    }
};