@extends('adminlte::page')

@section('title', 'Video Upload')

@section('css')
@vite('resources/css/app.css')

    <style>
        /* Loading overlay styles */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            /* hidden by default */
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .overlay.show {
            display: flex;
        }

        .loading-text {
            color: white;
            font-size: 1.25rem;
            font-weight: bold;
            animation: pulse 1.5s infinite;
        }

		@keyframes pulse {
			0%, 100% { opacity: 1; }
			50% { opacity: 0.6; }
		}

        /* Progress bar removed */

		/* Modal */
		.modal-overlay {
			position: fixed;
			inset: 0;
			background: rgba(0,0,0,0.6);
			display: none;
			align-items: center;
			justify-content: center;
			z-index: 10000;
		}

		.modal-overlay.show { display: flex; }

		.modal-card {
			width: 100%;
			max-width: 480px;
			background: #fff;
			border-radius: 0.75rem;
			padding: 1.25rem 1.25rem 1rem;
			box-shadow: 0 10px 25px rgba(0,0,0,0.2);
		}

		/* Loading spinner */
		.spinner {
			width: 40px;
			height: 40px;
			border: 4px solid #e5e7eb; /* gray-200 */
			border-top-color: #3b82f6; /* blue-500 */
			border-radius: 9999px;
			animation: spin 0.8s linear infinite;
			margin-right: 12px;
		}

		@keyframes spin {
			to { transform: rotate(360deg); }
        }

    </style>
    @endsection

    @section('content')
	@include('admin.components.logout')

    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-10">
        <div class="w-full max-w-2xl bg-white p-8 rounded-2xl shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('videos.list') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Videos
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Upload New Video</h2>
                <div></div>
            </div>

            <form action="{{ route('videos.upload') }}" method="POST" enctype="multipart/form-data"
                class="space-y-5" id="uploadForm">
                @csrf

                {{-- Title --}}
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Title <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>
                @error('title')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
				<p class="text-sm text-red-600 mt-1" id="error-title"></p>

                {{-- Description --}}
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">{{ old('description') }}</textarea>
                </div>
                @error('description')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
				<p class="text-sm text-red-600 mt-1" id="error-description"></p>

                {{-- Video file --}}
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Video File <span
                            class="text-red-500">*</span></label>
                    <input type="file" name="video"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 cursor-pointer focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>
                @error('video')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
				<p class="text-sm text-red-600 mt-1" id="error-video"></p>

                {{-- Subject Selection --}}
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Subject <span
                            class="text-red-500">*</span></label>
                    <select name="subject_id" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="">Select a subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }} ({{ $subject->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('subject_id')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
				<p class="text-sm text-red-600 mt-1" id="error-subject_id"></p>

                {{-- Google Form Link --}}
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Google Form Link (for exam after video) <span
                            class="text-red-500">*</span></label>
                    <input type="url" name="google_form_link"
                        value="{{ old('google_form_link') }}"
                        placeholder="https://forms.google.com/..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>
                @error('google_form_link')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
				<p class="text-sm text-red-600 mt-1" id="error-google_form_link"></p>

                {{-- Watermark Option --}}
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="has_watermark" value="1" {{ old('has_watermark') ? 'checked' : '' }}
                            class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700 font-semibold">Add watermark to video for students</span>
                    </label>
                </div>

                {{-- Submit button --}}
                <div class="mt-10">
					<button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded disabled:opacity-60 disabled:cursor-not-allowed"
                        id="btn-upload">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

	<!-- Progress Modal -->
	<div class="modal-overlay" id="uploadModal" aria-hidden="true">
		<div class="modal-card">
			<div class="flex items-center">
				<div class="spinner"></div>
				<div>
					<h3 class="text-lg font-semibold text-gray-800">Processing your video...</h3>
					<p class="text-sm text-gray-600" id="progressLabel">Starting...</p>

				</div>
			</div>
        </div>
    </div>
    @stop

        @section('js')
		<script>
			(function () {
				const form = document.getElementById('uploadForm');
				const labelEl = document.getElementById('progressLabel');
				const btn = document.getElementById('btn-upload');
				const modal = document.getElementById('uploadModal');
				let pollTimer = null;
				let uploadToken = null;
				let processStartMs = 0;
				let lastProcessPercent = 0;
				let lastProcessTs = 0;

                function setProgress(percent, label) { if (label) labelEl.textContent = label; }

				function hasClientErrors() {
					let hasError = false;
					const title = form.querySelector('input[name="title"]').value.trim();
					const video = form.querySelector('input[name="video"]').files[0];
					const subject = form.querySelector('select[name="subject_id"]').value;
					const link = form.querySelector('input[name="google_form_link"]').value.trim();
					if (!title) { document.getElementById('error-title').textContent = 'The title field is required.'; hasError = true; }
					if (!video) { document.getElementById('error-video').textContent = 'The video field is required.'; hasError = true; }
					if (!subject) { document.getElementById('error-subject_id').textContent = 'The subject field is required.'; hasError = true; }
					if (!link) { document.getElementById('error-google_form_link').textContent = 'The google form link field is required.'; hasError = true; }
					return hasError;
				}

				form.addEventListener('submit', function (e) {
                e.preventDefault();
                
					const formData = new FormData(form);
					const xhr = new XMLHttpRequest();
					const startTime = Date.now();

					btn.disabled = true;

					// clear previous ajax errors
					['title','description','video','subject_id','google_form_link'].forEach(function(name){
						const el = document.getElementById('error-' + name);
						if (el) el.textContent = '';
					});

                    // Do not show modal if there are client-side errors
					if (hasClientErrors()) {
						btn.disabled = false;
						return;
					}

                    // show and initialize minimal loading UI only when there are no errors
					modal.classList.add('show');
					setProgress(0, 'Starting upload...');
					// set token to correlate server progress
					uploadToken = (Math.random().toString(36).slice(2)) + Date.now().toString(36);
					formData.append('upload_token', uploadToken);

                    // progress bar removed; keep label-only updates
                    xhr.upload.onprogress = function () { setProgress(0, 'Uploading...'); };

						xhr.onreadystatechange = function () {
						if (xhr.readyState === 2) {
								// begin polling server-side processing progress
								labelEl.textContent = 'Processing on server...';
								processStartMs = Date.now();
								lastProcessPercent = 0;
								lastProcessTs = processStartMs;
								if (uploadToken && !pollTimer) {
									pollTimer = setInterval(async function() {
										try {
											const res = await fetch("{{ route('videos.progress', ['token' => 'TOKEN_PLACEHOLDER']) }}".replace('TOKEN_PLACEHOLDER', uploadToken), { headers: { 'Accept': 'application/json' } });
											const json = await res.json();
											if (typeof json.percent === 'number') {
												setProgress(json.percent, 'Processing on server...');
												const now = Date.now();
												const elapsedProcSec = (now - processStartMs) / 1000;
												let rate = 0; // percent per second
												if (json.percent > 0 && elapsedProcSec > 0) {
													rate = json.percent / elapsedProcSec;
												}
												lastProcessPercent = json.percent;
												lastProcessTs = now;
											}
											if (json.percent >= 100) {
												labelEl.textContent = 'Finalizing...';
												clearInterval(pollTimer); pollTimer = null;
											}
										} catch (_) {}
									}, 800);
								}
						}
						if (xhr.readyState === 4) {
							btn.disabled = false;
								const isJSON = (xhr.getResponseHeader('Content-Type') || '').includes('application/json');
								let data = null;
								if (isJSON) {
									try { data = JSON.parse(xhr.responseText); } catch (_) {}
								}

								if (xhr.status >= 200 && xhr.status < 300) {
									labelEl.textContent = 'Completed';
									if (data && data.redirect) {
												modal.classList.remove('show');
												window.location.href = data.redirect;
									} else if (data && data.message) {
												modal.classList.remove('show');
												alert(data.message);
									} else {
												modal.classList.remove('show');
												window.location.reload();
									}
							} else if (xhr.status === 422 && data) {
									// Laravel validation errors
									labelEl.textContent = 'Validation error';
									if (data.errors) {
										Object.keys(data.errors).forEach(function (field) {
											const el = document.getElementById('error-' + field);
											if (el) el.textContent = (data.errors[field] || []).join(' ');
										});
									}
								// hide modal since submission has validation errors
								modal.classList.remove('show');
									if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
                    } else {
									labelEl.textContent = 'Failed';
								}
                    }
                };

					xhr.open('POST', "{{ route('videos.upload') }}", true);
					xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send(formData);
            });
			})();
		</script>
    @endsection
