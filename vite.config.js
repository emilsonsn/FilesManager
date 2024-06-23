import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/sidebar.js',
                'resources/js/tags.js',

                'resources/sass/_variables.scss',
                'resources/sass/app.scss',
                'resources/sass/dashboard.scss',
                'resources/sass/header.scss',
                'resources/sass/login.scss',
                'resources/sass/menu.scss',
                'resources/sass/normalize.scss',
                'resources/sass/sidebar.scss',
            ],
            refresh: true,
        }),
    ],
});
