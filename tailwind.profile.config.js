/** Tailwind build for resources/css/profile.css (see the comment there). */
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/profile/**/*.blade.php',
        './resources/views/components/**/*.blade.php',
    ],

    important: '.profile-page',

    darkMode: 'class',

    // No preflight, and no .container component: that one is not scoped and would fight Bootstrap's .container.
    corePlugins: {
        preflight: false,
        container: false,
    },

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
        },
    },
};
