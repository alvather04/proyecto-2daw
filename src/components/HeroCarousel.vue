<template>
  <!-- ============================================================ -->
  <!-- CARRUSEL DE IMÁGENES                                         -->
  <!-- Muestra varias imágenes que cambian solas                    -->
  <!-- ============================================================ -->

  <section class="hero-carousel">
    <!-- ============================================================ -->
    <!-- RECORRE CADA SLIDE                                          -->
    <!-- ============================================================ -->
    <div 
      v-for="(slide, index) in slides" 
      :key="index"
      class="slide"
      :class="{ active: currentSlide === index }"
      :style="{ backgroundImage: `url(${slide.image})` }"
    >

      <div class="overlay">

        <!-- ============================================================ -->
        <!-- TEXTO DEL SLIDE (título, descripción, botón)                 -->
        <!-- ============================================================ -->
        <div class="slide-content">
          <h2>{{ slide.title }}</h2>
          <p>{{ slide.description }}</p>
          <button class="btn-hero">{{ slide.button }}</button>
        </div>

        <!-- ============================================================ -->
        <!-- SLIDE DE ZAAHEN (habilidades del campeón)                    -->
        <!-- ============================================================ -->
        <div v-if="index === 1" class="abilities-container">

          <!-- Iconos de las habilidades -->
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

          <!-- Información de la habilidad elegida -->
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

  // ============================================================
  // DATOS QUE RECIBE
  // ============================================================
  props: {
    slides: Array,          // Las imágenes y textos del carrusel
    abilities: Object,      // Las habilidades del campeón
    selectedAbility: String, // Cuál habilidad está elegida
    currentSlide: Number    // Qué número de slide se muestra
  }
};
</script>

<style scoped>
/* Los estilos están en estilos.css */
</style>
