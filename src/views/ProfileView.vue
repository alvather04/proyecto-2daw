<template>
  <div class="summoner-profile-section">
    <div class="profile-header">
      <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <div>
          <h2>Mi Perfil de Invocador</h2>
          <p>Visualiza tu información competitiva en League of Legends</p>
        </div>
        <div style="display: flex; gap: 10px;">
          <button class="btn-login" style="padding: 8px 16px; font-size: 14px;" @click="switchAccount">Cambiar Cuenta</button>
          <button class="btn-login" style="padding: 8px 16px; font-size: 14px; background: #dc3545;" @click="logout">Cerrar Sesión</button>
        </div>
      </div>
    </div>

    <div class="search-section">
      <h3>Buscar Invocador</h3>
      <div class="search-form">
        <input v-model="searchData.gameName" type="text" placeholder="Nombre de invocador" class="search-input">
        <input v-model="searchData.tagLine" type="text" placeholder="Tag (ej. LA1)" class="search-input" style="width: 100px;">
        <select v-model="searchData.region" class="search-select">
          <option value="na1">NA</option>
          <option value="euw1">EUW</option>
          <option value="eun1">EUNE</option>
          <option value="kr">KR</option>
          <option value="br1">BR</option>
          <option value="la1">LAS</option>
          <option value="la2">LAN</option>
          <option value="jp1">JP</option>
          <option value="ru">RU</option>
          <option value="oc1">OCE</option>
          <option value="sg2">SG</option>
          <option value="tw2">TW</option>
          <option value="tr1">TR</option>
        </select>
        <button class="btn-login" @click="searchSummoner" :disabled="loading">
          {{ loading ? 'Buscando...' : 'Buscar' }}
        </button>
      </div>
      <p v-if="error" class="error-message">{{ error }}</p>
    </div>

    <div v-if="summonerData" class="profile-container">
      <div class="summoner-info-card">
        <div class="summoner-header">
          <div class="avatar-container">
            <img :src="profile.avatar" alt="Avatar" class="summoner-avatar">
            <div class="rank-badge">{{ summonerData.clasificaciones[0]?.rango || 'Sin rango' }}</div>
          </div>
          <div class="summoner-details">
            <h3 class="summoner-name">{{ summonerData.jugador.nombre }} <span class="tag">#{{ summonerData.jugador.tag }}</span></h3>
            <div class="rank-info">
              <p><strong>Email:</strong> {{ profile.email }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="rankings-section">
        <h3>Clasificaciones</h3>
        <div class="rankings-grid">
          <div v-for="(liga, index) in summonerData.clasificaciones" :key="index" class="rank-card">
            <div class="rank-header">
              <span class="queue-type">{{ liga.tipo_cola }}</span>
              <span class="hot-streak" v-if="liga.en_racha === 'Si'">🔥 Racha activa</span>
            </div>
            <div class="rank-tier">
              <img :src="getTierImage(liga.rango)" :alt="liga.rango" class="tier-icon">
              <span class="tier-name">{{ liga.rango }} {{ liga.division }}</span>
            </div>
            <div class="rank-stats">
              <div class="stat">
                <span class="stat-label">LP</span>
                <span class="stat-value">{{ liga.puntos }}</span>
              </div>
              <div class="stat">
                <span class="stat-label">Victorias</span>
                <span class="stat-value wins">{{ liga.stats.victorias }}</span>
              </div>
              <div class="stat">
                <span class="stat-label">Derrotas</span>
                <span class="stat-value losses">{{ liga.stats.derrotas }}</span>
              </div>
              <div class="stat">
                <span class="stat-label">Total</span>
                <span class="stat-value">{{ liga.stats.total }}</span>
              </div>
              <div class="stat">
                <span class="stat-label">Winrate</span>
                <span class="stat-value winrate">{{ liga.stats.winrate }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="profile-container">
      <div class="summoner-info-card">
        <h3 class="most-played-title">Campeón Más Jugado</h3>
        <div class="champion-showcase">
          <div class="champion-image">
            <img :src="mostPlayed.image" :alt="mostPlayed.name">
          </div>
          <div class="champion-stats">
            <h4 class="champion-name">{{ mostPlayed.name }}</h4>
            <div class="stat-item">
              <span class="stat-label">Puntos de Maestría:</span>
              <span class="stat-value">{{ mostPlayed.mastery.toLocaleString() }} <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23c8aa6e' width='16' height='16'%3E%3Cpath d='M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z'/%3E%3C/svg%3E" class="mastery-icon"></span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Horas Jugadas:</span>
              <span class="stat-value">{{ mostPlayed.hours }} horas</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Partidas Jugadas:</span>
              <span class="stat-value">{{ mostPlayed.games }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Tasa de Victoria ({{ mostPlayed.name }}):</span>
              <span class="stat-value win-rate">{{ mostPlayed.winRate }}%</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="other-champions">
      <h3>Otros campeones frecuentes</h3>
      <div class="champions-grid">
        <div v-for="champ in otherChampions" :key="champ.name" class="champion-card">
          <img :src="champ.image" :alt="champ.name">
          <div class="card-info">
            <h5>{{ champ.name }}</h5>
            <p>{{ champ.mastery.toLocaleString() }} Maestría</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProfileView',
  data() {
    return {
      profile: {
        name: '',
        email: '',
        server: 'LAS',
        rank: 'Platino',
        division: '2',
        lp: 87,
        winRate: 54,
        avatar: 'https://ddragon.leagueoflegends.com/cdn/14.1.1/img/profileicon/1.png'
      },
      mostPlayed: {
        name: 'Ahri',
        image: 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Ahri_0.jpg',
        mastery: 287450,
        hours: 342,
        games: 487,
        winRate: 56
      },
      otherChampions: [
        { name: 'Lissandra', image: 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Lissandra_0.jpg', mastery: 142320 },
        { name: 'Syndra', image: 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Syndra_0.jpg', mastery: 98750 },
        { name: 'Lux', image: 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Lux_0.jpg', mastery: 87640 }
      ],
      searchData: {
        gameName: '',
        tagLine: '',
        region: 'la1'
      },
      summonerData: null,
      loading: false,
      error: ''
    };
  },
  created() {
    const user = localStorage.getItem('nexus_user');
    if (user) {
      const userData = JSON.parse(user);
      this.profile.name = userData.username;
      this.profile.email = userData.email;
      this.profile.avatar = userData.avatar || this.profile.avatar;
    }
  },
  methods: {
    async searchSummoner() {
      if (!this.searchData.gameName || !this.searchData.tagLine) {
        this.error = 'Por favor ingresa el nombre y tag del invocador';
        return;
      }
      
      this.loading = true;
      this.error = '';
      
      try {
        const response = await fetch(
          `/api/account.php?gameName=${encodeURIComponent(this.searchData.gameName)}&tagLine=${encodeURIComponent(this.searchData.tagLine)}&region=${encodeURIComponent(this.searchData.region)}`
        );
        
        const data = await response.json();
        
        if (!response.ok || data.error) {
          throw new Error(data.error || data.details || 'Invocador no encontrado');
        }
        
        this.summonerData = data;
      } catch (err) {
        this.error = err.message || 'Error al buscar el invocador';
        this.summonerData = null;
      } finally {
        this.loading = false;
      }
    },
    getTierImage(tier) {
      const tierImages = {
        'IRON': 'https://raw.githubusercontent.com/nickcde/league-assets/main/ranked-emblems/Emblem_Iron.png',
        'BRONZE': 'https://raw.githubusercontent.com/nickcde/league-assets/main/ranked-emblems/Emblem_Bronze.png',
        'SILVER': 'https://raw.githubusercontent.com/nickcde/league-assets/main/ranked-emblems/Emblem_Silver.png',
        'GOLD': 'https://raw.githubusercontent.com/nickcde/league-assets/main/ranked-emblems/Emblem_Gold.png',
        'PLATINUM': 'https://raw.githubusercontent.com/nickcde/league-assets/main/ranked-emblems/Emblem_Platinum.png',
        'EMERALD': 'https://raw.githubusercontent.com/nickcde/league-assets/main/ranked-emblems/Emblem_Emerald.png',
        'DIAMOND': 'https://raw.githubusercontent.com/nickcde/league-assets/main/ranked-emblems/Emblem_Diamond.png',
        'MASTER': 'https://raw.githubusercontent.com/nickcde/league-assets/main/ranked-emblems/Emblem_Master.png',
        'GRANDMASTER': 'https://raw.githubusercontent.com/nickcde/league-assets/main/ranked-emblems/Emblem_Grandmaster.png',
        'CHALLENGER': 'https://raw.githubusercontent.com/nickcde/league-assets/main/ranked-emblems/Emblem_Challenger.png'
      };
      return tierImages[tier] || tierImages['IRON'];
    },
    logout() {
      localStorage.removeItem('nexus_user');
      this.$router.push('/login');
    },
    switchAccount() {
      localStorage.removeItem('nexus_user');
      this.$router.push('/login');
    }
  }
};
</script>