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
        Schema::table('rc_videos', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->constrained('rc_subjects')->onDelete('set null');
            $table->string('google_form_link')->nullable();
            $table->boolean('has_watermark')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rc_videos', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn(['subject_id', 'google_form_link', 'has_watermark']);
        });
    }
};