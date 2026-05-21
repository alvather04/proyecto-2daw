<template>
  <div class="rotation-section">
    <div class="profile-header">
      <h2>Rotación de Campeones</h2>
      <p>Campeones gratuitos de esta semana</p>
    </div>

    <div class="search-section">
      <div class="search-form">
        <select v-model="region" class="search-select" @change="fetchRotation">
          <option value="na1">NA</option>
          <option value="euw1">EUW</option>
          <option value="eun1">EUNE</option>
          <option value="kr">KR</option>
          <option value="br1">BR</option>
          <option value="la1">LAS</option>
          <option value="la2">LAN</option>
          <option value="jp1">JP</option>
          <option value="oc1">OCE</option>
        </select>
        <button class="btn-login" @click="fetchRotation" :disabled="loading" style="width: auto; padding: 10px 20px;">
          {{ loading ? 'Cargando...' : 'Actualizar' }}
        </button>
      </div>
      <p v-if="error" class="error-message">{{ error }}</p>
    </div>

    <div v-if="freeChampions.length > 0 && !loading" class="rotation-grid">
      <div class="rotation-card" v-for="champ in freeChampions" :key="champ.id">
        <div class="rotation-img-wrapper">
          <img :src="`https://ddragon.leagueoflegends.com/cdn/15.5.1/img/champion/${champ.id}.png`" :alt="champ.name" loading="lazy">
        </div>
        <div class="rotation-info">
          <h4>{{ champ.name }}</h4>
          <p>{{ champ.title }}</p>
        </div>
      </div>
    </div>

    <div v-if="freeChampions.length === 0 && !loading && !error" class="rotation-empty">
      <p>Selecciona una región y presiona "Actualizar" para ver la rotación semanal.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RotationView',
  data() {
    return {
      region: 'la1',
      freeChampions: [],
      allChampions: {},
      loading: false,
      error: ''
    };
  },
  created() {
    this.loadChampionData();
  },
  methods: {
    async loadChampionData() {
      try {
        const res = await fetch('https://ddragon.leagueoflegends.com/cdn/15.5.1/data/en_US/champion.json');
        const data = await res.json();
        this.allChampions = data.data;
      } catch (e) {
        console.error('Error loading champions:', e);
      }
    },
    async fetchRotation() {
      this.loading = true;
      this.error = '';
      try {
        const res = await fetch(`api/rotation.php?region=${this.region}`);
        const data = await res.json();
        if (data.error) {
          this.error = data.error;
          this.freeChampions = [];
        } else {
          const freeIds = [...(data.freeChampionIds || []), ...(data.freeChampionIdsForNewPlayers || [])];
          const uniqueIds = [...new Set(freeIds)];
          this.freeChampions = uniqueIds.map(id => {
            const champ = Object.values(this.allChampions).find(c => parseInt(c.key) === id);
            return champ ? { id: champ.id, name: champ.name, title: champ.title, blurb: champ.blurb } : null;
          }).filter(Boolean);
        }
      } catch (e) {
        this.error = 'Error al conectar con el servidor';
        this.freeChampions = [];
      }
      this.loading = false;
    }
  }
};
</script>
