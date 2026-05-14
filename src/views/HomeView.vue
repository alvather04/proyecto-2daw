<template>
  <div class="home-content">
    <HeroCarousel 
      :slides="slides" 
      :abilities="abilities"
      :selectedAbility="selectedAbility"
      :currentSlide="currentSlide"
      @select-ability="selectAbility"
    />
    
    <CardsSection 
      title="Destacados del Parche" 
      :cards="cards"
    />
    
    <ChatAI 
      title="Nexus AI"
      subtitle="Powered by Gemini"
      description="Tu asistente personalizado de League of Legends. Pregunta lo que quieras sobre el juego."
      :messages="messages"
      :history="history"
      :isLoading="isLoading"
      @send-message="handleSendMessage"
      @load-history="loadFromHistory"
      @clear-history="clearHistory"
    />
  </div>
</template>

<script>
import HeroCarousel from '../components/HeroCarousel.vue';
import CardsSection from '../components/CardsSection.vue';
import ChatAI from '../components/ChatAI.vue';

export default {
  name: 'HomeView',
  components: {
    HeroCarousel,
    CardsSection,
    ChatAI
  },
  data() {
    return {
      currentSlide: 0,
      selectedAbility: null,
      slides: [
        {
          image: '/leaguelegendsworlds_4385023b.jpg',
          title: 'Worlds 2025',
          description: 'La competición definitiva regresa a Europa. Prepárate para ver a los mejores equipos del mundo luchar por la Copa del Invocador.',
          button: 'Más Información'
        },
        {
          image: '/enhanced_Zaahen_0.png',
          title: 'Zaahen - Habilidades',
          description: 'Explora las habilidades de Zaahen y mira cómo funcionan en acción.',
          button: 'Ver Habilidades'
        },
        {
          image: '/enhanced_Jinx_60.png',
          title: 'Evento: Soul Fighter',
          description: 'Nuevo modo de juego Arena, skins exclusivas y misiones. ¡Demuestra tu valía en el torneo de las almas!',
          button: 'Ver Recompensas'
        }
      ],
      abilities: {
        pasiva: { name: 'Pasiva - Escudo de Cristal', icon: '/pasiva_zaahen.png', description: 'Zaahen obtiene un escudo que se activa al recibir daño.' },
        q: { name: 'Q - Impacto Cristalino', icon: '/q_zaahen.jfif', description: 'Lanza un cristal que causa daño y ralentiza.' },
        w: { name: 'W - Refuerzos Cristalinos', icon: '/w_zaahen.jfif', description: 'Potencia a un aliado con escudo y velocidad de ataque.' },
        e: { name: 'E - Campo de Cristales', icon: '/e_zaahen.jfif', description: 'Crea una zona que ralentiza a los enemigos.' },
        r: { name: 'R - Tormenta Cristalina', icon: '/r_zaahen.jfif', description: 'Invoca una tormenta de cristales en área.' }
      },
      cards: [
        { image: 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Aatrox_0.jpg', title: 'Aatrox Buffeado', description: 'El destructor oscuro vuelve al meta gracias a mejoras en su Q y sustain en línea.', button: 'Leer Más' },
        { image: 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Ezreal_0.jpg', title: 'Nuevo Build para Ezreal', description: 'Letalidad vuelve a ser viable. Descubre la build más rota del parche 15.9.', button: 'Ver Build' },
        { image: 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Morgana_0.jpg', title: 'Evento: Eclipse Lunar', description: 'Recompensas exclusivas, misiones y skins temáticas para todos los roles.', button: 'Detalles' }
      ],
      messages: [
        { type: 'bot', text: '¡Hola! Soy Nexus AI, tu asistente de League of Legends. ¿Qué quieres saber? Puedo ayudarte con:<ul><li>Builds y objetos para cualquier campeón</li><li>Runas y configuraciones</li><li>Counter picks y matchups</li><li>Estrategias y macros</li><li>Información de parches</li></ul>' }
      ],
      isLoading: false,
      history: [],
      currentUserId: null
    };
  },
  created() {
    const user = localStorage.getItem('nexus_user');
    if (user) {
      const userData = JSON.parse(user);
      this.currentUserId = userData.id;
    }
  },
  mounted() {
    setInterval(() => {
      this.currentSlide = (this.currentSlide + 1) % this.slides.length;
    }, 5000);
    this.loadHistory();
  },
  methods: {
    selectAbility(key) {
      this.selectedAbility = key;
    },
    async handleSendMessage(message) {
      this.messages.push({ type: 'user', text: message });
      this.isLoading = true;
      try {
        const response = await fetch('api/chat.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            message: message,
            user_id: this.currentUserId
          })
        });
        const data = await response.json();
        if (data.success) {
          this.messages.push({ type: 'bot', text: data.chat.response });
          this.history.unshift(data.chat);
          if (this.history.length > 10) this.history.pop();
        } else {
          this.messages.push({ type: 'bot', text: data.error || 'Lo siento, hubo un error.' });
        }
      } catch (error) {
        console.error('Error:', error);
        this.messages.push({ type: 'bot', text: 'Error de conexión. Verifica que el servidor PHP esté funcionando.' });
      }
      this.isLoading = false;
      this.$nextTick(() => {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
      });
    },
    loadFromHistory(item) {
      this.messages = [
        { type: 'bot', text: '¡Hola! Soy Nexus AI, tu asistente de League of Legends. ¿Qué quieres saber?' },
        { type: 'user', text: item.message },
        { type: 'bot', text: item.response }
      ];
    },
    clearHistory() {
      this.history = [];
    },
    async loadHistory() {
      if (!this.currentUserId) return;
      try {
        const response = await fetch('api/get_chats.php');
        const data = await response.json();
        if (data.chats && data.chats.length > 0) {
          this.history = data.chats.slice(0, 10);
        }
      } catch (e) {
        console.log('No se pudo cargar el historial');
      }
    }
  }
};
</script>