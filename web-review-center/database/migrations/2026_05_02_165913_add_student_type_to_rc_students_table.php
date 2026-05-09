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
            $table->boolean('type')->default(false)->after('course')->comment('0 for f2f students, 1 for online student');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rc_students', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
