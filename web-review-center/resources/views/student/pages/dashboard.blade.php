@extends('student.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex-1 p-10 bg-green-200 rounded-lg shadow-md m-4">
        <div class="mb-8 flex flex-col gap-5">
            <h1 class="text-3xl font-semibold text-gray-800 mb-2">
                Welcome back, {{ $student->first_name }} 
            </h1>
            <p class="text-gray-600">
                Access your courses, track your progress, and manage your profile.
            </p>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Progress -->
        <div
            class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition"
            x-data="{ progress: {{ $progress ?? 0 }} }"
        >
        <h2 class="text-lg font-semibold text-gray-800 mb-1">
            Progress
        </h2>

        <p class="text-gray-500 text-sm mb-4">
            Videos Completed
        </p>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                <div
                    class="bg-green-600 h-2 rounded-full transition-all duration-500"
                    :style="`width: ${progress}%`"
                ></div>
            </div>

            <!-- Progress Text -->
            <p class="text-sm text-gray-600">
                <span x-text="progress"></span>% completed
            </p>
        </div>

        <!--- Quizzes -->

        <div
            class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition"
            x-data="{ exam_progress: {{ $exam_progress ?? 0 }} }"
        >
        <h2 class="text-lg font-semibold text-gray-800 mb-1">
            Progress
        </h2>

        <p class="text-gray-500 text-sm mb-4">
            Quizzes Taken
        </p>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                <div
                    class="bg-green-600 h-2 rounded-full transition-all duration-500"
                    :style="`width: ${exam_progress}%`"
                ></div>
            </div>

            <!-- Progress Text -->
            <p class="text-sm text-gray-600">
                <span x-text="exam_progress"></span>% completed
            </p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition">
            <h2 class="text-lg font-semibold text-gray-800 mb-1">
                Total Active Subjects
            </h2>
            <p class="text-gray-500 text-sm mb-4">
                
            </p>
            <p class="text-2xl font-bold text-gray-900">
                {{ $totalVideos }}
            </p>
        </div>
    </div>

    

@endsection
