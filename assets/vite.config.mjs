import { defineConfig } from 'vite';
import symfony from '@symfony/reprise/vite';

export default defineConfig(({ mode }) => ({
    build: {
        emptyOutDir: true,
        sourcemap: mode !== 'production',
        assetsInlineLimit: 10 * 1024,
        rollupOptions: {
            input: {
                app: './app.js',
                export: './styles/export.css',
                dark: './styles/themes/dark.css',
                light: './styles/themes/light.css',
            },
            output: mode === 'production' ? {} : {
                entryFileNames: '[name].js',
                chunkFileNames: '[name].js',
                assetFileNames: '[name].[ext]',
            },
        },
    },
    plugins: [
        symfony({
            outputPath: '../public/build',
            publicPath: '/build/',
            stimulus: 'controllers.json',
            copy: [
                { from: './img', to: 'images' },
                { from: './node_modules/@materializecss/materialize/dist/js', to: '', pattern: /^materialize\.min\.js$/, hash: false },
            ],
        }),
    ],
}));
