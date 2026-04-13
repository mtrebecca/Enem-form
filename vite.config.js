import { defineConfig } from 'vitest/config';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';

const isVitest = Boolean(process.env.VITEST);

export default defineConfig({
  plugins: [
    ...(isVitest
      ? []
      : [
          laravel({
            input: ['resources/css/app.css', 'resources/js/main.jsx'],
            refresh: true,
          }),
        ]),
    react(),
  ],
  server: {
    host: 'localhost',
    port: 5173,
    strictPort: true,
  },
  test: {
    environment: 'jsdom',
    setupFiles: ['resources/js/test/setup.js'],
    include: ['resources/**/*.{test,spec}.{js,jsx}'],
    css: true,
  },
});
