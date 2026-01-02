@extends('student.layouts.app')

@section('title', 'Dashboard')

@section('content')



<!-- Welcome Box -->
<div class="bg-blue-800 rounded-xl text-white p-10 flex justify-between items-center m-2">
    <div>
        <p class="text-sm">{{ now()->format('F j, Y') }}</p>
        <h1 class="text-2xl font-bold mt-1">Welcome back,
            {{ auth('student')->user()->first_name ?? 'Student' }}!
        </h1>
        <p class="text-sm mt-1">Always stay updated in your student portal</p>
    </div>
    <div class="flex items-center">
        <img src="{{ asset('image/dashboard.png') }}" alt="Student" class="h-24 w-24">
    </div>
</div>

<!-- Stats Boxes -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 m-2">
    <!-- Videos Watched -->
    <div class="bg-white rounded-xl p-4 flex flex-col items-center shadow">
        <h2 class="text-lg font-bold">{{ $totalWatched }}</h2>
        <p class="text-gray-400 text-sm">Videos Watched</p>
    </div>

    <!-- Forms Submitted -->
    <div class="bg-white rounded-xl p-4 flex flex-col items-center shadow">
        <h2 class="text-lg font-bold">{{ $totalCompletedForms }}</h2>
        <p class="text-gray-400 text-sm">Forms Submitted</p>
    </div>
</div>

@endsection
