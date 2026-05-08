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
        <li><a href="index.html">Inicio</a></li>
        <li><a href="forum.html">Foro</a></li>
        <li><a href="profile.html">Mi Perfil</a></li>
        <li><a href="login.html">Contacto</a></li>
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
/* Los estilos ya están en estilos.css */
</style>