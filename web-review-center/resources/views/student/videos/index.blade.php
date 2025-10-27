@extends('student.layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex items-center mb-6 w-full max-w-2xl mx-auto">
        <a href="{{ route('student.dashboard') }}"
           class="flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-gray-700 font-semibold transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
        <h1 class="text-2xl font-bold ml-4">Video Player</h1>
    </div>
    <div class="flex flex-col items-center">
        <div id="videoContainer" class="relative w-full max-w-2xl bg-black rounded-lg shadow-lg overflow-hidden">
            <video id="videoPlayer"
                class="w-full h-auto rounded-t-lg"
                controlsList="nodownload"
                oncontextmenu="return false"></video>
        </div>
        <div class="flex gap-4 mt-4">
            <button id="playPauseBtn"
                class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition focus:outline-none"
                aria-label="Play/Pause">
                <svg id="playIcon" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 5v14l11-7z" />
                </svg>
                <svg id="pauseIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 4h4v16H6zm8 0h4v16h-4z" />
                </svg>
            </button>
            <button id="fullscreenBtn"
                class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-700 text-white hover:bg-gray-800 transition focus:outline-none"
                aria-label="Fullscreen">
                <svg id="fsEnterIcon" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 3H5a2 2 0 0 0-2 2v3m0 8v3a2 2 0 0 0 2 2h3m8-16h3a2 2 0 0 1 2 2v3m0 8v3a2 2 0 0 1-2 2h-3" />
                </svg>
                <svg id="fsExitIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 3h3a2 2 0 0 1 2 2v3m0 8v3a2 2 0 0 1-2 2h-3m-8-16H5a2 2 0 0 0-2 2v3m0 8v3a2 2 0 0 0 2 2h3" />
                </svg>
            </button>
            <button id="rollbackBtn"
                class="hidden items-center justify-center w-12 h-12 rounded-full bg-yellow-600 text-white hover:bg-yellow-700 transition focus:outline-none"
                aria-label="Exit Fullscreen">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 3h3a2 2 0 0 1 2 2v3m0 8v3a2 2 0 0 1-2 2h-3m-8-16H5a2 2 0 0 0-2 2v3m0 8v3a2 2 0 0 0 2 2h3" />
                </svg>
            </button>
            <div id="googleFormContainer" class="hidden mt-8 w-full max-w-2xl">
            @if($formUrl)
                <iframe src="{{ $formUrl }}"
                        width="100%"
                        height="800"
                        class="rounded-lg border shadow-md"></iframe>
            @else
                <p class="text-center text-gray-600">No form available for this video.</p>
            @endif
</div>
        </div>
        
        <!-- Comments Section -->
        <div class="mt-8 w-full max-w-2xl">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Comments</h3>
                
                <!-- Comment Form -->
                <form id="commentForm" class="mb-6">
                    @csrf
                    <div class="mb-4">
                        <textarea id="commentContent" name="content" rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Share your thoughts about this video..."></textarea>
                    </div>
                    <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Post Comment
                    </button>
                </form>
                
                <!-- Comments List -->
                <div id="commentsList" class="space-y-4">
                    <!-- Comments will be loaded here -->
                </div>
            </div>
        </div>
        
    </div>
</div>

@push('js')
<script>
    const video = document.getElementById('videoPlayer');
    const mediaSource = new MediaSource();
    const playPauseBtn = document.getElementById('playPauseBtn');
    const playIcon = document.getElementById('playIcon');
    const pauseIcon = document.getElementById('pauseIcon');
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    const fsEnterIcon = document.getElementById('fsEnterIcon');
    const fsExitIcon = document.getElementById('fsExitIcon');
    const rollbackBtn = document.getElementById('rollbackBtn');
    const videoContainer = document.getElementById('videoContainer');
    var videoId = "{{ $videoId }}";
    video.src = URL.createObjectURL(mediaSource);

    const chunkSize = 1024 * 1024; // 1MB
    let currentStart = 0;
    let fileSize = 0;
    let sourceBuffer;

    mediaSource.addEventListener('sourceopen', async () => {
        sourceBuffer = mediaSource.addSourceBuffer('video/mp4; codecs="avc1.4d401f, mp4a.40.2"');
        const sizeResp = await fetch('/student/video-file-size/' + videoId);
        const sizeJson = await sizeResp.json();
        fileSize = sizeJson.size;
        getNextChunk();
    });

    async function getNextChunk() {
        if (currentStart >= fileSize) {
            console.log("All chunks fetched. Finalizing stream...");

            // Ensure the buffer is done updating
            if (!sourceBuffer.updating) {
                finalizeStream();
            } else {
                sourceBuffer.addEventListener('updateend', finalizeStream, { once: true });
            }
            return;
        }
        const end = Math.min(currentStart + chunkSize - 1, fileSize - 1);
        const resp = await fetch(`/student/video-chunk/${videoId}?start=${currentStart}&end=${end}`);
        const chunk = await resp.arrayBuffer();
        sourceBuffer.appendBuffer(chunk);
        sourceBuffer.addEventListener('updateend', () => {
            currentStart = end + 1;
            getNextChunk();
        }, { once: true });
    }

    function finalizeStream() {
        try {
            mediaSource.endOfStream();
            // ✅ Explicitly set duration to the buffered length
            if (isFinite(video.duration)) {
                mediaSource.duration = video.duration;
            } else {
                // fallback to 1 if browser doesn't know
                mediaSource.duration = video.buffered.end(0);
            }
            console.log("Media source ended properly. Duration:", mediaSource.duration);
        } catch (e) {
            console.warn('Error ending stream:', e);
        }
    }

    // Play/Pause toggle
    playPauseBtn.addEventListener('click', () => {
        if (video.paused) {
            video.play();
        } else {
            video.pause();
        }
    });

    video.addEventListener('play', () => {
        playIcon.classList.add('hidden');
        pauseIcon.classList.remove('hidden');
    });

    video.addEventListener('pause', () => {
        playIcon.classList.remove('hidden');
        pauseIcon.classList.add('hidden');
    });

    video.addEventListener('ended', () => {
        console.log('Video ended detected ✅');
    // Hide video controls and show the Google Form
        videoContainer.classList.add('hidden');
        document.getElementById('googleFormContainer').classList.remove('hidden');
    });

    video.addEventListener('timeupdate', () => {
        if (video.duration - video.currentTime < 1) {
            console.log("Approaching end of video...");
        }
    });

    // Fullscreen toggle
    fullscreenBtn.addEventListener('click', () => {
        if (!document.fullscreenElement) {
            if (videoContainer.requestFullscreen) {
                videoContainer.requestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    });

    // Rollback/Exit Fullscreen button
    rollbackBtn.addEventListener('click', () => {
        if (document.fullscreenElement && document.exitFullscreen) {
            document.exitFullscreen();
        }
    });

    // Listen for fullscreen change to update icons and rollback button
    document.addEventListener('fullscreenchange', () => {
        if (document.fullscreenElement) {
            fsEnterIcon.classList.add('hidden');
            fsExitIcon.classList.remove('hidden');
            rollbackBtn.classList.remove('hidden');
        } else {
            fsEnterIcon.classList.remove('hidden');
            fsExitIcon.classList.add('hidden');
            rollbackBtn.classList.add('hidden');
        }
    });

    // Comments functionality
    const commentForm = document.getElementById('commentForm');
    const commentContent = document.getElementById('commentContent');
    const commentsList = document.getElementById('commentsList');

    // Load comments on page load
    loadComments();

    // Handle comment form submission
    commentForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const content = commentContent.value.trim();
        if (!content) return;

        try {
            const response = await fetch(`/student/videos/${videoId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ content: content })
            });

            const result = await response.json();
            
            if (result.success) {
                commentContent.value = '';
                loadComments(); // Reload comments
            } else {
                alert('Failed to post comment. Please try again.');
            }
        } catch (error) {
            console.error('Error posting comment:', error);
            alert('Failed to post comment. Please try again.');
        }
    });

    // Load comments function
    async function loadComments() {
        try {
            const response = await fetch(`/student/videos/${videoId}/comments`);
            const result = await response.json();
            
            commentsList.innerHTML = '';
            
            if (result.comments && result.comments.length > 0) {
                result.comments.forEach(comment => {
                    const commentElement = document.createElement('div');
                    commentElement.className = 'bg-gray-50 p-4 rounded-lg';
                    commentElement.innerHTML = `
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-semibold text-gray-800">${comment.student_name}</h4>
                            <span class="text-sm text-gray-500">${comment.created_at}</span>
                        </div>
                        <p class="text-gray-700">${comment.content}</p>
                    `;
                    commentsList.appendChild(commentElement);
                });
            } else {
                commentsList.innerHTML = '<p class="text-gray-500 text-center">No comments yet. Be the first to comment!</p>';
            }
        } catch (error) {
            console.error('Error loading comments:', error);
            commentsList.innerHTML = '<p class="text-red-500 text-center">Failed to load comments.</p>';
        }
    }
</script>
@endpush
@endsection