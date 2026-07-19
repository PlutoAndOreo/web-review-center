<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rc_notifications', function (Blueprint $table) {
            $table->dropForeign(['comment_id']);
        });

        DB::statement('ALTER TABLE rc_notifications MODIFY comment_id BIGINT UNSIGNED NULL');

        Schema::table('rc_notifications', function (Blueprint $table) {
            $table->foreign('comment_id')->references('id')->on('rc_comments')->onDelete('cascade');
            $table->foreignId('video_id')->nullable()->after('comment_id')->constrained('rc_videos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('rc_notifications', function (Blueprint $table) {
            $table->dropForeign(['video_id']);
            $table->dropColumn('video_id');
            $table->dropForeign(['comment_id']);
        });

        DB::statement('ALTER TABLE rc_notifications MODIFY comment_id BIGINT UNSIGNED NOT NULL');

        Schema::table('rc_notifications', function (Blueprint $table) {
            $table->foreign('comment_id')->references('id')->on('rc_comments')->onDelete('cascade');
        });
    }
};
