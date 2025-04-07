import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources',
        },
    },
    server: {
        host: '0.0.0.0',
        port: 8000,
        hmr: {
            host: '172.20.10.5'  // Replace with your actual IP
        },
    },
});
