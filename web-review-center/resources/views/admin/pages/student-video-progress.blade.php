@extends('adminlte::page')

@section('title', 'Student Video Progress')

@section('content')
@include('admin.components.logout')

<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.students.list') }}" class="btn btn-sm btn-outline-light mr-3">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <h3 class="card-title mb-0">Video Progress: {{ $student->first_name }} {{ $student->last_name }}</h3>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Video Title</th>
                            <th>Subject</th>
                            <th>Watched</th>
                            <th>Form Completed</th>
                            <th>Completed</th>
                            <th>Retake Allowed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allVideos as $video)
                            @php
                                $history = $histories->firstWhere('video_id', $video->id);
                                $watched = $history && $history->watched;
                                $formCompleted = $history && $history->form_completed;
                                $completedAt = $history && $history->form_completed_at ? \Carbon\Carbon::parse($history->form_completed_at)->format('M d, Y H:i') : '-';
                                $retakeAllowed = $history && $history->retake_allowed;
                            @endphp
                            <tr>
                                <td>{{ $video->title }}</td>
                                <td>{{ $video->subject->name ?? 'N/A' }}</td>
                                <td>
                                    @if($watched)
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($formCompleted)
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>{{ $completedAt }}</td>
                                <td>
                                    @if($retakeAllowed)
                                        <span class="badge badge-warning">Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($formCompleted)
                                        <button 
                                            type="button" 
                                            class="btn btn-sm {{ $retakeAllowed ? 'btn-warning' : 'btn-success' }} toggle-retake-btn"
                                            data-student-id="{{ $student->id }}"
                                            data-video-id="{{ $video->id }}"
                                            data-retake-allowed="{{ $retakeAllowed ? '1' : '0' }}">
                                            {{ $retakeAllowed ? 'Disable Retake' : 'Enable Retake' }}
                                        </button>
                                    @else
                                        <span class="text-muted">Not completed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No videos available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-retake-btn');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', async function() {
                const studentId = this.getAttribute('data-student-id');
                const videoId = this.getAttribute('data-video-id');
                const currentRetakeAllowed = this.getAttribute('data-retake-allowed') === '1';
                const newRetakeAllowed = !currentRetakeAllowed;
                
                if (!confirm(`Are you sure you want to ${newRetakeAllowed ? 'enable' : 'disable'} retake for this video?`)) {
                    return;
                }
                
                try {
                    const response = await fetch(`/admin/students/${studentId}/videos/${videoId}/toggle-retake`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            retake_allowed: newRetakeAllowed
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Reload page to update the status
                        window.location.reload();
                    } else {
                        alert('Failed to update retake status. Please try again.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to update retake status. Please try again.');
                }
            });
        });
    });
</script>
@endpush
@endsection

