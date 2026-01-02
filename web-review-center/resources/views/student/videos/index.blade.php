@extends('student.layouts.app')

@push('styles')
    @vite('resources/css/student/comments.css')
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :fullscreen #videoContainer {
            background-color: black;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #videoContainer {
            position: relative;
            width: 100%;
            background: #000;
        }
        
        #videoPlayer {
            width: 100%;
            height: auto;
            display: block;
        }
        
        @media (max-width: 768px) {
            .video-controls {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .video-controls button {
                margin: 5px;
            }
            
            .card-footer .row {
                flex-direction: column;
            }
            
            .card-footer .col-md-4 {
                width: 100%;
                margin-top: 10px;
            }
            
            .card-footer .col-md-8 {
                width: 100%;
            }
            
            .embed-responsive {
                min-height: 400px;
            }
        }
        
        @media (max-width: 576px) {
            .content-header h1 {
                font-size: 1.5rem;
            }
            
            .breadcrumb {
                font-size: 0.875rem;
            }
        }
    </style>
@endpush

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $video_title }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('student.videos.list') }}">Videos</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($video_title, 30) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-12 col-lg-10 col-xl-8">
                    <!-- Video Player Card -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-play-circle mr-1"></i>
                                Video Player
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('student.videos.list') }}" class="btn btn-sm btn-default">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="videoContainer" class="w-100">
                                <video id="videoPlayer" class="w-100" controlsList="nodownload" preload="metadata"
                                    oncontextmenu="return false"
                                    src="{{ route('student.video.stream', ['id' => $videoId]) }}"></video>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-center align-items-center video-controls">
                                <button id="playPauseBtn" class="btn btn-danger btn-sm mr-2" aria-label="Play/Pause">
                                    <i id="playIcon" class="fas fa-play"></i>
                                    <i id="pauseIcon" class="fas fa-pause d-none"></i>
                                </button>
                                <button id="fullscreenBtn" class="btn btn-secondary btn-sm" aria-label="Fullscreen">
                                    <i id="fsEnterIcon" class="fas fa-expand"></i>
                                    <i id="fsExitIcon" class="fas fa-compress d-none"></i>
                                </button>
                                <button id="rollbackBtn" class="btn btn-warning btn-sm ml-2 d-none" aria-label="Exit Fullscreen">
                                    <i class="fas fa-compress"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Google Forms Section -->
                    @if($showForm && $formUrl)
                        <div class="card card-success card-outline" id="googleFormSection">
                            <div class="card-header bg-danger">
                                <h3 class="card-title">
                                    <i class="fas fa-clipboard-list mr-1"></i>
                                    Complete Your Review
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="icon fas fa-info-circle"></i>
                                    @if($retakeAllowed && $isCompleted)
                                        <strong>Retake:</strong> Please complete the form again.
                                    @else
                                        Please complete the form below after watching the video.
                                    @endif
                                </div>
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe 
                                        id="googleFormIframe"
                                        src="{{ $formUrl }}" 
                                        class="embed-responsive-item"
                                        frameborder="0">Loading ...</iframe>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12 col-md-8">
                                        <p class="text-muted mb-0">
                                            <small>After submitting the form, click the button below to mark as complete.</small>
                                        </p>
                                    </div>
                                    <div class="col-12 col-md-4 text-right">
                                        <button id="markCompleteBtn" class="btn btn-danger btn-block">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Mark as Complete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($isCompleted && !$showForm && $formUrl)
                        <div class="card card-success" id="completedSection">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Review Completed
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success mb-0">
                                    <i class="icon fas fa-check"></i>
                                    You have successfully completed this review. Thank you!
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if(!$formUrl)
                        <div class="card card-warning">
                            <div class="card-body">
                                <div class="alert alert-warning mb-0">
                                    <i class="icon fas fa-exclamation-triangle"></i>
                                    No form available for this video.
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Comments Section -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-comments mr-1"></i>
                                Comments
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Comment Form -->
                            <form id="commentForm" class="mb-4">
                                @csrf
                                <div class="form-group">
                                    <label for="commentContent">Share your thoughts about this video</label>
                                    <textarea 
                                        id="commentContent" 
                                        name="content" 
                                        class="form-control" 
                                        rows="3"
                                        placeholder="Write your comment here..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane mr-1"></i>
                                    Post Comment
                                </button>
                            </form>

                            <hr>

                            <!-- Comments List -->
                            <div id="commentsList">
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-spinner fa-spin"></i> Loading comments...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

    @push('js')
        <!-- AdminLTE JS -->
        <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            
            // Initialize AdminLTE tooltips if available
            if (typeof $ !== 'undefined' && $.fn.tooltip) {
                $('[data-toggle="tooltip"]').tooltip();
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
                playIcon.classList.add('d-none');
                pauseIcon.classList.remove('d-none');
            });

            video.addEventListener('pause', () => {
                playIcon.classList.remove('d-none');
                pauseIcon.classList.add('d-none');
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
                                <div class="card card-success" id="completedSection">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Review Completed
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-success mb-0">
                                            <i class="icon fas fa-check"></i>
                                            Thank you for completing the review!
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
                    fsEnterIcon.classList.add('d-none');
                    fsExitIcon.classList.remove('d-none');
                    rollbackBtn.classList.remove('d-none');

                    videoContainer.style.width = '100vw';
                    videoContainer.style.height = '100vh';
                    videoContainer.style.borderRadius = '0';
                    video.style.height = '100%';
                    video.style.objectFit = 'contain';
                } else {
                    fsEnterIcon.classList.remove('d-none');
                    fsExitIcon.classList.add('d-none');
                    rollbackBtn.classList.add('d-none');

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
                    playIcon.classList.remove('d-none');
                    pauseIcon.classList.add('d-none');
                } else {
                    playIcon.classList.add('d-none');
                    pauseIcon.classList.remove('d-none');
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
                            <div class="card card-info card-outline mt-3">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="comment-reply-author font-weight-bold">
                                            <i class="fas fa-user-shield mr-1"></i>
                                            ${escapeHtml(comment.admin_name || 'Admin')}
                                        </div>
                                        <div class="comment-reply-date text-muted small">${comment.admin_replied_at}</div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="comment-reply-content">${escapeHtml(comment.admin_reply)}</div>
                                </div>
                            </div>
                        `;
                            }

                            commentElement.className = 'card card-outline mb-3';
                            commentElement.innerHTML = `
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="comment-author font-weight-bold">${escapeHtml(comment.student_name)}</div>
                                <div class="comment-date text-muted small">${comment.created_at}</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="comment-content">${escapeHtml(comment.content)}</div>
                            ${adminReplyHTML}
                        </div>
                    `;
                            commentsList.appendChild(commentElement);
                        });
                    } else {
                        commentsList.innerHTML =
                            '<div class="alert alert-info text-center"><i class="fas fa-info-circle mr-1"></i>No comments yet. Be the first to comment!</div>';
                    }
                } catch (error) {
                    console.error('Error loading comments:', error);
                    commentsList.innerHTML =
                        '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-1"></i>Failed to load comments. Please try again.</div>';
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
