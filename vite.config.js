import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [vue()],
  root: '.',
  publicDir: 'public',
  server: {
    port: 5173,
    open: false,
    proxy: {
      '/api': {
        target: 'http://localhost/proyecto%20dise%C3%B1o%20web-semana5/proyecto%20dise%C3%B1o%20web-semana5/proyecto%20dise%C3%B1o%20web-seman3(1)/proyecto%20dise%C3%B1o%20web-semana2/proyecto%20dise%C3%B1o%20web',
        changeOrigin: true
      }
    }
  },
  build: {
    outDir: 'dist',
    emptyOutDir: true
  }
});