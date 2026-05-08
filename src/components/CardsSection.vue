<template>
  <section class="cards-section">
    <h2 class="cards-title">{{ title }}</h2>
    <div class="cards-container">
      <div v-for="(card, index) in cards" :key="index" class="card">
        <div class="card-img" :style="{ backgroundImage: `url(${card.image})` }"></div>
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
  props: {
    title: String,
    cards: Array
  }
};
</script>

<style scoped>
/* Los estilos ya están en estilos.css */
</style>