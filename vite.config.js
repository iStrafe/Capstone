import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Bootstrap site pages
                'resources/sass/app.scss',
                'resources/js/app.js',
                // Tailwind, loaded only by the Breeze layouts (layouts/guest, layouts/app)
                'resources/css/app.css',
                'resources/js/breeze.js',
                // Tailwind utilities without preflight, scoped to the Breeze partials on /profile
                'resources/css/profile.css',
            ],
            refresh: true,
        }),
    ],
});
