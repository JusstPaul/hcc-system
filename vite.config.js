import react from '@vitejs/plugin-react'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import { defineConfig } from 'vite'

export default defineConfig({
  build: {
    target: 'es6',
  },
  plugins: [
    react(),
    laravel({
      input: ['resources/js/app.jsx', 'resources/scss/app.scss'],
    }),
  ],
  resolve: {
    alias: {
      ziggy: path.resolve(__dirname, 'vendor/tightenco/ziggy/dist/vue.es.js'),
      '@components': path.resolve(__dirname, 'resources/js/components/'),
    },
  },
})
