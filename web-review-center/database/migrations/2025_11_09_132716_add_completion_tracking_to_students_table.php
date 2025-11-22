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
        Schema::table('rc_student_histories', function (Blueprint $table) {
            $table->boolean('form_completed')->default(false)->after('watched');
            $table->timestamp('form_completed_at')->nullable()->after('form_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rc_student_histories', function (Blueprint $table) {
            $table->dropColumn(['form_completed', 'form_completed_at']);
        });
    }
};
