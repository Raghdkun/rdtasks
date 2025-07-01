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
        Schema::create('employee_clockings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id'); // Changed from unsignedBigInteger to unsignedInteger
            $table->timestamp('clock_in_time');
            $table->timestamp('clock_out_time')->nullable();
            $table->integer('total_work_minutes')->default(0);
            $table->integer('total_break_minutes')->default(0);
            $table->json('break_sessions')->nullable(); // Store break sessions as JSON
            $table->enum('status', ['clocked_in', 'on_break', 'clocked_out'])->default('clocked_in');
            $table->date('work_date');
            $table->integer('daily_work_hours')->default(420); // 7 hours in minutes
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_clockings');
    }
};