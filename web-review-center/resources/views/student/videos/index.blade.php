@extends('student.layouts.app')

@section('content')

<div class="container">
    <h1>Video List</h1>
    <div class="row">
        <div class="col-md-12">
            <div id="videoContainer" style="position: relative; background:black;">

                <video id="videoPlayer" style="width:100%; height:auto;" controlsList="nodownload"
                    oncontextmenu="return false"></video>

                <div style="position:absolute; bottom:10px; left:10px; z-index:10;">
                    <button id="customPlayBtn">Play Video</button>
                    <button id="fullscreenBtn">Fullscreen</button>
                </div>

            </div>
            {{-- mad add kog full screen --}}
        </div>
    </div>
</div>

@push('javascript')
    <script>
        const video = document.getElementById('videoPlayer');
        const mediaSource = new MediaSource();
        const playBtn = document.getElementById('customPlayBtn');
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const videoContainer = document.getElementById('videoContainer');

        video.src = URL.createObjectURL(mediaSource);

        const chunkSize = 1024 * 1024; // 1MB
        let currentStart = 0;
        let fileSize = 0;
        let sourceBuffer;

        mediaSource.addEventListener('sourceopen', async () => {
            sourceBuffer = mediaSource.addSourceBuffer('video/mp4; codecs="avc1.4d401f, mp4a.40.2"');

            const sizeResp = await fetch('/video-file-size');
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
            const resp = await fetch(`/video-chunk?start=${currentStart}&end=${end}`);
            const chunk = await resp.arrayBuffer();

            sourceBuffer.appendBuffer(chunk);
            sourceBuffer.addEventListener('updateend', () => {
                currentStart = end + 1;
                getNextChunk();
            }, {
                once: true
            });

            if (currentStart === 0) {
                const playBtn = document.getElementById('customPlayBtn');
                playBtn.addEventListener('click', () => {
                    video.muted = false;
                    video.play();
                    playBtn.style.display = 'none';

                });
            }
        }

        fullscreenBtn.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                if (videoContainer.requestFullscreen) {
                    videoContainer.requestFullscreen();
                } else if (videoContainer.webkitRequestFullscreen) {
                    videoContainer.webkitRequestFullscreen();
                } else if (videoContainer.msRequestFullscreen) {
                    videoContainer.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        });

    </script>
@endpush
@endsection
