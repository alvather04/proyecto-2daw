<template>
  <section class="hero-carousel">
    <div 
      v-for="(slide, index) in slides" 
      :key="index"
      class="slide"
      :class="{ active: currentSlide === index }"
      :style="{ backgroundImage: `url(${slide.image})` }"
    >
      <div class="overlay">
        <div class="slide-content">
          <h2>{{ slide.title }}</h2>
          <p>{{ slide.description }}</p>
          <button class="btn-hero">{{ slide.button }}</button>
        </div>

        <div v-if="index === 1" class="abilities-container">
          <div class="ability-icons">
            <img 
              v-for="(ability, key) in abilities" 
              :key="key"
              :src="ability.icon"
              :data-ability="key"
              @click="$emit('select-ability', key)"
              :class="{ active: selectedAbility === key }"
            >
          </div>
          <div class="ability-info">
            <h3>{{ selectedAbility ? abilities[selectedAbility].name : 'Selecciona una habilidad' }}</h3>
            <p>{{ selectedAbility ? abilities[selectedAbility].description : '' }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'HeroCarousel',
  props: {
    slides: Array,
    abilities: Object,
    selectedAbility: String,
    currentSlide: Number
  }
};
</script>

<style scoped>
/* Los estilos ya están en estilos.css */
</style>