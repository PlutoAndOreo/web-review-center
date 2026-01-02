@extends('student.layouts.app')

@push('styles')
    @vite('resources/css/student/comments.css')
        <style>
            :fullscreen #videoContainer {
                background-color: black;
                display: flex;
                align-items: center;
                justify-content: center;
            }

        </style>
    @endpush

    @section('content')
    <div class="container mx-auto py-8">
        <div class="flex items-center mb-6 w-full max-w-2xl mx-auto">
            <a href="{{ route('student.dashboard') }}"
                class="flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-gray-700 font-semibold transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
            <h1 class="text-2xl font-bold ml-4">{{ $video_title }}</h1>
        </div>
        <div class="flex flex-col items-center">
            <div id="videoContainer" class="relative w-full max-w-2xl bg-black rounded-lg shadow-lg overflow-hidden">
                <video id="videoPlayer" class="w-full h-auto rounded-t-lg" controlsList="nodownload" preload="metadata"
                    oncontextmenu="return false"
                    src="{{ route('student.video.stream', ['id' => $videoId]) }}"></video>
            </div>
            <div class="flex gap-4 mt-4">
                <button id="playPauseBtn"
                    class="flex items-center justify-center w-12 h-12 rounded-full bg-red-600 text-white hover:bg-red-700 transition focus:outline-none"
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
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 3h3a2 2 0 0 1 2 2v3m0 8v3a2 2 0 0 1-2 2h-3m-8-16H5a2 2 0 0 0-2 2v3m0 8v3a2 2 0 0 0 2 2h3" />
                    </svg>
                </button>
            </div>

            <!-- Google Forms Section (Inline) -->
            @if($showForm && $formUrl)
                <div id="googleFormSection" class="mt-8 w-full max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-4">
                        <h3 class="text-xl font-semibold">Complete Your Review</h3>
                        <p class="text-sm opacity-90 mt-1">
                            @if($retakeAllowed && $isCompleted)
                                Retake: Please complete the form again.
                            @else
                                Please complete the form below after watching the video.
                            @endif
                        </p>
                    </div>
                    <div class="p-4">
                        <iframe 
                            id="googleFormIframe"
                            src="{{ $formUrl }}" 
                            width="100%" 
                            height="600" 
                            frameborder="0"
                            class="rounded-lg border border-gray-200">Loading ...</iframe>
                    </div>
                    <div class="p-4 border-t bg-gray-50">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <p class="text-sm text-gray-600">After submitting the form, click the button below to mark as complete.</p>
                            <button id="markCompleteBtn"
                                class="bg-red-600 text-white py-2 px-6 rounded-lg hover:bg-red-700 transition font-semibold whitespace-nowrap">
                                Mark as Complete
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($isCompleted && !$showForm && $formUrl)
                <div id="completedSection" class="mt-8 w-full max-w-4xl mx-auto bg-green-50 border-2 border-green-200 rounded-lg p-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-xl font-semibold text-green-800">Review Completed</h3>
                            <p class="text-sm text-green-600 mt-1">You have successfully completed this review. Thank you!</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if(!$formUrl)
                <div class="mt-8 w-full max-w-4xl mx-auto bg-yellow-50 border-2 border-yellow-200 rounded-lg p-6">
                    <p class="text-center text-yellow-800">No form available for this video.</p>
                </div>
            @endif

            <!-- Comments Section -->
            <div class="mt-8 w-full max-w-2xl comments-section">
                <h3>Comments</h3>

                <!-- Comment Form -->
                <form id="commentForm" class="comment-form">
                    @csrf
                    <textarea id="commentContent" name="content" rows="3"
                        placeholder="Share your thoughts about this video..."></textarea>
                    <button type="submit">Post Comment</button>
                </form>

                <!-- Comments List -->
                <div id="commentsList" class="comments-list">
                    <!-- Comments will be loaded here -->
                </div>
            </div>

        </div>
    </div>

    @push('js')
        <script>
            const video = document.getElementById('videoPlayer');
            const playPauseBtn = document.getElementById('playPauseBtn');
            const playIcon = document.getElementById('playIcon');
            const pauseIcon = document.getElementById('pauseIcon');
            const fullscreenBtn = document.getElementById('fullscreenBtn');
            const fsEnterIcon = document.getElementById('fsEnterIcon');
            const fsExitIcon = document.getElementById('fsExitIcon');
            const rollbackBtn = document.getElementById('rollbackBtn');
            const videoContainer = document.getElementById('videoContainer');
            var videoId = "{{ $videoId }}";

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
                // Show and scroll to form section if it exists
                const formSection = document.getElementById('googleFormSection');
                if (formSection) {
                    // Show form section if it was hidden
                    formSection.classList.remove('hidden');
                    formSection.style.display = 'block';
                    // Scroll to form section
                    setTimeout(() => {
                        formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        // Highlight the form section briefly
                        formSection.style.transition = 'box-shadow 0.3s';
                        formSection.style.boxShadow = '0 0 20px rgba(34, 197, 94, 0.5)';
                        setTimeout(() => {
                            formSection.style.boxShadow = '';
                        }, 2000);
                    }, 100);
                }
            });

            // Mark as complete
            const markCompleteBtn = document.getElementById('markCompleteBtn');
            if (markCompleteBtn) {
                markCompleteBtn.addEventListener('click', async () => {
                    // Confirm before marking as complete
                    if (!confirm('Have you submitted the Google Form? Click OK to mark this review as complete.')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/student/videos/${videoId}/complete`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            }
                        });

                        const result = await response.json();
                        if (result.success) {
                            // Hide form section and show completion message
                            const formSection = document.getElementById('googleFormSection');
                            if (formSection) {
                                formSection.style.display = 'none';
                            }
                            
                            // Show completion message
                            const completedHtml = `
                                <div id="completedSection" class="mt-8 w-full max-w-4xl mx-auto bg-red-50 border-2 border-red-200 rounded-lg p-6">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <h3 class="text-xl font-semibold text-red-800">Review Completed</h3>
                                            <p class="text-sm text-red-600 mt-1">Thank you for completing the review!</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            // Insert completion message before comments section
                            const commentsSection = document.querySelector('.comments-section');
                            if (commentsSection && commentsSection.parentNode) {
                                commentsSection.parentNode.insertAdjacentHTML('beforebegin', completedHtml);
                            }
                            
                            // Scroll to completion message
                            const completedSection = document.getElementById('completedSection');
                            if (completedSection) {
                                completedSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        } else {
                            alert('Failed to mark as complete. Please try again.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Failed to mark as complete. Please try again.');
                    }
                });
            }

            video.addEventListener('timeupdate', () => {
                if (video.duration && (video.duration - video.currentTime < 1)) {
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


                const isFullscreen = !!document.fullscreenElement;

                if (isFullscreen) {
                    fsEnterIcon.classList.add('hidden');
                    fsExitIcon.classList.remove('hidden');
                    rollbackBtn.classList.remove('hidden');

                    videoContainer.style.width = '100vw';
                    videoContainer.style.height = '100vh';
                    videoContainer.style.borderRadius = '0';
                    video.style.height = '100%';
                    video.style.objectFit = 'contain';
                } else {
                    fsEnterIcon.classList.remove('hidden');
                    fsExitIcon.classList.add('hidden');
                    rollbackBtn.classList.add('hidden');

                    // Unlock orientation when exiting
                    if (screen.orientation && screen.orientation.unlock) {
                        screen.orientation.unlock();
                    }

                    // Restore layout
                    videoContainer.style.width = '';
                    videoContainer.style.height = '';
                    videoContainer.style.borderRadius = '';
                    video.style.height = '';
                }

                // Sync play/pause icons
                if (video.paused) {
                    playIcon.classList.remove('hidden');
                    pauseIcon.classList.add('hidden');
                } else {
                    playIcon.classList.add('hidden');
                    pauseIcon.classList.remove('hidden');
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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            content: content
                        })
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
                            commentElement.className = 'comment-item';

                            let adminReplyHTML = '';
                            if (comment.admin_reply) {
                                adminReplyHTML = `
                            <div class="comment-reply">
                                <div class="comment-reply-header">
                                    <div class="comment-reply-author">${comment.admin_name || 'Admin'}</div>
                                    <div class="comment-reply-date">${comment.admin_replied_at}</div>
                                </div>
                                <div class="comment-reply-content">${escapeHtml(comment.admin_reply)}</div>
                            </div>
                        `;
                            }

                            commentElement.innerHTML = `
                        <div class="comment-header">
                            <div class="comment-author">${escapeHtml(comment.student_name)}</div>
                            <div class="comment-date">${comment.created_at}</div>
                        </div>
                        <div class="comment-content">${escapeHtml(comment.content)}</div>
                        ${adminReplyHTML}
                    `;
                            commentsList.appendChild(commentElement);
                        });
                    } else {
                        commentsList.innerHTML =
                            '<div class="empty-comments"><p>No comments yet. Be the first to comment!</p></div>';
                    }
                } catch (error) {
                    console.error('Error loading comments:', error);
                    commentsList.innerHTML =
                        '<div class="comment-error">Failed to load comments. Please try again.</div>';
                }
            }

            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, m => map[m]);
            }

        </script>
    @endpush
    @endsection
