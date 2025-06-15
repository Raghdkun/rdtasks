<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_days_off', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->date('week_start_date');
            $table->json('selected_days'); // Store array of day numbers [1,2,3] etc
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->unique(['user_id', 'week_start_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_days_off');
    }
};