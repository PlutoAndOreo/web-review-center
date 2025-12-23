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
        Schema::table('rc_students', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('school_graduated')->nullable();
            $table->string('password');
            $table->year('graduation_year')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rc_students', function (Blueprint $table) {
            $table->dropColumn(['address', 'school_graduated', 'graduation_year']);
        });
    }
};