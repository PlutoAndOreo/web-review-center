import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // allow access from Docker host
        port: 3000,       // must match Docker port mapping
        hmr: {
            host: 'localhost', // host for hot module reload
        },
    },
});
