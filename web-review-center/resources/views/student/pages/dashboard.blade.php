@extends('student.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-green-600 to-white-500 text-white rounded-2xl shadow-lg p-8 flex flex-col md:flex-row justify-between items-center">
        <div>
            <p class="text-sm opacity-90 mb-1">{{ now()->format('F j, Y') }}</p>
            <h1 class="text-3xl font-semibold">Welcome back, {{ auth('student')->user()->first_name ?? 'Student' }}!</h1>
        </div>
        <div class="relative mt-4 md:mt-0">
    <!-- Accordion Button -->
    <button 
        type="button"
        onclick="toggleAccordion('infoAccordion')" 
        class="w-full md:w-auto flex justify-between items-center px-5 py-2 bg-white text-green-700 font-medium rounded-xl shadow hover:bg-green-50 transition"
    >
        <span>My Info</span>
        <svg id="arrow-infoAccordion" class="w-5 h-5 ml-2 transform transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Accordion Content -->
    <div id="infoAccordion" class="hidden absolute right-0 mt-2 bg-white border border-gray-100 rounded-xl shadow-lg w-64 overflow-hidden z-10">
        <div class="p-4 space-y-2 text-sm text-gray-700">
            <div class="flex flex-col">
                <span class="font-semibold">Name:</span>
                <span>{{ auth('student')->user()->first_name ?? 'N/A' }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Email:</span>
                <span>{{ auth('student')->user()->email ?? 'N/A' }}</span>
            </div>
            <hr class="my-2">
            <a href="{{ route('student.info') }}" class="block text-center text-green-700 font-medium hover:underline">
                View Full Profile
            </a>
            <form method="POST" action="{{ route('student.logout') }}">
                @csrf
                <button type="submit" class="w-full text-center text-green-700 font-medium hover:underline">
                    Log out
                </button>
            </form>
        </div>
    </div>
</div>
    </div>

    <!-- Finance Section -->
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ auth('student')->user()->first_name ?? '' }} Progress</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow rounded-2xl p-6 text-center border-2 border-green-500">
             <p class="text-2xl font-bold text-green-700">Videos Watched</p>
                <p class="text-gray-500 text-sm mt-1">{{ $totalWatched }}</p>
            </div>
            <div class="bg-white shadow rounded-2xl p-6 text-center border-2 border-green-500">
                <p class="text-2xl font-bold text-green-700">Forms Submitted</p>
                <p class="text-gray-500 text-sm mt-1">{{ $totalCompletedForms }}</p>
            </div>
        </div>
    </div>

    <!-- Subjects Section -->
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Videos</h2>

        @foreach($subjects as $subject)
            @php
                $subjectVideos = $videos->where('subject_id', $subject->id);
            @endphp

            <div class="bg-white shadow-sm rounded-2xl mb-5 overflow-hidden border border-gray-100">
                <button type="button"
                    class="w-full flex justify-between items-center px-5 py-4 bg-green-50 text-left text-gray-800 font-medium hover:bg-green-100 transition"
                    onclick="toggleDropdown('subject-{{ $subject->id }}')">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                        </svg>
                        {{ $subject->name }}
                    </span>
                    <svg id="arrow-subject-{{ $subject->id }}" class="w-5 h-5 transform transition-transform duration-200"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div id="subject-{{ $subject->id }}" class="hidden bg-white">
                    @if($subjectVideos->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-5">
                            @foreach($subjectVideos as $video)
                                <a href="{{ route('student.videos', ['id' => $video->id]) }}"
                                    class="group block bg-gray-50 rounded-xl overflow-hidden hover:bg-green-50 hover:shadow-md transition">
                                    <div class="relative">
                                        <img src="{{ url(ltrim($video->video_thumb, '/')) }}" 
                                             alt="{{ $video->title }}"
                                             class="w-full h-40 object-cover">
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition"></div>
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-semibold text-gray-800 truncate">{{ $video->title }}</h3>
                                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit($video->description, 80) }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="p-5 text-gray-500 text-sm">No videos available for this subject.</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

   
</div>

<script>
    function toggleDropdown(id) {
        const content = document.getElementById(id);
        const arrow = document.getElementById('arrow-' + id);
        content.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

    function toggleAccordion(id) {
        const content = document.getElementById(id);
        const arrow = document.getElementById('arrow-' + id);
        content.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

    // Optional: click outside to close
    window.addEventListener('click', function(e) {
        const accordion = document.getElementById('infoAccordion');
        const button = e.target.closest('button[onclick*="toggleAccordion"]');
        if (!accordion.contains(e.target) && !button) {
            accordion.classList.add('hidden');
            document.getElementById('arrow-infoAccordion').classList.remove('rotate-180');
        }
    });
</script>
@endsection
