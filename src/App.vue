<template>
  <div id="app">
    <!--
      BARRA DE NAVEGACIÓN
      Solo se muestra cuando el usuario inició sesión
      menuOpen: controla si el menú está abierto en celulares
    -->
    <NavBar v-if="isLoggedIn" :menuOpen="menuOpen" @toggle-menu="menuOpen = !menuOpen" />

    <div v-if="isLoggedIn" class="top-bar-actions">
      <button class="theme-toggle-btn" @click="toggleTheme" :title="darkMode ? 'Modo claro' : 'Modo oscuro'">
        <span v-if="darkMode">☀️</span>
        <span v-else>🌙</span>
      </button>
      <Notifications :notifications="notifications" @update-notifications="notifications = $event" />
    </div>

    <router-view v-if="!isLoggedIn" @login-success="handleLoginSuccess" />

    <template v-else>
      <router-view />
      <Footer />
    </template>
  </div>
</template>

<script>
// Trae los componentes de la barra y el pie de página
import NavBar from './components/NavBar.vue';
import Footer from './components/Footer.vue';
import Notifications from './components/Notifications.vue';

export default {
  name: 'App',

  components: {
    NavBar,
    Footer,
    Notifications
  },

  data() {
    return {
      menuOpen: false,
      isLoggedIn: false,
      darkMode: true,
      notifications: [
        { icon: '💬', text: 'Bienvenido a Nexus Hub! Explora todas las secciones.', time: 'ahora', read: false }
      ]
    };
  },

  created() {
    this.checkLogin();
    this.loadTheme();
  },

  // ============================================================
  // FUNCIONES
  // ============================================================
  methods: {

    // ----------------------------------------------------------
    // checkLogin: revisa si el usuario ya inició sesión antes
    // Busca en el almacenamiento del navegador si hay datos
    // ----------------------------------------------------------
    checkLogin() {
      const user = localStorage.getItem('nexus_user');
      this.isLoggedIn = !!user;   // true si existe, false si no

      if (!this.isLoggedIn) {
        this.$router.push('/login');   // va al login
      }
    },

    // ----------------------------------------------------------
    // handleLoginSuccess: cuando el usuario inicia sesión bien
    // Marca como logueado y lo manda a la página principal
    // ----------------------------------------------------------
    handleLoginSuccess() {
      this.isLoggedIn = true;
      this.$router.push('/');
    },
    loadTheme() {
      const settings = JSON.parse(localStorage.getItem('nexus_settings') || '{}');
      this.darkMode = settings.darkMode !== undefined ? settings.darkMode : true;
      document.documentElement.setAttribute('data-theme', this.darkMode ? 'dark' : 'light');
      if (settings.accentColor) {
        document.documentElement.style.setProperty('--gold', settings.accentColor);
      }
    },
    toggleTheme() {
      this.darkMode = !this.darkMode;
      const settings = JSON.parse(localStorage.getItem('nexus_settings') || '{}');
      settings.darkMode = this.darkMode;
      localStorage.setItem('nexus_settings', JSON.stringify(settings));
      document.documentElement.setAttribute('data-theme', this.darkMode ? 'dark' : 'light');
    }
  },

  // ============================================================
  // VIGILAR CAMBIOS EN LA DIRECCIÓN
  // ============================================================
  watch: {
    '$route'() {
      this.checkLogin();   // vuelve a revisar si cambió de página
    }
  }
};
</script>

<style>
/* Trae los estilos de colores y fondos desde el archivo principal */
@import '../estilos.css';
</style>
