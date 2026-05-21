<template>
  <div class="streams-section">
    <div class="profile-header">
      <h2>Streams en Vivo</h2>
      <p>Canales de League of Legends en Twitch</p>
    </div>

    <div class="streams-info-bar">
      <p>Selecciona un streamer para ver su canal en vivo.</p>
    </div>

    <div class="streams-container">
      <div class="streams-list">
        <h3 class="streams-subtitle">Streamers Destacados</h3>
        <div class="streamer-grid">
          <div v-for="streamer in streamers" :key="streamer.name" class="streamer-card" :class="{ active: activeStream === streamer.name }" @click="selectStream(streamer.name)">
            <img :src="streamer.avatar" :alt="streamer.name" class="streamer-avatar" loading="lazy">
            <div class="streamer-info">
              <h4 class="streamer-name">{{ streamer.displayName }}</h4>
              <span class="streamer-status" :class="{ online: streamer.online }">{{ streamer.online ? '● En vivo' : '○ Offline' }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="streams-player" v-if="activeStream">
        <div class="stream-embed">
          <iframe
            :src="`https://player.twitch.tv/?channel=${activeStream}&parent=${parentDomain}&muted=false`"
            height="500"
            width="100%"
            frameborder="0"
            scrolling="no"
            allowfullscreen="true"
          ></iframe>
        </div>
        <div class="stream-chat-embed" v-if="showChat">
          <iframe
            :src="`https://www.twitch.tv/embed/${activeStream}/chat?parent=${parentDomain}`"
            height="500"
            width="100%"
            frameborder="0"
            scrolling="no"
          ></iframe>
        </div>
      </div>

      <div v-if="!activeStream" class="streams-placeholder">
        <div class="placeholder-icon">▶</div>
        <p>Selecciona un streamer de la lista para ver su canal</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StreamsView',
  data() {
    return {
      activeStream: null,
      showChat: false,
      parentDomain: window.location.hostname || 'localhost',
      streamers: [
        { name: 'kaicenat', displayName: 'Kaicenat', avatar: 'https://static-cdn.jtvnw.net/jtv_user_pictures/kaicenat-profile_image-1a2b3c4d5e6f7g8-300x300.png', online: true },
        { name: 'xqc', displayName: 'xQc', avatar: 'https://static-cdn.jtvnw.net/jtv_user_pictures/xqc-profile_image-1a2b3c4d5e6f7g8-300x300.png', online: true },
        { name: 'loltyler1', displayName: 'Tyler1', avatar: 'https://static-cdn.jtvnw.net/jtv_user_pictures/loltyler1-profile_image-1a2b3c4d5e6f7g8-300x300.png', online: true },
        { name: 'nightblue3', displayName: 'Nightblue3', avatar: 'https://static-cdn.jtvnw.net/jtv_user_pictures/nightblue3-profile_image-1a2b3c4d5e6f7g8-300x300.png', online: true },
        { name: 'sneaky', displayName: 'Sneaky', avatar: 'https://static-cdn.jtvnw.net/jtv_user_pictures/sneaky-profile_image-1a2b3c4d5e6f7g8-300x300.png', online: true },
        { name: 'doublelift', displayName: 'Doublelift', avatar: 'https://static-cdn.jtvnw.net/jtv_user_pictures/doublelift-profile_image-1a2b3c4d5e6f7g8-300x300.png', online: true },
        { name: 'bausffs', displayName: 'Thebausffs', avatar: 'https://static-cdn.jtvnw.net/jtv_user_pictures/thebausffs-profile_image-1a2b3c4d5e6f7g8-300x300.png', online: false },
        { name: 'tfblade', displayName: 'TFBlade', avatar: 'https://static-cdn.jtvnw.net/jtv_user_pictures/tfblade-profile_image-1a2b3c4d5e6f7g8-300x300.png', online: false }
      ],
      streamerStatus: {}
    };
  },
  methods: {
    selectStream(name) {
      this.activeStream = this.activeStream === name ? null : name;
    }
  }
};
</script>
