// Notifications Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let selectedNotifications = new Set();

    // Mark as read
    window.markAsRead = function(notificationId) {
        fetch(`/admin/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const card = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (card) {
                    card.classList.remove('unread');
                    card.classList.add('read');
                    updateUnreadCount();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to mark notification as read');
        });
    };

    // Mark all as read
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function() {
            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
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
                alert('Failed to mark all as read');
            });
        });
    }

    // Delete notification
    window.deleteNotification = function(notificationId) {
        if (!confirm('Are you sure you want to delete this notification?')) {
            return;
        }

        fetch(`/admin/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const card = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (card) {
                    card.style.transition = 'opacity 0.3s';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        updateUnreadCount();
                        if (document.querySelectorAll('.notification-card').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete notification');
        });
    };

    // Bulk delete
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const selected = Array.from(selectedNotifications);
            if (selected.length === 0) {
                alert('Please select at least one notification to delete');
                return;
            }

            if (!confirm(`Are you sure you want to delete ${selected.length} notification(s)?`)) {
                return;
            }

            fetch('/admin/notifications/bulk-delete', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ids: selected })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete notifications');
            });
        });
    }

    // Toggle reply box
    window.toggleReplyBox = function(notificationId) {
        const replyBox = document.getElementById(`replyBox-${notificationId}`);
        if (replyBox) {
            replyBox.classList.toggle('hidden');
            const textarea = replyBox.querySelector('textarea');
            if (textarea && !replyBox.classList.contains('hidden')) {
                textarea.focus();
            }
        }
    };

    // Submit reply
    window.submitReply = function(notificationId) {
        const replyBox = document.getElementById(`replyBox-${notificationId}`);
        const textarea = replyBox?.querySelector('textarea');
        const submitBtn = replyBox?.querySelector('button[onclick*="submitReply"]');

        if (!textarea || !textarea.value.trim()) {
            alert('Please enter a reply');
            return;
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
        }

        fetch(`/admin/notifications/${notificationId}/reply`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reply: textarea.value.trim() })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide reply box
                replyBox.classList.add('hidden');
                textarea.value = '';

                // Show existing reply
                const existingReplyDiv = document.getElementById(`existingReply-${notificationId}`);
                if (existingReplyDiv) {
                    existingReplyDiv.innerHTML = `
                        <div class="notification-reply-author">${data.reply.admin_name}</div>
                        <div class="notification-reply-content">${data.reply.content}</div>
                        <div class="notification-reply-date">${data.reply.replied_at}</div>
                    `;
                    existingReplyDiv.style.display = 'block';
                }
            } else {
                alert(data.message || 'Failed to send reply');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send reply');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit';
            }
        });
    };

    // Checkbox selection
    document.querySelectorAll('.notification-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const notificationId = this.value;
            if (this.checked) {
                selectedNotifications.add(notificationId);
            } else {
                selectedNotifications.delete(notificationId);
            }
            updateBulkActions();
        });
    });

    // Select all checkbox
    const selectAllCheckbox = document.getElementById('selectAllCheckbox'); 
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.notification-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;

                const notificationId = checkbox.value;
                if (checkbox.checked) {

                    selectedNotifications.add(notificationId);
                    console.log(selectedNotifications);

                } else {
                    selectedNotifications.delete(notificationId);
                }
            });
            updateBulkActions();
        });
    }

    function updateBulkActions() {
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        if (bulkDeleteBtn) {
            bulkDeleteBtn.disabled = selectedNotifications.size === 0;
        }
    }

    function updateUnreadCount() {
        fetch('/admin/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                // Update badge in sidebar if it exists
                const badge = document.querySelector('.notification-badge-count');
                if (badge) {
                    badge.textContent = data.count;
                    badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                }
            })
            .catch(error => console.error('Error updating unread count:', error));
    }

    // Load unread count on page load
    updateUnreadCount();
});

