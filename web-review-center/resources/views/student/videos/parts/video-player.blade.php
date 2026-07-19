@extends('student.layouts.app')

@section('title', 'Video Player')

@section('content')
@push('styles')
<style>
    .video-player-container:fullscreen,
    .video-player-container:-webkit-full-screen {
        width: 100%;
        height: 100%;
        background: #000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .video-player-container:fullscreen .video-player-media,
    .video-player-container:-webkit-full-screen .video-player-media {
        width: 100%;
        height: 100%;
        max-height: 100vh;
        object-fit: contain;
    }

    .video-player-container:fullscreen .custom-controls,
    .video-player-container:-webkit-full-screen .custom-controls {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
    }
</style>
@endpush

<div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">
    <div class="min-h-screen  py-6">
    <button onclick="window.history.back()"
        class="px-4 py-2 bg-green-500 text-white rounded mb-4">
        ← BACK
    </button>
    <div
        x-data="videoExamPlayer('{{ route('stream.video', $video->id) }}', {{ $student->type }})"
        x-init="init()"
        class="bg-black rounded-lg overflow-hidden mb-6 relative group"
    >
        @if($isWalkIn)
            <video
                x-ref="video"
                {{-- class="w-full aspect-video object-contain" --}}
                class="w-full h-[400px]"
                controls
                controlsList="nodownload"

            ></video>
        @else
        <div x-ref="playerContainer" class="video-player-container relative ">
            <video x-ref="video" class="video-player-media w-full h-[400px]" @ended="markVideoCompleted()"></video>

            <div class="custom-controls" style="
                    position: absolute;
                    bottom: 0;
                    display: flex;
                    justify-content: space-between;
                    width: 100%;">
                <div>
                <button @click="togglePlay()" class="px-4 py-2  text-white rounded">
                    <div id="play-icon" x-show="!isPlaying">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                        </svg>
                    </div>
                    <div id="pause-icon" x-show="isPlaying">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                        </svg>
                    </div>
                </button>
                </div>
                <div>
                <button @click="toggleFullscreen()" class="px-4 py-2  text-white rounded">
                    <div id="fullscreen-icon" x-show="!isFullscreen">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                        </svg>
                    </div>
                    <div id="exit-fullscreen-icon" x-show="isFullscreen">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
                          </svg>
                    </div>
                </button>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if(
        (!$student_history?->form_completed) ||
        ($student_history?->retake_allowed)
    )
        @include('student.videos.parts.exam')
    @endif
</div>

@if(!$isWalkIn)
    @include('student.videos.parts.comment')
@endif
@endsection
@push('js')

<script>
function videoExamPlayer(url, studentType) {
    return {
        videoCompleted  : false,
        examCompleted   : false,
        showExam        : false,
        iswalkin        : studentType === 1,
        isPlaying       : false,
        isFullscreen    : false,

        init() {
            this.video = this.$refs.video;

            if (Hls.isSupported()) {
                const hls = new Hls();
                hls.loadSource(url);
                hls.attachMedia(this.video);
            } else {
                this.video.src = url;
            }

            this.video.addEventListener('play', () => {
                this.isPlaying = true;
            });

            this.video.addEventListener('pause', () => {
                this.isPlaying = false;
            });

            document.addEventListener('fullscreenchange', () => {
                this.isFullscreen = !!document.fullscreenElement;
            });

        },
        togglePlay() {
            this.video.paused ? this.video.play() : this.video.pause();

        },
        toggleFullscreen() {
            if (!document.fullscreenElement) {
                this.$refs.playerContainer.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        },
        markVideoCompleted() {
            this.videoCompleted = true;

            fetch('{{ route('student.videos.completion-status', $video->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ completed: true })
            }).catch(err => console.error(err));
        },

        toggleExam() {
            if (!this.videoCompleted || this.examCompleted) return;
            this.showExam = !this.showExam;
        },

        async submitExam() {
            try {
                const response = await fetch(
                    '{{ route('student.videos.confirm-exam', $video->id) }}',
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            video_id: {{ $video->id }}
                        })
                    }
                );

                if (response.ok) {
                    this.examCompleted = true;
                    this.showExam = false;
                    alert('Exam submitted successfully!');
                } else {
                    alert('Failed to submit exam.');
                }

            } catch (error) {
                console.error(error);
                alert('Error submitting exam.');
            }
        }

    }
}

async function confirmSubmission() {
    const confirmed = confirm("Are you sure you submitted the exam?");
    let videoId = document.getElementById('videoId').value;

    if (!confirmed) return;

    try {

        const response = await fetch('/student/videos/confirm-exam/'+videoId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                video_id: 16
            })
        });

        const data = await response.json();

        if (data.status == 'success') {
            alert('✅ Exam confirmed successfully');
        }
        location.reload();



    } catch (error) {
        console.error(error);
    }
}


function showReplyForm(commentId) {
    const form = document.getElementById(`reply-form-${commentId}`);

    form.classList.toggle('hidden');
}

</script>
