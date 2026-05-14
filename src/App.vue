<template>
  <div id="app">
    <NavBar v-if="isLoggedIn" :menuOpen="menuOpen" @toggle-menu="menuOpen = !menuOpen" />
    <router-view v-if="!isLoggedIn" @login-success="handleLoginSuccess" />
    <template v-else>
      <router-view />
      <Footer />
    </template>
  </div>
</template>

<script>
import NavBar from './components/NavBar.vue';
import Footer from './components/Footer.vue';

export default {
  name: 'App',
  components: {
    NavBar,
    Footer
  },
  data() {
    return {
      menuOpen: false,
      isLoggedIn: false
    };
  },
  created() {
    this.checkLogin();
  },
  methods: {
    checkLogin() {
      const user = localStorage.getItem('nexus_user');
      this.isLoggedIn = !!user;
      if (!this.isLoggedIn) {
        this.$router.push('/login');
      }
    },
    handleLoginSuccess() {
      this.isLoggedIn = true;
      this.$router.push('/');
    }
  },
  watch: {
    '$route'() {
      this.checkLogin();
    }
  }
};
</script>

<style>
@import '../estilos.css';
</style>