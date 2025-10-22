import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/styles.css', 'resources/js/app.js', 'resources/js/usuarios/login.js', 'resources/js/usuarios/resend-verification.js', 'resources/js/usuarios/forgot-password.js', 'resources/js/usuarios/reset-password.js', 'resources/js/admin/editar-usuario.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
