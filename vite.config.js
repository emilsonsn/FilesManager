import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/_variables.scss',
                'resources/sass/alerts.scss',
                'resources/sass/app.scss',
                'resources/sass/client_economy.scss',
                'resources/sass/client.scss',
                'resources/sass/clients.scss',
                'resources/sass/credential.scss',
                'resources/sass/dashboard.scss',
                'resources/sass/emission_dashboard.scss',
                'resources/sass/financial_dashboard.scss',
                'resources/sass/header.scss',
                'resources/sass/kanban.scss',
                'resources/sass/login.scss',
                'resources/sass/menu.scss',
                'resources/sass/normalize.scss',
                'resources/sass/notify.scss',
                'resources/sass/print_client_economy.scss',
                'resources/sass/productkanban.scss',
                'resources/sass/sidebar.scss',
                'resources/sass/tasks.scss',

                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/client_economy.js',
                'resources/js/kanban.js',
                'resources/js/notify.js',
                'resources/js/product-kanban.js',
                'resources/js/sidebar.js',
                'resources/js/ticket-kanban.js',
            ],
            refresh: true,
        }),
    ],
});
