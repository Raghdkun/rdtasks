<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('days_off_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('days_per_week')->default(2);
            $table->integer('start_week_day')->default(1); // 1=Monday, 7=Sunday
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('days_off_settings');
    }
};