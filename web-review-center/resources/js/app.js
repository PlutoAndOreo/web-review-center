
import '../css/app.css';
import './bootstrap';
import '../css/app.css';
// import './csrf-handler';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

import Alpine from 'alpinejs'

window.Alpine = Alpine
Alpine.start()

const excludedPaths = [
    '/student/login',
    '/admin/login',
];

if (!excludedPaths.includes(window.location.pathname)) {

    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        forceTLS: true
    });

    window.Echo.channel('admin-comments')
    .listen('.CommentEvent', (e) => {
        console.log('EVENT RECEIVED:', e);
        showNotification(e.student+' commented on ' + e.video_title + ' video: "' + e.message + '"');
    });

    window.Echo.channel('admin-notifications')
    .listen('.VideoPublishedEvent', (e) => {
        console.log('VIDEO PUBLISHED EVENT:', e);

        showNotification('Video published: "' + e.title + '" is now available. Please refresh the page .');
    });
}
function getNotificationContainer() {
    let container = document.getElementById('notif-container');

    if (!container) {
        container = document.createElement('div');
        container.id = 'notif-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
        document.body.appendChild(container);
    }

    return container;
}

function showNotification(message) {

    const container = getNotificationContainer();

    const notif = document.createElement('div');

    notif.innerHTML = `
        <div style="
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 320px;
            max-width: 400px;
            background: white;
            border-left: 5px solid #22c55e;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.4s ease;
        ">


            <div>
                <div style="
                    font-weight: bold;
                    color: #111827;
                    margin-bottom: 3px;
                ">
                </div>
                <div style="
                    color: #6b7280;
                    font-size: 14px;
                ">
                    ${message}
                </div>
            </div>
        </div>
    `;

    container.appendChild(notif);

    const box = notif.firstElementChild;

    setTimeout(() => {
        box.style.transform = 'translateX(0)';
        box.style.opacity = '1';
    }, 100);

    setTimeout(() => {
        box.style.transform = 'translateX(100%)';
        box.style.opacity = '0';

        setTimeout(() => {
            notif.remove();
        }, 400);
    }, 8000);
}


