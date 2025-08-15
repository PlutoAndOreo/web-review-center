@extends('student.layouts.app')

@section('content')
    <div class="container">
        <h1>Video List</h1>
        <div class="row">
            <div class="col-md-12">
               {{-- <video width="640" height="360" controls controlsList="nodownload" oncontextmenu="return false;">
                    <source src="{{ url('/video/output_streamable2.mp4') }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video> --}}
                <video id="videoPlayer" width="640" height="360"  controlsList="nodownload" oncontextmenu="return false"></video>
                <button id="customPlayBtn">Play Video</button>
                {{-- mad add kog full screen --}}
            </div>
        </div>
    </div>

    @push('javascript')
        <script>
        const video = document.getElementById('videoPlayer');
        const mediaSource = new MediaSource();
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
            }, { once: true });

            if (currentStart === 0) {
                const playBtn = document.getElementById('customPlayBtn');
                playBtn.addEventListener('click', () => {
                    video.muted = false;
                    video.play();
                    playBtn.style.display = 'none';

                });
            }
        }



        </script>
    @endpush
@endsection
