@extends('adminlte::page')

@section('title', 'Video List')

@section('css')
    @vite('resources/css/app.css')
@endsection

@section('content')
@include('admin.components.logout')

<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Videos Management</h1>
                    <a href="{{ route('videos.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add New Video
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thumbnail</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Google Form</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Watermark</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($videos as $video)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $video->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($video->subject)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $video->subject->name }} ({{ $video->subject->code }})
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic">No subject</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if ($video->video_thumb)
                                            <img src="{{ url(ltrim($video->video_thumb, '/')) }}" 
                                                alt="Video Thumbnail" 
                                                class="w-20 h-12 object-cover rounded cursor-pointer hover:opacity-80 transition-opacity"
                                                onclick="openVideoModal({{ $video->id }}, '{{ $video->title }}')">
                                        @else
                                            <span class="text-red-500 italic">No thumbnail</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ Str::limit($video->description, 50) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        @if($video->google_form_link)
                                            <a href="{{ $video->google_form_link }}" 
                                            target="_blank" 
                                            class="text-blue-600 hover:underline">
                                                {{ Str::limit($video->google_form_link, 30) }}
                                            </a>
                                        @else
                                            <span class="text-gray-400 italic">No form</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($video->has_watermark)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Yes
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                No
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($video->created_at)->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('videos.edit', ['id' => $video->id]) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <button type="button" class="text-red-600 hover:text-red-900 btn-open-delete" data-id="{{ $video->id }}">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No videos found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $videos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>    
<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg">
        <div class="px-5 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Delete Video</h3>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-700">Are you sure you want to delete this video? This action cannot be undone.</p>
        </div>
        <div class="px-5 py-4 border-t flex justify-end gap-2">
            <button type="button" id="btn-cancel-delete" class="px-4 py-2 rounded border text-gray-700 hover:bg-gray-50">Cancel</button>
            <button type="button" id="btn-confirm-delete" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Delete</button>
        </div>
    </div>
    <form id="delete-target-form" method="POST" class="hidden"></form>
</div>

<!-- Video Preview Modal -->
<div id="videoModal" class="fixed inset-0 bg-black/80 z-50 hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-4xl rounded-lg shadow-lg">
        <div class="px-5 py-4 border-b flex justify-between items-center">
            <h3 id="videoModalTitle" class="text-lg font-semibold text-gray-800">Video Preview</h3>
            <button type="button" id="closeVideoModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-5">
            <video id="adminVideoPlayer" class="w-full h-auto max-h-96" controls playsinline>
                Your browser does not support the video tag.
            </video>
        </div>
    </div>
</div>

@section('js')
<script>
    (function(){
        const modal = document.getElementById('deleteModal');
        const cancelBtn = document.getElementById('btn-cancel-delete');
        const confirmBtn = document.getElementById('btn-confirm-delete');
        const targetForm = document.getElementById('delete-target-form');
        let currentForm = null;

        function openModal(form){
            currentForm = form;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(){
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            currentForm = null;
        }

        document.querySelectorAll('.btn-open-delete').forEach(function(btn){
            btn.addEventListener('click', function(){
                const id = this.getAttribute('data-id');
                const form = document.getElementById('delete-form-' + id);
                openModal(form);
            });
        });

        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e){
            if (e.target === modal) closeModal();
        });

        confirmBtn.addEventListener('click', function(){
            if (!currentForm) return;
            currentForm.submit();
            closeModal();
        });
    })();

    // Video Modal Functions
    function openVideoModal(videoId, title) {
        const videoModal = document.getElementById('videoModal');
        const videoPlayer = document.getElementById('adminVideoPlayer');
        const modalTitle = document.getElementById('videoModalTitle');
        
        modalTitle.textContent = title;
        videoPlayer.src = `/videos/${videoId}/stream`;
        videoModal.classList.remove('hidden');
        videoModal.classList.add('flex');
    }

    function closeVideoModal() {
        const videoModal = document.getElementById('videoModal');
        const videoPlayer = document.getElementById('adminVideoPlayer');
        
        videoPlayer.pause();
        videoPlayer.src = '';
        videoModal.classList.add('hidden');
        videoModal.classList.remove('flex');
    }

    document.getElementById('closeVideoModal').addEventListener('click', closeVideoModal);
    document.getElementById('videoModal').addEventListener('click', function(e) {
        if (e.target === this) closeVideoModal();
    });
</script>
@endsection
@stop