<template>
  <div class="leaderboard-section">
    <div class="profile-header">
      <h2>Leaderboard</h2>
      <p>Los mejores invocadores de Nexus Hub</p>
    </div>

    <div v-if="loading" class="skins-loading">Cargando clasificación...</div>
    <p v-if="error" class="error-message" style="text-align: center;">{{ error }}</p>

    <div class="leaderboard-container" v-if="leaderboard.length > 0 && !loading">
      <div class="leaderboard-header">
        <span class="lb-rank">#</span>
        <span class="lb-player">Invocador</span>
        <span class="lb-rank-tier">Rango</span>
        <span class="lb-stats">Estadísticas</span>
      </div>

      <div v-for="(entry, index) in leaderboard" :key="entry.username" class="leaderboard-row" :class="{ 'top-three': index < 3 }">
        <div class="lb-rank">
          <span v-if="index === 0" class="medal gold">🥇</span>
          <span v-else-if="index === 1" class="medal silver">🥈</span>
          <span v-else-if="index === 2" class="medal bronze">🥉</span>
          <span v-else class="rank-num">{{ index + 1 }}</span>
        </div>
        <div class="lb-player">
          <div class="lb-avatar">
            <img :src="getAvatar(entry.username)" :alt="entry.username" class="lb-avatar-img" @error="onAvatarError">
          </div>
          <div class="lb-player-info">
            <span class="lb-name">{{ entry.username }}</span>
            <span class="lb-email">{{ entry.email }}</span>
          </div>
        </div>
        <div class="lb-rank-tier">
          <img :src="getTierImage(entry.rank_tier)" :alt="entry.rank_tier" class="lb-tier-icon">
          <span class="lb-tier-name">{{ entry.rank_tier }}</span>
        </div>
        <div class="lb-stats">
          <div class="lb-stat">
            <span class="lb-stat-label">Victorias</span>
            <span class="lb-stat-value wins">{{ entry.wins }}</span>
          </div>
          <div class="lb-stat">
            <span class="lb-stat-label">Derrotas</span>
            <span class="lb-stat-value losses">{{ entry.losses }}</span>
          </div>
          <div class="lb-stat">
            <span class="lb-stat-label">LP</span>
            <span class="lb-stat-value lp">{{ entry.total_lp }}</span>
          </div>
        </div>
      </div>
    </div>

    <div v-if="leaderboard.length === 0 && !loading && !error" class="rotation-empty">
      <p>No hay datos en el leaderboard. ¡Juega partidas clasificatorias y actualiza tu perfil para aparecer aquí!</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LeaderboardView',
  data() {
    return {
      leaderboard: [],
      loading: true,
      error: ''
    };
  },
  created() {
    this.fetchLeaderboard();
  },
  methods: {
    async fetchLeaderboard() {
      try {
        const res = await fetch('api/leaderboard.php');
        const data = await res.json();
        if (data.success) {
          this.leaderboard = data.leaderboard;
        } else {
          this.error = data.error;
        }
      } catch (e) {
        this.error = 'Error al cargar leaderboard';
      }
      this.loading = false;
    },
    getAvatar(username) {
      return `https://ddragon.leagueoflegends.com/cdn/15.5.1/img/profileicon/${(username.length % 30) + 1}.png`;
    },
    getTierImage(tier) {
      const map = {
        'IRON': 'Emblem_Iron.png', 'BRONZE': 'Emblem_Bronze.png',
        'SILVER': 'Emblem_Silver.png', 'GOLD': 'Emblem_Gold.png',
        'PLATINUM': 'Emblem_Platinum.png', 'EMERALD': 'Emblem_Emerald.png',
        'DIAMOND': 'Emblem_Diamond.png', 'MASTER': 'Emblem_Master.png',
        'GRANDMASTER': 'Emblem_Grandmaster.png', 'CHALLENGER': 'Emblem_Challenger.png'
      };
      return `https://raw.githubusercontent.com/nickcde/league-assets/main/ranked-emblems/${map[tier] || 'Emblem_Iron.png'}`;
    },
    onAvatarError(e) {
      e.target.src = 'https://ddragon.leagueoflegends.com/cdn/15.5.1/img/profileicon/1.png';
    }
  }
};
</script>
