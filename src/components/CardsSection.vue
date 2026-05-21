<template>
  <!-- ============================================================ -->
  <!-- TARJETAS DESTACADAS                                          -->
  <!-- Muestra noticias, builds y eventos                           -->
  <!-- ============================================================ -->

  <section class="cards-section">

    <!-- Título de la sección -->
    <h2 class="cards-title">{{ title }}</h2>

    <!-- ============================================================ -->
    <!-- CONTENEDOR DE TARJETAS                                       -->
    <!-- ============================================================ -->
    <div class="cards-container">

      <!-- ============================================================ -->
      <!-- RECORRE CADA TARJETA                                         -->
      <!-- ============================================================ -->
      <div v-for="(card, index) in cards" :key="index" class="card">

        <!-- Imagen de fondo -->
        <div class="card-img" :style="{ backgroundImage: `url(${card.image})` }"></div>

        <!-- Texto de la tarjeta -->
        <div class="card-content">
          <h3>{{ card.title }}</h3>
          <p>{{ card.description }}</p>
          <a href="#" class="card-btn">{{ card.button }}</a>
        </div>

      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'CardsSection',

  // ============================================================
  // DATOS QUE RECIBE
  // ============================================================
  props: {
    title: String,    // Título de la sección
    cards: Array      // Lista de tarjetas (imagen, título, descripción, botón)
  }
};
</script>

<style scoped>
/* Los estilos están en estilos.css */
</style>
