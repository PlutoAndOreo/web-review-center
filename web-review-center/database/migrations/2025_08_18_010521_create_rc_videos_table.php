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
            $table->foreignId('user_id')->constrained('rc_admins');  
            $table->string('title');               
            $table->text('description')->nullable(); 
            $table->string('file_path')->nullable();           
            $table->enum('status', ['Draft', 'Published','Failed','Processing'])->default('Draft');
            $table->string('google_form_upload')->nullable();
            $table->string('video_thumb')->nullable();  
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
