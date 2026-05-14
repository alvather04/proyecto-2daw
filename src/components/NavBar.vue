<template>
  <header>
    <div class="logo">Nexus Hub</div>
    <button class="menu-toggle" :class="{ active: menuOpen }" @click="$emit('toggle-menu')">
      <span></span>
      <span></span>
      <span></span>
    </button>
    <nav :class="{ active: menuOpen }">
      <ul>
        <li><router-link to="/">Inicio</router-link></li>
        <li><router-link to="/forum">Foro</router-link></li>
        <li><router-link to="/profile">Mi Perfil</router-link></li>
        <li><router-link to="/contact">Contacto</router-link></li>
      </ul>
    </nav>
  </header>
</template>

<script>
export default {
  name: 'NavBar',
  props: {
    menuOpen: Boolean
  }
};
</script>

<style scoped>
a.router-link-active {
  color: #00d4ff;
}
</style>