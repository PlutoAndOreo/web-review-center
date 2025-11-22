@extends('adminlte::page')

@section('title', 'Edit Video')
@section('content')
@include('admin.components.logout')
<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.videos.list') }}" class="btn btn-sm btn-outline-light mr-3">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <h3 class="card-title mb-0">Edit Video</h3>
            </div>
        </div>

        <!-- form start -->
        <form action="{{ route('admin.videos.update', $video->id) }}" method="POST" enctype="multipart/form-data" id="editVideoForm">
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
                        <small class="form-text text-muted ">Current file: {{ $currentFile }}</small>
                    @endif
                </div>

                <!-- subject -->
                <div class="form-group">
                    <label for="subject_id">Subject</label>
                    <select id="subject_id" name="subject" class="form-control" required>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ $subject->id == $video->subject_id ? 'selected' : '' }}>
                            {{ $subject->code }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Google Form Link -->
                <div class="form-group">
                    <label for="google_form_link">Google Form Link</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="google_form_link" 
                        id="google_form_link"
                        value="{{ old('google_form_link', $video->google_form_link) }}">
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
                <button type="submit" class="btn btn-primary" id="btn-update">Update Video</button>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
    <style>
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center; z-index: 10000; }
        .modal-overlay.show { display: flex; }
        .modal-card { width: 100%; max-width: 480px; background: #fff; border-radius: 12px; padding: 18px 18px 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .spinner { width: 40px; height: 40px; border: 4px solid #e5e7eb; border-top-color: #3b82f6; border-radius: 9999px; animation: spin 0.8s linear infinite; margin-right: 12px; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    <div class="modal-overlay" id="updateModal" aria-hidden="true">
        <div class="modal-card">
            <div class="d-flex align-items-center">
                <div class="spinner"></div>
                <div>
                    <h5 class="mb-1">Updating your video...</h5>
                    <p id="updateLabel" class="mb-0 text-muted">Please wait</p>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelector('.custom-file-input').addEventListener('change', function (e) {
            var selected = document.getElementById('video').files[0];
            if (selected) {
                e.target.nextElementSibling.innerText = selected.name;
            } else {
                e.target.nextElementSibling.innerText = 'Choose file';
            }
        });

        (function(){
            const form = document.getElementById('editVideoForm');
            const btn = document.getElementById('btn-update');
            const modal = document.getElementById('updateModal');
            const labelEl = document.getElementById('updateLabel');
            form.addEventListener('submit', function(e){
                e.preventDefault();
                const formData = new FormData(form);
                // attach token for processing progress (server will handle caching)
                const uploadToken = (Math.random().toString(36).slice(2)) + Date.now().toString(36);
                formData.append('upload_token', uploadToken);

                btn.disabled = true;
                modal.classList.add('show');
                labelEl.textContent = 'Submitting...';

                const xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function(){
                    if (xhr.readyState === 4) {
                        btn.disabled = false;
                        const isJSON = (xhr.getResponseHeader('Content-Type') || '').includes('application/json');
                        let data = null;
                        if (isJSON) {
                            try { data = JSON.parse(xhr.responseText); } catch (_) {}
                        }
                        if (xhr.status >= 200 && xhr.status < 300) {
                            labelEl.textContent = 'Completed';
                            modal.classList.remove('show');
                            if (data && data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.reload();
                            }
                        } else if (xhr.status === 422 && data && data.errors) {
                            modal.classList.remove('show');
                            // show basic alerts for now; page has no field placeholders
                            alert('Validation error. Please check your inputs.');
                        } else {
                            modal.classList.remove('show');
                            alert('Update failed.');
                        }
                    }
                };
                xhr.open('POST', form.getAttribute('action'), true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send(formData);
            });
        })();
    </script>
@stop
