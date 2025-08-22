<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    
        Schema::create('rc_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('rc_admins');  // links to users.id 
            $table->string('title');               
            $table->text('description')->nullable(); 
            $table->string('file_path');           // where video is stored (e.g., /videos/filename.mp4)
            $table->string('file_path_s3');           // where video is stored (e.g., /videos/filename.mp4)
            $table->unsignedInteger('duration')->nullable(); // duration in seconds
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rc_videos');
    }
};
