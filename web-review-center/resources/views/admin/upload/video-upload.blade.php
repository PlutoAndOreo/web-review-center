@extends('adminlte::page')

@section('title', 'Video Upload')

@section('css')
	@vite(['resources/css/app.css','resources/css/admin/video-upload.css'])

@endsection

    @section('content')
	@include('admin.components.logout')

    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-10">
        <div class="w-full max-w-2xl bg-white p-8 rounded-2xl shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('admin.videos.list') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Videos
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Upload New Video</h2>
                <div></div>
            </div>

            <form action="{{ route('admin.videos.upload') }}" method="POST" enctype="multipart/form-data"
                class="space-y-5" id="uploadForm">
                @csrf

                {{-- Title --}}
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Title <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>
                @error('title')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
				<p class="text-sm text-red-600 mt-1" id="error-title"></p>

                {{-- Description --}}
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">{{ old('description') }}</textarea>
                </div>
                @error('description')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
				<p class="text-sm text-red-600 mt-1" id="error-description"></p>

                {{-- Video file --}}
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Video File <span
                            class="text-red-500">*</span></label>
                    <input type="file" name="video"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 cursor-pointer focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>
                @error('video')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
				<p class="text-sm text-red-600 mt-1" id="error-video"></p>

                {{-- Subject Selection --}}
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Subject <span
                            class="text-red-500">*</span></label>
                    <select name="subject_id" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="">Select a subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }} ({{ $subject->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('subject_id')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
				<p class="text-sm text-red-600 mt-1" id="error-subject_id"></p>

                {{-- Google Form Link --}}
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Google Form Link (for exam after video) <span
                            class="text-red-500">*</span></label>
                    <input type="url" name="google_form_link"
                        value="{{ old('google_form_link') }}"
                        placeholder="https://forms.google.com/..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>
                @error('google_form_link')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
				<p class="text-sm text-red-600 mt-1" id="error-google_form_link"></p>
            

                {{-- Submit button --}}
                <div class="mt-10">
					<button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded disabled:opacity-60 disabled:cursor-not-allowed"
                        id="btn-upload">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

	<!-- Progress Modal -->
	<div class="modal-overlay" id="uploadModal" aria-hidden="true">
		<div class="modal-card">
			<div class="flex items-center">
				<div class="spinner"></div>
				<div>
					<h3 class="text-lg font-semibold text-gray-800">Processing your video...</h3>
					<p class="text-sm text-gray-600" id="progressLabel">Starting...</p>

				</div>
			</div>
        </div>
    </div>
    @stop

        @section('js')
        <script>
            const uploadUrl = "{{ route('admin.videos.upload') }}";
            const progressUrlBase = "{{ url('videos/progress') }}";
        </script>
        
		@vite('resources/js/admin/video-upload.js')
    @endsection
