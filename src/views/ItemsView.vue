<template>
  <div class="items-section">
    <div class="profile-header">
      <h2>Objetos - League of Legends</h2>
      <p>Explora todos los objetos del juego</p>
    </div>

    <div class="items-search-bar">
      <input v-model="searchQuery" type="text" placeholder="Buscar objeto..." class="skins-search-input" @input="filterItems">
      <select v-model="selectedTag" class="skins-select" @change="filterItems">
        <option value="">Todas las categorías</option>
        <option v-for="tag in tags" :key="tag" :value="tag">{{ tag }}</option>
      </select>
    </div>

    <div v-if="loading" class="skins-loading">Cargando objetos...</div>

    <div v-if="filteredItems.length > 0 && !loading" class="items-grid">
      <div v-for="item in filteredItems" :key="item.id" class="item-card">
        <div class="item-img-wrapper">
          <img :src="`https://ddragon.leagueoflegends.com/cdn/15.5.1/img/item/${item.id}.png`" :alt="item.name" class="item-img" @error="onItemError" loading="lazy">
        </div>
        <div class="item-info">
          <h4 class="item-name">{{ item.name }}</h4>
          <div class="item-gold">
            <span class="item-gold-total">{{ item.gold?.total || 0 }}g</span>
            <span v-if="item.gold?.base" class="item-gold-base">({{ item.gold.base }}g + {{ item.gold.purchasable ? 'Combinación' : 'No comprable' }})</span>
          </div>
          <p class="item-description" v-html="item.description"></p>
          <div class="item-tags">
            <span v-for="tag in item.tags" :key="tag" class="item-tag">{{ tag }}</span>
          </div>
          <div v-if="item.from && item.from.length" class="item-builds-from">
            <span>Construye desde: </span>
            <img v-for="fromId in item.from" :key="fromId" :src="`https://ddragon.leagueoflegends.com/cdn/15.5.1/img/item/${fromId}.png`" class="mini-item" :title="getItemName(fromId)" @error="onItemError">
          </div>
          <div v-if="item.into && item.into.length" class="item-builds-into">
            <span>Construye en: </span>
            <img v-for="intoId in item.into" :key="intoId" :src="`https://ddragon.leagueoflegends.com/cdn/15.5.1/img/item/${intoId}.png`" class="mini-item" :title="getItemName(intoId)" @error="onItemError">
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ItemsView',
  data() {
    return {
      allItems: [],
      filteredItems: [],
      searchQuery: '',
      selectedTag: '',
      loading: true,
      tags: []
    };
  },
  created() {
    this.fetchItems();
  },
  methods: {
    async fetchItems() {
      try {
        const res = await fetch('https://ddragon.leagueoflegends.com/cdn/15.5.1/data/en_US/item.json');
        const data = await res.json();
        this.allItems = Object.entries(data.data).map(([id, item]) => ({ id, ...item }))
          .filter(item => item.gold?.purchasable && item.name && !item.name.includes('(Mï¿½todo'));
        const tagSet = new Set();
        this.allItems.forEach(item => item.tags?.forEach(t => tagSet.add(t)));
        this.tags = [...tagSet].sort();
        this.filteredItems = this.allItems;
      } catch (e) {
        console.error('Error loading items:', e);
      }
      this.loading = false;
    },
    filterItems() {
      const q = this.searchQuery.toLowerCase();
      this.filteredItems = this.allItems.filter(item => {
        const matchesSearch = !q || item.name.toLowerCase().includes(q) || item.plaintext?.toLowerCase().includes(q);
        const matchesTag = !this.selectedTag || item.tags?.includes(this.selectedTag);
        return matchesSearch && matchesTag;
      });
    },
    getItemName(id) {
      const item = this.allItems.find(i => i.id === id);
      return item?.name || id;
    },
    onItemError(e) {
      e.target.style.display = 'none';
    }
  }
};
</script>
