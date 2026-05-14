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
              <div v-if="extractItems(msg.text).length > 0 && !isMatchupOnly(msg.text)" class="lol-section">
                <h4 class="lol-section-title">Objetos</h4>
                <div class="lol-grid items-grid">
                  <div v-for="(item, idx) in extractItems(msg.text)" :key="'item-'+idx" class="lol-item">
                    <img :src="getItemImage(item)" :alt="item" class="lol-img" @error="handleImgError">
                    <span class="lol-name">{{ item }}</span>
                  </div>
                </div>
              </div>
              <div v-if="extractRunes(msg.text).length > 0 && !isMatchupOnly(msg.text)" class="lol-section">
                <h4 class="lol-section-title">Runas</h4>
                <div class="lol-grid runes-grid">
                  <div v-for="(rune, idx) in extractRunes(msg.text)" :key="'rune-'+idx" class="lol-item">
                    <img :src="getRuneImage(rune)" :alt="rune" class="lol-img" @error="handleImgError">
                    <span class="lol-name">{{ rune }}</span>
                  </div>
                </div>
              </div>
              <div v-if="isMatchupOnly(msg.text)" class="lol-section matchup-section">
                <div v-if="extractMatchups(msg.text).favorable.length > 0">
                  <h4 class="lol-section-title favorable">Matchups Favorables</h4>
                  <div class="matchup-list">
                    <span v-for="(champ, idx) in extractMatchups(msg.text).favorable" :key="'fav-'+idx" class="matchup-chip">{{ champ }}</span>
                  </div>
                </div>
                <div v-if="extractMatchups(msg.text).unfavorable.length > 0" style="margin-top: 12px;">
                  <h4 class="lol-section-title unfavorable">Matchups Desfavorables</h4>
                  <div class="matchup-list">
                    <span v-for="(champ, idx) in extractMatchups(msg.text).unfavorable" :key="'unfav-'+idx" class="matchup-chip unfavorable-chip">{{ champ }}</span>
                  </div>
                </div>
              </div>
              <p v-html="formatMessageClean(msg.text)"></p>
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
    getItemImage(name) {
      const itemMap = {
        'Espada Amplificadora': '1036', 'Daga': '1038', 'Botas de Lucidez': '3020',
        'Zhonyas': '3157', 'Rabadon': '3089', 'Sombrero de Morello': '3165',
        'Velo del Hakim': '3115', 'Tormento de Liandry': '3088', 'Liandry': '3088',
        'Práctica del Vacío': '3135', 'Filo del Infinito': '3031', 'Guantelete del Inmortal': '3053',
        'Rocafuerte': '3065', 'Armadura de Warmog': '3083', 'Warmog': '3083',
        'Espada del Rey Exiliado': '3033', 'Cuchilla Obsidiana': '3139', 'Hilibrand': '3124',
        'Eco de Kole': '3107', 'Kole': '3107', 'Malla': '1043', 'Guantelete de Hechicero': '3152',
        'Hechicero': '3152', 'Botas de Mercurio': '3111', 'Mercurio': '3111',
        'Botas de Placas': '3047', 'Placas de Acero': '3047', 'Cuchilla Ardiente': '3124',
        'Cortafuegos': '3146', 'Sek': '3146', 'Proyector de Sek': '3146',
        'Botas de Mobilidad': '3009', 'Espada Vampirica': '1037', 'Vampirica': '1037',
        'Guantelete Zeal': '1031', 'Zeal': '1031', 'Muerte Negra': '3135',
        'Espada del Rey Exiliado': '3033'
      };
      const id = itemMap[name] || itemMap[name.replace(/['"]/g, '')];
      return id ? `https://ddragon.leagueoflegends.com/cdn/16.9.1/img/item/${id}.png` : '';
    },
    getRuneImage(name) {
      const runeMap = {
        'Arcane Comet': 'perk-images/Styles/Sorcery/ArcaneComet/ArcaneComet.png',
        'Aery': 'perk-images/Styles/Sorcery/SummonAery/SummonAery.png',
        'Phase Rush': 'perk-images/Styles/Sorcery/PhaseRush/PhaseRush.png',
        'Manaflow Band': 'perk-images/Styles/Sorcery/ManaflowBand/ManaflowBand.png',
        'Transcendencia': 'perk-images/Styles/Sorcery/Transcendence/Transcendence.png',
        'Absolute Focus': 'perk-images/Styles/Sorcery/AbsoluteFocus/AbsoluteFocus.png',
        'Scorch': 'perk-images/Styles/Sorcery/Scorch/Scorch.png',
        'Waterwalking': 'perk-images/Styles/Sorcery/Waterwalking/Waterwalking.png',
        'Gathering Storm': 'perk-images/Styles/Sorcery/GatheringStorm/GatheringStorm.png',
        'Celerity': 'perk-images/Styles/Sorcery/Celerity/Celerity.png',
        'Electrocute': 'perk-images/Styles/Domination/Electrocute/Electrocute.png',
        'Predator': 'perk-images/Styles/Domination/NullifyingOrb/Predator.png',
        'Dark Harvest': 'perk-images/Styles/Domination/DarkHarvest/DarkHarvest.png',
        'Hail of Blades': 'perk-images/Styles/Domination/HailOfBlades/HailOfBlades.png',
        'Cheap Shot': 'perk-images/Styles/Domination/CheapShot/CheapShot.png',
        'Taste of Blood': 'perk-images/Styles/Domination/TasteOfBlood/GreenTerror_TasteOfBlood.png',
        'Sudden Impact': 'perk-images/Styles/Domination/SuddenImpact/SuddenImpact.png',
        'Zombie Ward': 'perk-images/Styles/Domination/ZombieWard/ZombieWard.png',
        'Ghost Poro': 'perk-images/Styles/Domination/GhostPoro/GhostPoro.png',
        'Eyeball Collection': 'perk-images/Styles/Domination/EyeballCollection/EyeballCollection.png',
        'Treasure Hunter': 'perk-images/Styles/Domination/TreasureHunter/TreasureHunter.png',
        'Relentless Hunter': 'perk-images/Styles/Domination/RelentlessHunter/RelentlessHunter.png',
        'Ultimate Hunter': 'perk-images/Styles/Domination/UltimateHunter/UltimateHunter.png',
        'Press the Attack': 'perk-images/Styles/Precision/PressTheAttack/PressTheAttack.png',
        'Lethal Tempo': 'perk-images/Styles/Precision/LethalTempo/LethalTempoTemp.png',
        'Fleet Footwork': 'perk-images/Styles/Precision/FleetFootwork/FleetFootwork.png',
        'Conqueror': 'perk-images/Styles/Precision/Conqueror/Conqueror.png',
        'Triumph': 'perk-images/Styles/Precision/Triumph.png',
        'Presence of Mind': 'perk-images/Styles/Precision/PresenceOfMind/PresenceOfMind.png',
        'Legend Alacrity': 'perk-images/Styles/Precision/LegendAlacrity/LegendAlacrity.png',
        'Legend Haste': 'perk-images/Styles/Precision/LegendHaste/LegendHaste.png',
        'Legend Bloodline': 'perk-images/Styles/Precision/LegendBloodline/LegendBloodline.png',
        'Coup de Grace': 'perk-images/Styles/Precision/CoupDeGrace/CoupDeGrace.png',
        'Cut Down': 'perk-images/Styles/Precision/CutDown/CutDown.png',
        'Last Stand': 'perk-images/Styles/Sorcery/LastStand/LastStand.png',
        'Grasp': 'perk-images/Styles/Resolve/GraspOfTheUndying/GraspOfTheUndying.png',
        'Aftershock': 'perk-images/Styles/Resolve/VeteranAftershock/VeteranAftershock.png',
        'Guardian': 'perk-images/Styles/Resolve/Guardian/Guardian.png',
        'Demolish': 'perk-images/Styles/Resolve/Demolish/Demolish.png',
        'Font of Life': 'perk-images/Styles/Resolve/FontOfLife/FontOfLife.png',
        'Shield Bash': 'perk-images/Styles/Resolve/MirrorShell/MirrorShell.png',
        'Conditioning': 'perk-images/Styles/Resolve/Conditioning/Conditioning.png',
        'Second Wind': 'perk-images/Styles/Resolve/SecondWind/SecondWind.png',
        'Bone Plating': 'perk-images/Styles/Resolve/BonePlating/BonePlating.png',
        'Overgrowth': 'perk-images/Styles/Resolve/Overgrowth/Overgrowth.png',
        'Revitalize': 'perk-images/Styles/Resolve/Revitalize/Revitalize.png',
        'Unflinching': 'perk-images/Styles/Sorcery/Unflinching/Unflinching.png',
        'Glacial Augment': 'perk-images/Styles/Inspiration/GlacialAugment/GlacialAugment.png',
        'Kleptomancy': 'perk-images/Styles/Inspiration/Kleptomancy/Kleptomancy.png',
        'Unsealed Spellbook': 'perk-images/Styles/Inspiration/UnsealedSpellbook/UnsealedSpellbook.png',
        'Hextech Flashtraption': 'perk-images/Styles/Inspiration/HextechFlashtraption/HextechFlashtraption.png',
        'Magical Footwear': 'perk-images/Styles/Inspiration/MagicalFootwear/MagicalFootware.png',
        'Perfect Timing': 'perk-images/Styles/Inspiration/PerfectTiming/AlchemistCabinet.png',
        'Future\'s Market': 'perk-images/Styles/Inspiration/FuturesMarket/FuturesMarket.png',
        'Minion Dematerializer': 'perk-images/Styles/Inspiration/MinionDematerializer/MinionDematerializer.png',
        'Biscuit Delivery': 'perk-images/Styles/Inspiration/BiscuitDelivery/BiscuitDelivery.png',
        'Cosmic Insight': 'perk-images/Styles/Inspiration/CosmicInsight/CosmicInsight.png',
        'Approach Velocity': 'perk-images/Styles/Resolve/ApproachVelocity/ApproachVelocity.png',
        'Jack Of All Trades': 'perk-images/Styles/Inspiration/JackOfAllTrades/JackofAllTrades.png',
        'Time Warp Tonic': 'perk-images/Styles/Inspiration/TimeWarpTonic/TimeWarpTonic.png',
        'Nullifying Orb': 'perk-images/Styles/Domination/NullifyingOrb/Predator.png'
      };
      const path = runeMap[name] || runeMap[name.replace(/['"]/g, '')];
      return path ? `https://ddragon.leagueoflegends.com/cdn/img/${path}` : '';
    },
    extractItems(text) {
      const regex = /\[\[item:([^\]]+)\]\]/gi;
      const matches = [];
      let match;
      while ((match = regex.exec(text)) !== null) {
        if (!matches.includes(match[1])) matches.push(match[1]);
      }
      return matches;
    },
    extractRunes(text) {
      const regex = /\[\[rune:([^\]]+)\]\]/gi;
      const matches = [];
      let match;
      while ((match = regex.exec(text)) !== null) {
        if (!matches.includes(match[1])) matches.push(match[1]);
      }
      return matches;
    },
    isMatchupOnly(text) {
      const lower = text.toLowerCase();
      return lower.includes('matchup') || lower.includes('favorable') || lower.includes('desfavorable') || lower.includes('counters') || lower.includes('contra');
    },
    extractMatchups(text) {
      const result = { favorable: [], unfavorable: [] };
      const lines = text.split('\n');
      const allChamps = ['Garen', 'Darius', 'Ahri', 'Yasuo', 'Jinx', 'Lux', 'Thresh', 'Blitzcrank', 'Vayne', 'Teemo', 'Nasus', 'Yorick', 'Sett', 'Quinn', 'Kayle', 'Gankplank', 'Fiora', 'Jax', 'Riven', 'Irelia', 'Camille', 'Aatrox', 'Mordekaiser', 'Kayne', 'Pantheon', 'Renekton', 'Urgot', 'Caitlyn', 'Ezreal', 'Tristana', 'Miss Fortune', 'Ashe', 'Sivir', 'Jhin', 'Zed', 'Talons', 'Fizz', 'Katarina', 'Akali', 'LeBlanc', 'Syndra', 'Orianna', 'Viktor', 'Malzahar', 'Xerath', 'Lux', 'Morgana', 'Zyra', 'Nami', 'Sona', 'Soraka', 'Lulu', 'Janna', 'Karma', 'Leona', 'Nautilus', 'Tham', 'Braum', 'Alistar', 'Taric', 'Poppy', 'Gnar', 'Kled', 'Rumble', 'Heimerdinger', 'Swain', 'Vladimir', 'Ryze', 'Anivia', 'Kassadin', 'Taliyah', 'Zoe', 'Neeko', 'Sylas', 'Qiyana', 'Sett', 'Akshan', 'Viego', 'Samira', 'Zeri', 'Aphelios', 'Senna', 'Pyke', 'Rakan', 'Xayah', 'Kaisa', 'Nilah', 'Belveth'];
      
      const textLower = text.toLowerCase();
      
      if (textLower.includes('favorable') || textLower.includes('buenos') || textLower.includes('easy') || textLower.includes('facil')) {
        const favorableSection = text.match(/(?:favorable|buenos|easy|facil)[:\s]+([A-Za-z\s,]+)/i);
        if (favorableSection && favorableSection[1]) {
          const names = favorableSection[1].split(/[,\/]/).map(n => n.trim()).filter(n => n);
          result.favorable = names.filter(n => n.length > 2 && n.length < 15);
        }
      }
      
      if (textLower.includes('desfavorable') || textLower.includes('malos') || textLower.includes('hard') || textLower.includes('difícil') || textLower.includes('counter')) {
        const unfavorableSection = text.match(/(?:desfavorable|malos|hard|difícil|counter)[:\s]+([A-Za-z\s,]+)/i);
        if (unfavorableSection && unfavorableSection[1]) {
          const names = unfavorableSection[1].split(/[,\/]/).map(n => n.trim()).filter(n => n);
          result.unfavorable = names.filter(n => n.length > 2 && n.length < 15);
        }
      }
      
      if (result.favorable.length === 0 && result.unfavorable.length === 0) {
        const words = text.split(/\s+/);
        const potentialChamps = words.filter(w => allChamps.some(c => c.toLowerCase() === w.toLowerCase()));
        result.favorable = potentialChamps.slice(0, 5);
      }
      
      return result;
    },
    formatMessageClean(text) {
      let cleaned = text.replace(/\[\[item:[^\]]+\]\]/gi, '').replace(/\[\[rune:[^\]]+\]\]/gi, '');
      cleaned = cleaned.replace(/\n/g, '<br>');
      return cleaned;
    },
    handleImgError(e) {
      e.target.style.display = 'none';
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

.lol-tag {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.8rem;
  margin: 2px;
}

.lol-icon {
  width: 18px;
  height: 18px;
  object-fit: contain;
}

.item-tag {
  background: rgba(50, 100, 200, 0.3);
  border: 1px solid rgba(100, 150, 255, 0.4);
  color: #7ab8ff;
}

.rune-tag {
  background: rgba(150, 50, 200, 0.3);
  border: 1px solid rgba(180, 100, 255, 0.4);
  color: #d4a0ff;
}

.lol-section {
  margin: 10px 0;
  padding: 10px;
  background: rgba(0, 0, 0, 0.2);
  border-radius: 8px;
}

.lol-section-title {
  margin: 0 0 8px 0;
  font-size: 0.85rem;
  color: #aaa;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.lol-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
}

.items-grid .lol-item {
  border-color: rgba(80, 120, 255, 0.4);
  background: rgba(40, 60, 120, 0.3);
}

.runes-grid .lol-item {
  border-color: rgba(180, 80, 255, 0.4);
  background: rgba(80, 40, 120, 0.3);
}

.lol-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  border-radius: 8px;
  padding: 8px;
  border: 1px solid;
}

.lol-img {
  width: 48px;
  height: 48px;
  object-fit: contain;
}

.lol-name {
  font-size: 0.7rem;
  color: #ccc;
  text-align: center;
  margin-top: 4px;
}

.matchup-section {
  border-left: 3px solid rgba(100, 200, 100, 0.5);
}

.matchup-section .lol-section-title.favorable {
  color: #5dba5d;
}

.matchup-section .lol-section-title.unfavorable {
  color: #e05050;
}

.matchup-list {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.matchup-chip {
  background: rgba(80, 160, 80, 0.3);
  border: 1px solid rgba(100, 200, 100, 0.5);
  color: #8ee88e;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 0.8rem;
}

.unfavorable-chip {
  background: rgba(180, 60, 60, 0.3);
  border: 1px solid rgba(220, 80, 80, 0.5);
  color: #e08080;
}
</style>