@extends('student.layouts.app')

@section('title', 'Student Info')

@section('content')
{{-- SUBJECTS --}}
<div>

    @foreach($subjects as $subject)
        @php
            $subjectVideos = $videos->where('subject_id', $subject->id);
        @endphp

        <div class="bg-white shadow rounded-2xl m-4 overflow-hidden border border-gray-200">

            {{-- Subject Header --}}
            <button type="button" class="w-full flex justify-between items-center px-6 py-4 
                           bg-blue-50 hover:bg-blue-100 text-gray-800 font-semibold transition"
                onclick="toggleDropdown('subject-{{ $subject->id }}')">

                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
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

            {{-- Subject Content --}}
            <div id="subject-{{ $subject->id }}" class="hidden bg-white">

                @if($subjectVideos->count() > 0)

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                        @foreach($subjectVideos as $video)

                            <a href="{{ route('student.videos', ['id' => $video->id]) }}"
                                class="group block bg-white border border-blue-100 rounded-xl overflow-hidden 
                                          hover:shadow-md hover:border-blue-300 transition">

                                <div class="relative">
                                    <img src="{{ Storage::url($video->video_thumb) }}"
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
