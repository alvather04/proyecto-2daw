<template>
  <!-- ============================================================ -->
  <!-- BARRA DE NAVEGACIÓN                                          -->
  <!-- ============================================================ -->

  <header>
    <!-- Logo del sitio -->
    <div class="logo">Nexus Hub</div>

    <!-- ============================================================ -->
    <!-- BOTÓN DEL MENÚ (para celulares)                              -->
    <!-- ============================================================ -->
    <button class="menu-toggle" :class="{ active: menuOpen }" @click="$emit('toggle-menu')">
      <span></span>
      <span></span>
      <span></span>
    </button>

    <!-- ============================================================ -->
    <!-- ENLACES DEL MENÚ                                             -->
    <!-- ============================================================ -->
    <nav :class="{ active: menuOpen }">
      <ul>
        <li><router-link to="/">Inicio</router-link></li>
        <li><router-link to="/forum">Foro</router-link></li>
        <li><router-link to="/skins">Aspectos</router-link></li>
        <li><router-link to="/rotation">Rotación</router-link></li>
        <li><router-link to="/items">Objetos</router-link></li>
        <li><router-link to="/live-game">En Vivo</router-link></li>
        <li><router-link to="/patches">Parches</router-link></li>
        <li><router-link to="/streams">Streams</router-link></li>
        <li><router-link to="/leaderboard">Clasificación</router-link></li>
        <li><router-link to="/profile">Mi Perfil</router-link></li>
        <li><router-link to="/settings">Ajustes</router-link></li>
        <li><router-link to="/contact">Contacto</router-link></li>
      </ul>
    </nav>
  </header>
</template>

<script>
export default {
  name: 'NavBar',

  // ============================================================
  // DATOS QUE RECIBE DEL PADRE
  // ============================================================
  props: {
    menuOpen: Boolean   // true = menú abierto, false = cerrado
  }
};
</script>

<!-- ============================================================ -->
<!-- ESTILOS PROPIOS (solo afectan a este componente)             -->
<!-- ============================================================ -->
<style scoped>
/*
  Cuando un enlace está en la página actual,
  se pone de color azul brillante
*/
a.router-link-active {
  color: #00d4ff;
}
</style>
