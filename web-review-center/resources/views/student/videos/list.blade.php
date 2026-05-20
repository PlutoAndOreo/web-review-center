@extends('student.layouts.app')

@section('title', 'Videos')

@section('content')

<div class="main-container space-y-12 px-6">

    @foreach ($videos as $subjectId => $subjectVideos)

        <!--  Album Header -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ optional($subjectVideos->first()->subject)->name ?? 'Unknown Subject' }}
                </h2>
            </div>

            <!--  Album Row -->
            <div class="flex gap-5 overflow-x-auto pb-3">

                @foreach ($subjectVideos as $video)
                    <form method="GET"
                          action="{{ route('student.videos', $video->id) }}"
                          class="min-w-[240px]">

                        <!--  CARD -->
                        <button type="submit" class="w-full text-left group">

                            <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">

                                <!--  THUMBNAIL -->
                                <div class="relative">
                                    <img 
                                        src="{{ url($video->video_thumb) ?? 'https://via.placeholder.com/300x200' }}"
                                        class="w-full h-40 object-cover"
                                    >

                                    <!-- Overlay -->
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition"></div>

                                    <!-- Play Button -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="bg-white/80 rounded-full p-3 group-hover:scale-110 transition">
                                            ▶
                                        </div>
                                    </div>
                                </div>

                                <!--  VIDEO INFO -->
                                <div class="p-3 space-y-1">

                                    <!-- Subject Code -->
                                    <p class="text-xs text-gray-500 font-semibold">
                                        {{ $video->subject->code ?? '' }}
                                    </p>

                                    <!-- Title -->
                                    <p class="text-sm font-medium text-gray-900 line-clamp-2">
                                        {{ $video->title }}
                                    </p>

                                </div>

                            </div>

                        </button>
                    </form>

                @endforeach

            </div>
        </div>

    @endforeach

</div>

@endsection
