import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

// Configuración de Vite para el proyecto Nexus Hub
export default defineConfig({
  plugins: [vue()],                             // Agrega el soporte para Vue 3
  root: '.',                                    // Carpeta principal del proyecto
  publicDir: 'public',                          // Carpeta con imágenes y archivos
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
    outDir: 'dist',                             // Carpeta donde se guarda el sitio listo
    emptyOutDir: true                           // Limpia la carpeta antes de guardar
  }
});
