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
        Schema::table('rc_comments', function (Blueprint $table) {
            $table->text('admin_reply')->nullable()->after('content');
            $table->foreignId('admin_id')->nullable()->after('admin_reply')->constrained('rc_admins')->onDelete('set null');
            $table->timestamp('admin_replied_at')->nullable()->after('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rc_comments', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn(['admin_reply', 'admin_id', 'admin_replied_at']);
        });
    }
};
