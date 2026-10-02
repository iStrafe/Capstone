import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // React + Inertia pages (resources/js/pages)
                'resources/js/app.jsx',
            ],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
});
