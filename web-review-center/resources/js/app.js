
import '../css/app.css';
import './bootstrap';
import '../css/app.css';
// import './csrf-handler';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

import Alpine from 'alpinejs'



window.Alpine = Alpine
Alpine.start()

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

    showNotification('New comment on your '+ e.video_title+' by ' + e.student + ': "' + e.message + '"');
});

function showNotification(message) {

    const container = document.getElementById('notif-container');

    const notif = document.createElement('div');

    notif.innerHTML = `
        <div style="
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            margin-top: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.4s ease;
        ">
            📢 ${message}
        </div>
    `;

    container.appendChild(notif);

    const box = notif.firstElementChild;

    // // ✅ slide in
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
    }, 3000);
}


