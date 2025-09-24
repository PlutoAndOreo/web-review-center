@extends('adminlte::page')

@section('title', 'Edit Video')
@section('content')
<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Edit Video</h3>
        </div>

        <!-- form start -->
        <form action="{{ route('videos.update', $video->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">

                <!-- Title -->
                <div class="form-group">
                    <label for="title">Title</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="title" 
                        id="title" 
                        value="{{ old('title', $video->title) }}" 
                        required>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea 
                        class="form-control" 
                        name="description" 
                        id="description" 
                        rows="4">{{ old('description', $video->description) }}</textarea>
                </div>

                <!-- Video File -->
                <div class="form-group">
                    <label for="video">Video (MP4 only)</label>
                    <div class="custom-file">
                        <input 
                            type="file" 
                            class="custom-file-input" 
                            name="video" 
                            id="video" 
                            accept="video/mp4">
                        <label class="custom-file-label" for="video">
                            Choose file
                        </label>
                    </div>
                    @php
                        $currentFile = basename($video->file_path ?? '');
                    @endphp
                    @if($currentFile)
                        <small class="form-text text-muted">Current file: {{ $currentFile }}</small>
                    @endif
                </div>

                <!-- Google Form -->
                <div class="form-group">
                    <label for="google_form_upload">Google Form Upload (URL)</label>
                    <input 
                        type="url" 
                        class="form-control" 
                        name="google_form_upload" 
                        id="google_form_upload"
                        value="{{ old('google_form_upload', $video->google_form_upload) }}">
                </div>

                <!-- Thumbnail Preview -->
                @if(!empty($video->video_thumb))
                <div class="form-group">
                    <label>Current Thumbnail</label>
                    <div>
                        <img src="{{ url(ltrim($video->video_thumb, '/')) }}" alt="Thumbnail" style="width: 160px; height: 90px; object-fit: cover; border-radius: 6px;" />
                    </div>
                </div>
                @endif
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update Video</button>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
    <script>
        document.querySelector('.custom-file-input').addEventListener('change', function (e) {
            var fileName = document.getElementById("video").files[0].name;
            e.target.nextElementSibling.innerText = fileName;
        });
    </script>
@stop
