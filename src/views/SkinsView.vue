<template>
  <div class="skins-section">
    <div class="profile-header">
      <h2>Galería de Aspectos</h2>
      <p>Explora todos los aspectos de cada campeón en League of Legends</p>
    </div>

    <div class="skins-search-bar">
      <input v-model="searchQuery" type="text" placeholder="Buscar campeón..." class="skins-search-input" @input="filterChampions">
      <select v-model="selectedChampion" class="skins-select" @change="selectChampion">
        <option value="">Selecciona un campeón</option>
        <option v-for="champ in filteredChampions" :key="champ.id" :value="champ.id">
          {{ champ.name }}
        </option>
      </select>
    </div>

    <div v-if="loading" class="skins-loading">Cargando campeones...</div>

    <div v-if="selectedChampionData && !loading" class="skins-grid-container">
      <h3 class="skins-champ-title">{{ selectedChampionData.name }} - {{ skins.length }} Aspectos</h3>
      <div class="skins-grid">
        <div v-for="skin in skins" :key="skin.num" class="skin-card">
          <div class="skin-img-wrapper">
            <img :src="getSkinImage(selectedChampionData.id, skin.num)" :alt="skin.name" class="skin-img" loading="lazy" @error="onImgError">
          </div>
          <div class="skin-info">
            <h4>{{ skin.name }}</h4>
            <span v-if="skin.num === 0" class="skin-badge default">Clásico</span>
            <span v-else class="skin-badge">Aspecto</span>
          </div>
        </div>
      </div>
    </div>

    <div v-if="!selectedChampion && !loading" class="skins-grid-container">
      <h3 class="skins-champ-title">Todos los campeones</h3>
      <p style="color: var(--grey-text); text-align: center; margin-bottom: 2rem;">Selecciona un campeón para ver sus aspectos</p>
      <div class="champions-grid">
        <div v-for="champ in filteredChampions" :key="champ.id" class="champion-card" @click="selectChampionDirect(champ.id)">
          <img :src="`https://ddragon.leagueoflegends.com/cdn/15.5.1/img/champion/${champ.id}.png`" :alt="champ.name" loading="lazy">
          <div class="card-info">
            <h5>{{ champ.name }}</h5>
            <p>{{ champ.title }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SkinsView',
  data() {
    return {
      champions: [],
      filteredChampions: [],
      selectedChampion: '',
      selectedChampionData: null,
      skins: [],
      searchQuery: '',
      loading: true
    };
  },
  created() {
    this.fetchChampions();
  },
  methods: {
    async fetchChampions() {
      try {
        const res = await fetch('https://ddragon.leagueoflegends.com/cdn/15.5.1/data/en_US/champion.json');
        const data = await res.json();
        this.champions = Object.values(data.data).map(c => ({
          id: c.id,
          name: c.name,
          title: c.title,
          blurb: c.blurb
        }));
        this.filteredChampions = this.champions;
      } catch (e) {
        console.error('Error loading champions:', e);
      }
      this.loading = false;
    },
    filterChampions() {
      const q = this.searchQuery.toLowerCase();
      this.filteredChampions = this.champions.filter(c =>
        c.name.toLowerCase().includes(q) || c.title.toLowerCase().includes(q)
      );
    },
    async selectChampion() {
      if (!this.selectedChampion) {
        this.selectedChampionData = null;
        this.skins = [];
        return;
      }
      this.loading = true;
      try {
        const res = await fetch(`https://ddragon.leagueoflegends.com/cdn/15.5.1/data/en_US/champion/${this.selectedChampion}.json`);
        const data = await res.json();
        const champData = data.data[this.selectedChampion];
        this.selectedChampionData = champData;
        this.skins = champData.skins;
      } catch (e) {
        console.error('Error loading skins:', e);
      }
      this.loading = false;
    },
    selectChampionDirect(id) {
      this.selectedChampion = id;
      this.selectChampion();
    },
    getSkinImage(champId, skinNum) {
      return `https://ddragon.leagueoflegends.com/cdn/img/champion/splash/${champId}_${skinNum}.jpg`;
    },
    onImgError(e) {
      e.target.src = 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Ahri_0.jpg';
    }
  }
};
</script>
