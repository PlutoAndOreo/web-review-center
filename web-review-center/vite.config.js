import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/css/admin/notifications.css',
                'resources/css/student/comments.css',
                'resources/js/app.js',
                'resources/js/admin/notifications.js',
                'resources/js/admin/video-list.js',
                'resources/js/admin/video-upload.js'
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 3002,
        strictPort: true,
        hmr: {
            host: 'localhost',
        },
    },
});
