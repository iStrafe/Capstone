import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // React + Inertia pages (resources/js/pages). New pages go here.
                'resources/js/app.jsx',
                // Bootstrap site pages, until each one is rebuilt in React
                'resources/sass/app.scss',
                'resources/js/app.js',
                // Tailwind, loaded only by the Breeze guest layout (layouts/guest)
                'resources/css/app.css',
                // Tailwind utilities without preflight, scoped to the Breeze partials on /profile
                'resources/css/profile.css',
            ],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
});
