@extends('student.layouts.app')

@section('title', 'Videos')

@section('content')

<!-- @if($videos->isEmpty())

    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="text-5xl mb-4">
            <img src="{{ asset('images/video.png') }}" alt="No Videos" class="w-32 h-32 mx-auto">
        </div>

        <h2 class="text-xl font-semibold text-gray-800">
            No videos available
        </h2>

        <p class="text-gray-500 mt-1">
            Please check back later.
        </p>
    </div>

@else -->
@php
    $type = auth()->guard('student')->user()->type;
@endphp
    <div class="max-w-7xl mx-auto px-6 py-10 bg-gray-100 min-h-screen">
    <button onclick="window.history.back()"
        class="px-4 py-2 bg-green-500 text-white rounded mb-4">
        ← BACK
    </button>

        <!-- ✅ HEADER -->
        <div class="flex items-center justify-between mb-8">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Browse Subjects
                </h1>

                <!-- Sort -->
                <div class="flex gap-4">

                    <a href = "{{ route('student.videos.list', ['sort' => 'asc']) }}"
                    class="{{ $sort === 'asc' ? 'text-green-600 font-semibold' : 'text-gray-500' }}">
                        Sort A–Z
                    </a>

                    <a href = "{{ route('student.videos.list', ['sort' => 'desc']) }}"
                    class="{{ $sort === 'desc' ? 'text-green-600 font-semibold' : 'text-gray-500' }}">
                        Sort Z–A
                    </a>
                </div>
            </div>

            <!-- View Toggle TODO-->
            <!-- <div class="flex gap-2">

                <button class="p-2 border rounded-lg hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </button>

                <button class="p-2 border rounded-lg bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
                    </svg>
                </button>

            </div> -->

        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($subjects as $subject)

                @if($subject->videos_count > 0)
                    <a href="{{ route('student.videos.bySubject', $subject->id)  }}">
                        <div class="group cursor-pointer">

                            <div class="relative bg-white rounded-xl p-6 flex flex-col items-center justify-center
                            shadow-sm hover:shadow-md transition">

                                <!-- ✅ Badge (TOP RIGHT) -->
                                <p class="absolute top-3 right-3 text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">
                                    {{ $subject->videos_count }}
                                </p>

                                <!-- Folder Icon -->
                                <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center
                                            group-hover:scale-110 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113a.375.375 0 0 1-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112Z" />
                                    </svg>
                                </div>

                            </div>

                            <!-- Subject Name -->
                            <p class="mt-3 text-center font-semibold text-gray-900">
                                {{ $subject->name }}
                            </p>
                        </div>
                    </a>
                @endif

            @endforeach
        </div>

    </div>


<!-- @endif -->

@endsection
