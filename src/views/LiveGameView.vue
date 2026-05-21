<template>
  <div class="livegame-section">
    <div class="profile-header">
      <h2>Partida en Vivo</h2>
      <p>Busca la partida actual de cualquier invocador</p>
    </div>

    <div class="search-section">
      <h3>Buscar Partida</h3>
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
          <option value="oc1">OCE</option>
        </select>
        <button class="btn-login" @click="searchGame" :disabled="loading" style="width: auto; padding: 10px 20px;">
          {{ loading ? 'Buscando...' : 'Buscar Partida' }}
        </button>
      </div>
      <p v-if="error" class="error-message">{{ error }}</p>
    </div>

    <div v-if="gameData && gameData.inGame" class="livegame-container">
      <div class="livegame-header-bar">
        <span class="livegame-mode">{{ gameData.gameMode }} - {{ gameData.gameType }}</span>
        <span class="livegame-duration">Duración: {{ formatDuration(gameData.gameLength) }}</span>
        <span class="livegame-map">{{ gameData.mapId === 11 ? 'Grieta del Invocador' : 'Mapa especial' }}</span>
      </div>

      <div class="livegame-teams">
        <div class="livegame-team team-blue">
          <h3 class="team-title">Equipo Azul</h3>
          <div v-for="(player, idx) in blueTeam" :key="idx" class="livegame-player">
            <div class="player-champ">
              <img :src="`https://ddragon.leagueoflegends.com/cdn/15.5.1/img/champion/${getChampionName(player.championId)}.png`" :alt="player.championId" class="player-champ-img" @error="onChampError">
            </div>
            <div class="player-info">
              <span class="player-summoner">{{ player.summonerName }}</span>
              <div class="player-spells">
                <img :src="`https://ddragon.leagueoflegends.com/cdn/15.5.1/img/spell/${getSpellName(player.spell1Id)}.png`" class="spell-icon" @error="onSpellError">
                <img :src="`https://ddragon.leagueoflegends.com/cdn/15.5.1/img/spell/${getSpellName(player.spell2Id)}.png`" class="spell-icon" @error="onSpellError">
              </div>
            </div>
          </div>
        </div>

        <div class="livegame-vs">VS</div>

        <div class="livegame-team team-red">
          <h3 class="team-title">Equipo Rojo</h3>
          <div v-for="(player, idx) in redTeam" :key="idx" class="livegame-player">
            <div class="player-champ">
              <img :src="`https://ddragon.leagueoflegends.com/cdn/15.5.1/img/champion/${getChampionName(player.championId)}.png`" :alt="player.championId" class="player-champ-img" @error="onChampError">
            </div>
            <div class="player-info">
              <span class="player-summoner">{{ player.summonerName }}</span>
              <div class="player-spells">
                <img :src="`https://ddragon.leagueoflegends.com/cdn/15.5.1/img/spell/${getSpellName(player.spell1Id)}.png`" class="spell-icon" @error="onSpellError">
                <img :src="`https://ddragon.leagueoflegends.com/cdn/15.5.1/img/spell/${getSpellName(player.spell2Id)}.png`" class="spell-icon" @error="onSpellError">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="gameData && !gameData.inGame && !error" class="livegame-empty">
      <p>{{ gameData.message || 'El invocador no está en partida.' }}</p>
    </div>
  </div>
</template>

<script>
const SPELL_MAP = {
  1: 'SummonerBoost', 2: 'SummonerClairvoyance', 3: 'SummonerExhaust',
  4: 'SummonerFlash', 5: 'SummonerDot', 6: 'SummonerHaste',
  7: 'SummonerHeal', 8: 'SummonerMana', 9: 'SummonerMana',
  10: 'SummonerTeleport', 11: 'SummonerSmite', 12: 'SummonerTeleport',
  13: 'SummonerBarrier', 14: 'SummonerDot', 21: 'SummonerTest'
};

const CHAMPION_IDS = {};

export default {
  name: 'LiveGameView',
  data() {
    return {
      searchData: { gameName: '', tagLine: '', region: 'la1' },
      gameData: null,
      loading: false,
      error: '',
      championNames: {}
    };
  },
  computed: {
    blueTeam() {
      if (!this.gameData?.participants) return [];
      return this.gameData.participants.filter(p => p.teamId === 100);
    },
    redTeam() {
      if (!this.gameData?.participants) return [];
      return this.gameData.participants.filter(p => p.teamId === 200);
    }
  },
  created() {
    this.loadChampionNames();
  },
  methods: {
    async loadChampionNames() {
      try {
        const res = await fetch('https://ddragon.leagueoflegends.com/cdn/15.5.1/data/en_US/champion.json');
        const data = await res.json();
        Object.values(data.data).forEach(c => {
          this.championNames[parseInt(c.key)] = c.id;
        });
      } catch (e) {
        console.error('Error loading champion names:', e);
      }
    },
    getChampionName(id) {
      return this.championNames[id] || 'Ahri';
    },
    getSpellName(id) {
      return SPELL_MAP[id] || 'SummonerFlash';
    },
    formatDuration(seconds) {
      const m = Math.floor(seconds / 60);
      const s = seconds % 60;
      return `${m}:${s.toString().padStart(2, '0')}`;
    },
    async searchGame() {
      if (!this.searchData.gameName || !this.searchData.tagLine) {
        this.error = 'Ingresa nombre y tag del invocador';
        return;
      }
      this.loading = true;
      this.error = '';
      this.gameData = null;
      try {
        const res = await fetch(`api/live_game.php?gameName=${encodeURIComponent(this.searchData.gameName)}&tagLine=${encodeURIComponent(this.searchData.tagLine)}&region=${this.searchData.region}`);
        const data = await res.json();
        if (data.error) {
          this.error = data.error;
        } else {
          this.gameData = data;
        }
      } catch (e) {
        this.error = 'Error al conectar con el servidor';
      }
      this.loading = false;
    },
    onChampError(e) {
      e.target.src = 'https://ddragon.leagueoflegends.com/cdn/15.5.1/img/champion/Ahri.png';
    },
    onSpellError(e) {
      e.target.src = 'https://ddragon.leagueoflegends.com/cdn/15.5.1/img/spell/SummonerFlash.png';
    }
  }
};
</script>
