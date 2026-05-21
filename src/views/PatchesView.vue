<template>
  <div class="patches-section">
    <div class="profile-header">
      <h2>Notas del Parche</h2>
      <p>Últimas actualizaciones de League of Legends</p>
    </div>

    <div v-if="loading" class="skins-loading">Cargando notas del parche...</div>

    <div v-if="error" class="error-message" style="text-align: center;">{{ error }}</div>

    <div class="patches-container" v-if="patches.length > 0 && !loading">
      <div v-for="patch in patches" :key="patch.id" class="patch-card" @click="togglePatch(patch.id)">
        <div class="patch-header">
          <div class="patch-version">
            <span class="patch-version-badge">{{ patch.version }}</span>
            <span class="patch-date">{{ patch.date }}</span>
          </div>
          <div class="patch-title-bar">
            <h3 class="patch-title">{{ patch.title }}</h3>
            <span class="patch-toggle">{{ expandedPatch === patch.id ? '▲' : '▼' }}</span>
          </div>
        </div>
        <div v-if="expandedPatch === patch.id" class="patch-body">
          <div v-for="(section, idx) in patch.sections" :key="idx" class="patch-section">
            <h4 class="patch-section-title">{{ section.name }}</h4>
            <ul class="patch-changes">
              <li v-for="(change, cIdx) in section.changes" :key="cIdx" :class="change.type">
                <span v-if="change.type === 'buff'" class="change-icon">▲</span>
                <span v-if="change.type === 'nerf'" class="change-icon">▼</span>
                <span v-if="change.type === 'adjustment'" class="change-icon">⟳</span>
                <strong>{{ change.champion }}</strong>: {{ change.description }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PatchesView',
  data() {
    return {
      patches: [],
      expandedPatch: null,
      loading: true,
      error: ''
    };
  },
  created() {
    this.fetchPatches();
  },
  methods: {
    togglePatch(id) {
      this.expandedPatch = this.expandedPatch === id ? null : id;
    },
    async fetchPatches() {
      try {
        const res = await fetch('https://ddragon.leagueoflegends.com/cdn/15.5.1/data/en_US/champion.json');
        const champData = await res.json();
        const champions = Object.keys(champData.data);

        const mockPatches = [
          {
            id: 1,
            version: '15.9',
            date: 'Mayo 2026',
            title: 'Parche 15.9 - Equilibrium de Medio',
            sections: [
              { name: 'Campeones Buffeados', changes: champions.slice(0, 5).map(c => ({ champion: c, description: 'Daño base aumentado y escalado con AP mejorado.', type: 'buff' })) },
              { name: 'Campeones Nerfeados', changes: champions.slice(5, 10).map(c => ({ champion: c, description: 'Reducción de daño base y enfriamiento aumentado.', type: 'nerf' })) },
              { name: 'Ajustes', changes: champions.slice(10, 13).map(c => ({ champion: c, description: 'Reajuste de estadísticas base.', type: 'adjustment' })) }
            ]
          },
          {
            id: 2,
            version: '15.8',
            date: 'Abril 2026',
            title: 'Parche 15.8 - Campeones de Medio',
            sections: [
              { name: 'Campeones Buffeados', changes: champions.slice(13, 18).map(c => ({ champion: c, description: 'Mejora en escalado de daño.', type: 'buff' })) },
              { name: 'Campeones Nerfeados', changes: champions.slice(18, 22).map(c => ({ champion: c, description: 'Reducción de daño en habilidades.', type: 'nerf' })) },
              { name: 'Objetos', changes: [{ champion: 'Filo del Infinito', description: 'Daño crítico aumentado a 40%.', type: 'buff' }, { champion: 'Cuchilla Obsidiana', description: 'Precio reducido en 200 de oro.', type: 'buff' }] }
            ]
          },
          {
            id: 3,
            version: '15.7',
            date: 'Marzo 2026',
            title: 'Parche 15.7 - Renovación de Items',
            sections: [
              { name: 'Campeones Buffeados', changes: champions.slice(22, 27).map(c => ({ champion: c, description: 'Velocidad de movimiento aumentada.', type: 'buff' })) },
              { name: 'Campeones Nerfeados', changes: champions.slice(27, 31).map(c => ({ champion: c, description: 'Daño de definitiva reducido.', type: 'nerf' })) },
              { name: 'Sistema', changes: [{ champion: 'Torres', description: 'Daño de torres aumentado contra campeones.', type: 'adjustment' }, { champion: 'Épicos', description: 'Tiempo de reaparición de Barón Nashor ajustado.', type: 'adjustment' }] }
            ]
          }
        ];
        this.patches = mockPatches;
      } catch (e) {
        this.error = 'No se pudieron cargar las notas del parche';
      }
      this.loading = false;
    }
  }
};
</script>
