<template>
  <div class="summoner-profile-section">
    <div class="profile-header">
      <h2>Ajustes</h2>
      <p>Personaliza tu experiencia en Nexus Hub</p>
    </div>

    <div class="settings-container">
      <div class="settings-card">
        <h3 class="settings-card-title">Apariencia</h3>
        <div class="settings-item">
          <div class="settings-item-info">
            <span class="settings-label">Tema Oscuro</span>
            <span class="settings-desc">Cambia entre tema oscuro y claro</span>
          </div>
          <label class="settings-toggle">
            <input type="checkbox" :checked="darkMode" @change="toggleTheme">
            <span class="toggle-slider"></span>
          </label>
        </div>
        <div class="settings-item">
          <div class="settings-item-info">
            <span class="settings-label">Color del Tema</span>
            <span class="settings-desc">Color principal de la interfaz</span>
          </div>
          <input type="color" v-model="accentColor" @change="changeAccentColor" class="settings-color-input">
        </div>
      </div>

      <div class="settings-card">
        <h3 class="settings-card-title">Notificaciones</h3>
        <div class="settings-item">
          <div class="settings-item-info">
            <span class="settings-label">Notificaciones del Foro</span>
            <span class="settings-desc">Recibir alertas cuando alguien responda tus posts</span>
          </div>
          <label class="settings-toggle">
            <input type="checkbox" v-model="notifForum" @change="saveSettings">
            <span class="toggle-slider"></span>
          </label>
        </div>
        <div class="settings-item">
          <div class="settings-item-info">
            <span class="settings-label">Sonidos</span>
            <span class="settings-desc">Reproducir sonidos al recibir notificaciones</span>
          </div>
          <label class="settings-toggle">
            <input type="checkbox" v-model="notifSound" @change="saveSettings">
            <span class="toggle-slider"></span>
          </label>
        </div>
      </div>

      <div class="settings-card">
        <h3 class="settings-card-title">Perfil de Invocador</h3>
        <div class="settings-item">
          <div class="settings-item-info">
            <span class="settings-label">Región por defecto</span>
            <span class="settings-desc">Región para búsquedas en Riot API</span>
          </div>
          <select v-model="defaultRegion" @change="saveSettings" class="settings-select">
            <option value="na1">NA</option>
            <option value="euw1">EUW</option>
            <option value="eun1">EUNE</option>
            <option value="kr">KR</option>
            <option value="br1">BR</option>
            <option value="la1">LAS</option>
            <option value="la2">LAN</option>
            <option value="jp1">JP</option>
          </select>
        </div>
        <div class="settings-item">
          <div class="settings-item-info">
            <span class="settings-label">Nombre de Invocador</span>
            <span class="settings-desc">Tu nombre dentro del juego</span>
          </div>
          <input type="text" v-model="summonerName" class="settings-text-input" @change="saveSettings" placeholder="Ej: Faker">
        </div>
      </div>

      <div class="settings-card">
        <h3 class="settings-card-title">Cuenta</h3>
        <div class="settings-item">
          <div class="settings-item-info">
            <span class="settings-label">Cerrar Sesión</span>
            <span class="settings-desc">Salir de tu cuenta actual</span>
          </div>
          <button class="btn-login" @click="logout" style="width: auto; padding: 8px 20px; background: #dc3545;">Cerrar Sesión</button>
        </div>
      </div>

      <p v-if="saved" class="settings-saved">✓ Ajustes guardados</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SettingsView',
  data() {
    return {
      darkMode: true,
      accentColor: '#c8aa6e',
      notifForum: true,
      notifSound: false,
      defaultRegion: 'la1',
      summonerName: '',
      saved: false
    };
  },
  created() {
    this.loadSettings();
  },
  methods: {
    loadSettings() {
      const settings = JSON.parse(localStorage.getItem('nexus_settings') || '{}');
      this.darkMode = settings.darkMode !== undefined ? settings.darkMode : true;
      this.accentColor = settings.accentColor || '#c8aa6e';
      this.notifForum = settings.notifForum !== undefined ? settings.notifForum : true;
      this.notifSound = settings.notifSound || false;
      this.defaultRegion = settings.defaultRegion || 'la1';
      this.summonerName = settings.summonerName || '';
      this.applyTheme();
    },
    saveSettings() {
      const settings = {
        darkMode: this.darkMode,
        accentColor: this.accentColor,
        notifForum: this.notifForum,
        notifSound: this.notifSound,
        defaultRegion: this.defaultRegion,
        summonerName: this.summonerName
      };
      localStorage.setItem('nexus_settings', JSON.stringify(settings));
      this.applyTheme();
      this.saved = true;
      setTimeout(() => this.saved = false, 2000);
    },
    toggleTheme() {
      this.darkMode = !this.darkMode;
      this.saveSettings();
    },
    changeAccentColor() {
      this.saveSettings();
    },
    applyTheme() {
      document.documentElement.setAttribute('data-theme', this.darkMode ? 'dark' : 'light');
      document.documentElement.style.setProperty('--gold', this.accentColor);
    },
    logout() {
      localStorage.removeItem('nexus_user');
      this.$router.push('/login');
    }
  }
};
</script>
