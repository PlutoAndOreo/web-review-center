@extends('adminlte::page')

@section('title', 'Notifications')

@section('css')
@vite('resources/css/app.css')
@endsection

@section('content')
@include('admin.components.logout')

<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                    <button id="markAllReadBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Mark All as Read
                    </button>
                </div>

                @if($notifications->count() > 0)
                    <div class="space-y-4">
                        @foreach($notifications as $notification)
                            <div class="border rounded-lg p-4 {{ $notification->is_read ? 'bg-gray-50' : 'bg-blue-50 border-blue-200' }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            @if(!$notification->is_read)
                                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                            @endif
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ $notification->type === 'comment' ? 'New Comment' : 'Notification' }}
                                            </h3>
                                        </div>
                                        <p class="text-gray-700 mt-1">{{ $notification->message }}</p>
                                        <div class="mt-2 text-sm text-gray-500">
                                            <span>Video: {{ $notification->comment->video->title ?? 'Unknown' }}</span>
                                            <span class="mx-2">•</span>
                                            <span>Student: {{ $notification->comment->student->first_name ?? '' }} {{ $notification->comment->student->last_name ?? '' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-500">{{ $notification->created_at->format('M d, Y H:i') }}</span>
                                        @if(!$notification->is_read)
                                            <button onclick="markAsRead({{ $notification->id }})" 
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Mark as Read
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 1 0-15 0v5h5l-5 5-5-5h5v-5a7.5 7.5 0 1 0 15 0v5z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                        <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function markAsRead(notificationId) {
    fetch(`/admin/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

document.getElementById('markAllReadBtn').addEventListener('click', function() {
    fetch('/admin/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>
@endsection
