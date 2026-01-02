@extends('student.layouts.app')

@section('content')

<div class="container mx-auto py-8">
    <div class="flex items-center mb-6 w-full max-w-2xl mx-auto">
        
        <a href="{{ route('student.dashboard') }}"
           class="flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-gray-700 font-semibold transition">
            <!-- Back Icon -->
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
                <!-- Play Icon -->
                <svg id="playIcon" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 5v14l11-7z" />
                </svg>
                <!-- Pause Icon (hidden by default) -->
                <svg id="pauseIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 4h4v16H6zm8 0h4v16h-4z" />
                </svg>
            </button>
            <button id="fullscreenBtn"
                class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-700 text-white hover:bg-gray-800 transition focus:outline-none"
                aria-label="Fullscreen">
                <!-- Fullscreen Icon -->
                <svg id="fsEnterIcon" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 3H5a2 2 0 0 0-2 2v3m0 8v3a2 2 0 0 0 2 2h3m8-16h3a2 2 0 0 1 2 2v3m0 8v3a2 2 0 0 1-2 2h-3" />
                </svg>
                <!-- Exit Fullscreen Icon (hidden by default) -->
                <svg id="fsExitIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 3h3a2 2 0 0 1 2 2v3m0 8v3a2 2 0 0 1-2 2h-3m-8-16H5a2 2 0 0 0-2 2v3m0 8v3a2 2 0 0 0 2 2h3" />
                </svg>
            </button>
            <!-- Rollback/Exit Fullscreen Button (hidden by default, shown only in fullscreen) -->
            <button id="rollbackBtn"
                class="hidden items-center justify-center w-12 h-12 rounded-full bg-yellow-600 text-white hover:bg-yellow-700 transition focus:outline-none"
                aria-label="Exit Fullscreen">
                <!-- Rollback Icon (can use the same as exit fullscreen) -->
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 3h3a2 2 0 0 1 2 2v3m0 8v3a2 2 0 0 1-2 2h-3m-8-16H5a2 2 0 0 0-2 2v3m0 8v3a2 2 0 0 0 2 2h3" />
                </svg>
            </button>
        </div>
        <div id="googleFormContainer" class="w-full max-w-2xl mt-8 hidden">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Please complete the review form</h2>
        <!-- Replace the src below with your actual Google Form embed link -->
        <iframe src="https://docs.google.com/forms/d/e/1FAIpQLScEnwZrTr54Ot2_bA3-pKkavIIRuA3p61rG9S1LwZu4XtyQWQ/closedform"
                width="100%" height="600" frameborder="0" marginheight="0" marginwidth="0">
            Loading…
        </iframe>
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
            mediaSource.endOfStream();
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
    video.addEventListener('ended', () => {
    document.getElementById('googleFormContainer').classList.remove('hidden');
    // Optionally, scroll to the form
    document.getElementById('googleFormContainer').scrollIntoView({ behavior: 'smooth' });
});
</script>
@endpush
@endsection