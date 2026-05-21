<template>
  <!-- ============================================================ -->
  <!-- PIE DE PÁGINA                                                -->
  <!-- ============================================================ -->
  <footer>
    <p>{{ text }}</p>
  </footer>
</template>

<script>
export default {
  name: 'Footer',

  // ============================================================
  // DATOS QUE RECIBE
  // ============================================================
  props: {
    text: {
      type: String,
      default: '&copy; 2025 Nexus Hub. Sitio fan no oficial. League of Legends es propiedad de Riot Games.'
    }
  }
};
</script>

<style scoped>
/* Los estilos están en estilos.css */
</style>
