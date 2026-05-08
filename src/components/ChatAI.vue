<template>
  <section class="ai-section">
    <div class="ai-header">
      <h2>{{ title }}</h2>
      <span>{{ subtitle }}</span>
      <p style="margin-top: 15px; color: #aaa;">{{ description }}</p>
    </div>

    <div class="ai-container">
      <div class="chat-wrapper">
        <div class="chat-messages" ref="chatMessages">
          <div v-for="(msg, index) in messages" :key="index" class="message" :class="msg.type">
            <div class="message-avatar">
              <img :src="msg.type === 'bot' ? '/Jinx_60.png' : userAvatar" :alt="msg.type">
            </div>
            <div class="message-content">
              <p v-html="formatMessage(msg.text)"></p>
            </div>
          </div>
        </div>
        
        <div class="chat-input-container">
          <input 
            type="text" 
            v-model="chatInput"
            @keyup.enter="sendMessage"
            placeholder="Escribe tu pregunta sobre League of Legends..."
            autocomplete="off"
            :disabled="isLoading"
          >
          <button class="btn-send" @click="sendMessage" :disabled="isLoading">
            <span v-if="isLoading" class="loading-spinner"></span>
            <svg v-else viewBox="0 0 24 24" width="24" height="24">
              <path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
            </svg>
          </button>
        </div>
      </div>

      <div class="chat-history-panel">
        <div class="history-header">
          <h3>Historial</h3>
          <button class="btn-clear" @click="clearHistory">Limpiar</button>
        </div>
        <div class="history-list">
          <p v-if="history.length === 0" class="no-history">No hay conversaciones guardadas</p>
          <div 
            v-for="(item, index) in history" 
            :key="index" 
            class="history-item"
            @click="$emit('load-history', item)"
          >
            <p class="history-preview">{{ item.message.substring(0, 40) }}{{ item.message.length > 40 ? '...' : '' }}</p>
            <span class="history-time">{{ formatTime(item.created_at) }}</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'ChatAI',
  props: {
    title: { type: String, default: 'Nexus AI' },
    subtitle: { type: String, default: 'Powered by Gemini' },
    description: { type: String, default: 'Tu asistente personalizado de League of Legends.' },
    messages: Array,
    history: Array,
    isLoading: Boolean
  },
  data() {
    return {
      chatInput: '',
      userAvatar: 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Ahri_0.jpg'
    };
  },
  methods: {
    formatMessage(text) {
      if (text.includes('<ul>')) return text;
      return text;
    },
    formatTime(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    },
    sendMessage() {
      if (this.chatInput.trim() && !this.isLoading) {
        this.$emit('send-message', this.chatInput);
        this.chatInput = '';
      }
    },
    clearHistory() {
      this.$emit('clear-history');
    }
  }
};
</script>

<style scoped>
.message-content ul {
  margin: 0.5rem 0 0 1.25rem;
  padding: 0;
}

.message-content li {
  margin-bottom: 0.25rem;
  font-size: 0.9rem;
  color: #ddd;
}
</style>