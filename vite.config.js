import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/style.css',
                'resources/sass/app.scss',
            ],
            refresh: true,
        }),
        vue(),
    ],
    build: {
        outDir: 'public/build',
        // Generate manifest.json in outDir
        rollupOptions: {
            // Ensure the vendor bundle is created
            output: {
                manualChunks: {
                    vendor: ['vue']
                }
            }
        }
    },
    publicDir: 'resources',
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
