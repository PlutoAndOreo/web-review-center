@extends('student.layouts.app')

@section('title', 'Subject List')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10 bg-gray-100 min-h-screen">
    <button onclick="window.history.back()"
        class="px-4 py-2 bg-green-500 text-white rounded mb-4">
        ← BACK
    </button>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $subject->name }}
            </h1>

            <!-- Sort -->
            <div class="flex gap-4">

                <a href="{{ route('student.videos.bySubject', [
                                'sort' => 'asc',
                                'subjectId' => $subjectId
                            ]) }}"
                    class="{{ $sort === 'asc' ? 'text-green-600 font-semibold' : 'text-gray-500' }}">
                    Sort A–Z
                </a>

                <a href="{{ route('student.videos.bySubject', [
                                'sort' => 'desc',
                                'subjectId' => $subjectId
                            ]) }}"
                    class="{{ $sort === 'desc' ? 'text-green-600 font-semibold' : 'text-gray-500' }}">
                    Sort Z–A
                </a>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-3 gap-4">
        @foreach($videos as $video)
            <a href="{{ route('student.videos.player', $video->id) }}">
                <div class="group cursor-pointer w-[240px]">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden
                                    hover:shadow-lg transition">
                        <div class="relative">

                            <img src="{{ asset($video->video_thumb) }}" alt="{{ $video->title }}"
                                class="w-full h-40 object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0
                                            group-hover:opacity-100 transition">
                            </div>

                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="bg-white/80 rounded-full p-3
                                                group-hover:scale-110 transition">

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-3">
                            <p class="text-sm font-semibold text-gray-900 line-clamp-2">
                                {{ $video->title }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($video->created_at)->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    <div>
@endsection
