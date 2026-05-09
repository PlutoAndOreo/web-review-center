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
        
        Schema::table('rc_subjects', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('rc_subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->after('name');
            $table->foreign('course_id')->references('id')->on('rc_courses')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::table('rc_subjects', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });

        Schema::table('rc_subjects', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

    }
};
