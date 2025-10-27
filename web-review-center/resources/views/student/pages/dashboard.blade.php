@extends('student.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Video List</h1>
            <p class="text-gray-600">Browse the latest videos below.</p>
        </div>
        <a href="{{ route('student.info') }}" class="px-4 py-2 rounded bg-gray-800 text-white">My Info</a>
    </div>
    
    <!-- Subject Filter -->
    <div class="mb-6">
        <form method="GET" action="{{ route('student.dashboard') }}" class="flex items-center gap-4">
            <label for="subject" class="text-sm font-medium text-gray-700">Filter by Subject:</label>
            <select name="subject" id="subject" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="all" {{ $selectedSubject === 'all' || !$selectedSubject ? 'selected' : '' }}>All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ $selectedSubject == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }} ({{ $subject->code }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    
    <h2 class="text-xl font-semibold mb-3">Latest Videos</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($videos as $video)
            <a href="{{ route('student.videos', ['id' => $video->id]) }}" class="block border rounded-lg overflow-hidden hover:shadow">
                <img src="{{ url(ltrim($video->video_thumb, '/')) }}" alt="{{ $video->title }}" class="w-full h-40 object-cover">
                <div class="p-3">
                    <div class="font-medium">{{ $video->title }}</div>
                    @if($video->subject)
                        <div class="text-xs text-blue-600 font-medium mt-1">{{ $video->subject->name }} ({{ $video->subject->code }})</div>
                    @endif
                    <div class="text-sm text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit($video->description, 80) }}</div>
                </div>
            </a>
        @empty
            <p class="text-gray-600">No videos yet.</p>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div class="mt-6 flex justify-center">
        {{ $videos->links() }}
    </div>
</div>
@endsection