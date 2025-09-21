import { defineConfig } from "vite";
import tailwindcss from "@tailwindcss/vite";
import ViteRestart from 'vite-plugin-restart';
import viteCompression from 'vite-plugin-compression';
import checker from 'vite-plugin-checker';

export default defineConfig(({command}) => ({
    base: command === 'serve' ? '/' : '/dist/',
    plugins: [
        checker({
            stylelint: {
                lintCommand: 'stylelint "src/**/*.css"'
            },
            eslint: {
                lintCommand: 'eslint "src/**/*.js"',
                useFlatConfig: true,
                dev: {
                    overrideConfig: {
                        cache: true,
                    }
                }
            }
        }),
        tailwindcss(),
        viteCompression({
            filter: /\.(mjs|json|css|map)$/i,
        }),
    ],
    build: {
        manifest: true,
        rollupOptions: {
            input: {
                app: 'src/js/app.js',
            }
        },
        outDir: 'web/dist'
    },
    server: {
        port: 5173,
        host: '0.0.0.0',
        strictPort: true,
        cors: {
            origin: /https?:\/\/([A-Za-z0-9\-\.]+)?(localhost|\.local|\.test|\.site)(?::\d+)?$/
        },
        origin: "https://growlr.ddev.site:5173",
        watch: {
            ignored: ['./storage/**', './vendor/**', './web/**'],
        }
    }
}));