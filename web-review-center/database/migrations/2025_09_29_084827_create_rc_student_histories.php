<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rc_student_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('rc_students')->onDelete('cascade');
            $table->foreignId('video_id')->constrained('rc_videos')->onDelete('cascade');
            $table->boolean('watched')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rc_student_histories');
    }
};
