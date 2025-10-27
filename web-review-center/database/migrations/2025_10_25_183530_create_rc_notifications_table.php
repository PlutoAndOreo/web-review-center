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
        Schema::create('rc_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('rc_admins')->onDelete('cascade');
            $table->foreignId('comment_id')->constrained('rc_comments')->onDelete('cascade');
            $table->string('type')->default('comment');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rc_notifications');
    }
};