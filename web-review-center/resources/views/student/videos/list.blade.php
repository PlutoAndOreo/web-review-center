@extends('student.layouts.app')

@section('title', 'Videos')

@section('content')

<h1 class="mb-4 text-center">Subjects</h1>
<div class="main-container space-y-8">
    @foreach ($videos as $subjectId => $videos)
        <div class="per-video-container">
            
            <!-- Subject Title -->
            <h2 class="text-xl font-semibold mb-4">
                {{ optional($videos->first()->subject)->name ?? 'Unknown Subject' }}
            </h2>

            <!-- Grid: 4 columns -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                @foreach ($videos as $video)
                    <div class="p-4 border rounded-lg shadow-sm">
                        <h3 class="font-medium">{{ $video->title }}</h3>
                        <p class="text-sm text-gray-500">{{ $video->description }}</p>
                    </div>
                @endforeach

            </div>

        </div>
    @endforeach
</div>

@endsection
