@extends('student.layouts.app')

@section('title', 'Dashboard')

@section('content')


<style>
    /* Minimal fade-in + subtle lift */
    @keyframes minimalFade {
        0% {
            opacity: 0;
            transform: translateY(4px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .minimal-fade {
        animation: minimalFade 0.45s ease-out forwards;
    }

    /* Smooth dropdown animation */
    .dropdown-smooth {
        transition: all 0.25s ease-out;
        transform-origin: top;
        opacity: 0;
        transform: translateY(-6px);
    }
    .dropdown-smooth.show {
        opacity: 1;
        transform: translateY(0);
    }
</style>


<div class="max-w-7xl mx-auto p-6 space-y-10">

    {{-- HEADER --}}
    <div class="bg-gradient-to-r from-red-600 to-rose-400 text-white
                rounded-3xl shadow-xl p-8 flex flex-col md:flex-row justify-between items-center">

        <div>
            <p class="text-sm opacity-90 mb-1">{{ now()->format('F j, Y') }}</p>
            <h1 class="text-3xl font-bold tracking-wide">
                Welcome back,
                {{ auth('student')->user()->first_name ?? 'Student' }}!
            </h1>
        </div>

        {{-- DROPDOWN --}}
        <div class="relative mt-4 md:mt-0">
            <button type="button" onclick="toggleAccordion('infoAccordion')" class="w-full md:w-auto flex justify-between items-center px-6 py-2.5
                       bg-white text-red-600 font-semibold rounded-xl shadow 
                       hover:bg-red-50 
                       transition duration-100 ease-out 
                      hover:-translate-y-0.5 hover:shadow-sm minimal-fade
">
                <span>My Info</span>
                <svg id="arrow-infoAccordion" class="w-5 h-5 ml-2 transform transition-transform duration-300"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div id="infoAccordion"
                class="hidden absolute right-0 mt-2 bg-white border border-gray-200 rounded-2xl shadow-xl w-64 z-10">

                <div class="p-4 space-y-3 text-sm text-gray-700">
                    <div>
                        <span class="font-semibold">Name:</span>
                        <p>{{ auth('student')->user()->first_name ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <span class="font-semibold">Email:</span>
                        <p>{{ auth('student')->user()->email ?? 'N/A' }}
                        </p>
                    </div>

                    <hr>

                    <a href="{{ route('student.info') }}"
                        class="block text-center text-green-700 font-medium hover:underline">
                        View Full Profile
                    </a>

                    <form method="POST" action="{{ route('student.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-center text-red-600 font-semibold hover:underline">
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- PROGRESS CARDS --}}
    <div>
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            {{ auth('student')->user()->first_name ?? '' }}'s Progress
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-white shadow-lg rounded-2xl p-6 text-center border border-red-300">
                <p class="text-2xl font-bold text-red-600">Videos Watched</p>
                <p class="text-gray-600 text-sm mt-1">{{ $totalWatched }}</p>
            </div>

            <div class="bg-white shadow-lg rounded-2xl p-6 text-center border border-red-300">
                <p class="text-2xl font-bold text-red-600">Forms Submitted</p>
                <p class="text-gray-600 text-sm mt-1">{{ $totalCompletedForms }}</p>
            </div>

        </div>
    </div>


    {{-- SUBJECTS --}}
    <div>
        <h2 class="text-xl font-bold text-gray-800 mb-4">Videos</h2>

        @foreach($subjects as $subject)
            @php
                $subjectVideos = $videos->where('subject_id', $subject->id);
            @endphp

            <div class="bg-white shadow rounded-2xl mb-5 overflow-hidden border border-gray-200">

                {{-- Subject Header --}}
                <button type="button" class="w-full flex justify-between items-center px-6 py-4 
                           bg-red-50 hover:bg-red-100 text-gray-800 font-semibold transition"
                    onclick="toggleDropdown('subject-{{ $subject->id }}')">

                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                        </svg>
                        {{ $subject->name }}
                    </span>

                    <svg id="arrow-subject-{{ $subject->id }}"
                        class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- Subject Content --}}
                <div id="subject-{{ $subject->id }}" class="hidden bg-white">

                    @if($subjectVideos->count() > 0)

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                            @foreach($subjectVideos as $video)

                                <a href="{{ route('student.videos', ['id' => $video->id]) }}"
                                    class="group block bg-white border border-rose-100 rounded-xl overflow-hidden 
                                          hover:shadow-md hover:border-rose-300 transition">

                                    <div class="relative">
                                        <img src="{{ url(ltrim($video->video_thumb, '/')) }}"
                                            alt="{{ $video->title }}" class="w-full h-40 object-cover">
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition">
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <h3 class="font-semibold text-gray-800 truncate">{{ $video->title }}</h3>
                                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                                            {{ Str::limit($video->description, 80) }}
                                        </p>
                                    </div>

                                </a>

                            @endforeach
                        </div>

                    @else
                        <p class="p-5 text-gray-500 text-sm">No videos available.</p>
                    @endif

                </div>

            </div>
        @endforeach
    </div>

</div>

{{-- JS --}}
<script>
    function toggleDropdown(id) {
        const card = document.getElementById(id);
        const arrow = document.getElementById('arrow-' + id);
        card.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

    function toggleAccordion(id) {
        const box = document.getElementById(id);
        const arrow = document.getElementById('arrow-' + id);
        box.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

</script>
@endsection
