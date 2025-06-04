<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('client_ratings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('task_id');
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('rated_by'); // User who created the rating
            $table->tinyInteger('rating')->comment('1-50 rating scale');
            $table->text('comment')->nullable();
            $table->unsignedInteger('edited_by')->nullable()->comment('User who last edited the rating');
            $table->timestamp('edited_at')->nullable()->comment('When the rating was last edited');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('rated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('set null');
            
            // Ensure one rating per task per client
            $table->unique(['task_id', 'client_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_ratings');
    }
};