@extends('adminlte::page')

@section('title', 'Notifications')

@section('css')
@vite('resources/css/app.css')
@vite('resources/css/admin/notifications.css')
@endsection

@section('content')
@include('admin.components.logout')

<div class="notifications-container">
    <div class="max-w-7xl mx-auto">
        <div class="notifications-header">
            <h1>Notifications</h1>
            <div class="notifications-actions">
                <button id="selectAllCheckbox" type="button" class="btn btn-secondary">Select All</button>
                <button id="bulkDeleteBtn" type="button" class="btn btn-danger" disabled>Delete Selected</button>
                <button id="markAllReadBtn" class="btn btn-primary">Mark All as Read</button>
            </div>
        </div>

        @if($notifications->count() > 0)
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div class="notification-card {{ $notification->is_read ? 'read' : 'unread' }}" data-notification-id="{{ $notification->id }}">
                        <div class="notification-header-row">
                            <div class="checkbox-container">
                                <input type="checkbox" class="notification-checkbox" value="{{ $notification->id }}">
                            </div>
                            <div class="notification-info">
                                <div class="notification-title">
                                    New Comment on Video
                                </div>
                                <div class="notification-meta">
                                    <span class="notification-badge">{{ $notification->comment->video->title ?? 'Unknown Video' }}</span>
                                    <span>{{ $notification->created_at->format('M d, Y H:i') }}</span>
                                    @if(!$notification->is_read)
                                        <span class="notification-badge" style="background: #fef3c7; color: #92400e;">New</span>
                                    @endif
                                </div>
                            </div>
                            <div class="notification-actions-row">
                                @if(!$notification->is_read)
                                    <button onclick="markAsRead({{ $notification->id }})" 
                                        class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                        Mark as Read
                                    </button>
                                @endif
                                <button onclick="deleteNotification({{ $notification->id }})" 
                                    class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                    Delete
                                </button>
                            </div>
                        </div>

                        <div class="notification-content">
                            <div class="notification-student-info">
                                <strong>{{ $notification->comment->student->first_name ?? '' }} {{ $notification->comment->student->last_name ?? '' }}</strong>
                            </div>
                            <div class="notification-comment">
                                {{ $notification->comment->content }}
                            </div>

                            @if($notification->comment->admin_reply)
                                <div class="notification-existing-reply" id="existingReply-{{ $notification->id }}">
                                    <div class="notification-reply-author">
                                        {{ $notification->comment->admin->first_name ?? 'Admin' }} {{ $notification->comment->admin->last_name ?? '' }}
                                    </div>
                                    <div class="notification-reply-content">
                                        {{ $notification->comment->admin_reply }}
                                    </div>
                                    <div class="notification-reply-date">
                                        {{ $notification->comment->admin_replied_at ? \Carbon\Carbon::parse($notification->comment->admin_replied_at)->format('M d, Y H:i') : '' }}
                                    </div>
                                </div>
                            @else
                                <div class="notification-reply-section">
                                    <button onclick="toggleReplyBox({{ $notification->id }})"
                                        class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                        {{ $notification->comment->admin_reply ? 'Edit Reply' : 'Reply' }}
                                    </button>

                                    <div id="replyBox-{{ $notification->id }}" class="notification-reply-box hidden">
                                        <textarea
                                            placeholder="Write your reply..."
                                            rows="3"></textarea>
                                        <div style="margin-top: 0.5rem; display: flex; gap: 0.5rem;">
                                            <button onclick="submitReply({{ $notification->id }})"
                                                class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                                Submit
                                            </button>
                                            <button onclick="toggleReplyBox({{ $notification->id }})"
                                                class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                    <div id="existingReply-{{ $notification->id }}" style="display: none;"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination-wrapper">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <h3>No notifications</h3>
                <p>You're all caught up!</p>
            </div>
        @endif
    </div>
</div>

@push('js')
@vite('resources/js/admin/notifications.js')
@endpush
@endsection
