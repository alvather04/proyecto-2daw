<template>
  <div class="notif-container">
    <button class="notif-bell" @click="togglePanel">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
      </svg>
      <span v-if="unreadCount > 0" class="notif-badge">{{ unreadCount }}</span>
    </button>

    <div v-if="showPanel" class="notif-panel">
      <div class="notif-panel-header">
        <h3>Notificaciones</h3>
        <button class="notif-mark-read" @click="markAllRead" v-if="unreadCount > 0">✓ Marcar leídas</button>
      </div>
      <div class="notif-list">
        <div v-for="(notif, idx) in notifications" :key="idx" class="notif-item" :class="{ unread: !notif.read }" @click="markRead(idx)">
          <div class="notif-icon">{{ notif.icon }}</div>
          <div class="notif-content">
            <p class="notif-text">{{ notif.text }}</p>
            <span class="notif-time">{{ notif.time }}</span>
          </div>
        </div>
        <div v-if="notifications.length === 0" class="notif-empty">
          <p>No hay notificaciones</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Notifications',
  props: {
    notifications: { type: Array, default: () => [] }
  },
  data() {
    return { showPanel: false };
  },
  computed: {
    unreadCount() {
      return this.notifications.filter(n => !n.read).length;
    }
  },
  methods: {
    togglePanel() {
      this.showPanel = !this.showPanel;
    },
    markRead(idx) {
      if (!this.notifications[idx].read) {
        this.notifications[idx].read = true;
        this.$emit('update-notifications', this.notifications);
      }
    },
    markAllRead() {
      this.notifications.forEach(n => n.read = true);
      this.$emit('update-notifications', this.notifications);
    },
    handleClickOutside(e) {
      if (!this.$el.contains(e.target)) {
        this.showPanel = false;
      }
    }
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside);
  }
};
</script>

<style scoped>
.notif-container { position: relative; }
.notif-bell { background: none; border: none; color: var(--grey-text); cursor: pointer; padding: 8px; position: relative; display: flex; align-items: center; transition: color 0.3s; }
.notif-bell:hover { color: var(--gold); }
.notif-badge { position: absolute; top: 0; right: 0; background: #ef4444; color: #fff; font-size: 10px; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
.notif-panel { position: absolute; top: 100%; right: 0; width: 320px; background: rgba(15, 25, 35, 0.98); border: 1px solid var(--gold); border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.6); z-index: 2000; max-height: 400px; overflow-y: auto; margin-top: 8px; }
.notif-panel-header { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; border-bottom: 1px solid #333; position: sticky; top: 0; background: rgba(15, 25, 35, 0.98); }
.notif-panel-header h3 { color: var(--gold); font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin: 0; }
.notif-mark-read { background: none; border: 1px solid var(--blue-accent); color: var(--blue-accent); padding: 4px 10px; border-radius: 12px; cursor: pointer; font-size: 11px; }
.notif-list { padding: 8px; }
.notif-item { display: flex; gap: 10px; padding: 10px; border-radius: 8px; cursor: pointer; transition: background 0.2s; align-items: flex-start; }
.notif-item:hover { background: rgba(200,170,110,0.08); }
.notif-item.unread { background: rgba(10,200,185,0.08); border-left: 3px solid var(--blue-accent); }
.notif-icon { font-size: 18px; flex-shrink: 0; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; }
.notif-content { flex: 1; min-width: 0; }
.notif-text { margin: 0; font-size: 13px; color: #ddd; line-height: 1.4; }
.notif-time { font-size: 11px; color: var(--grey-text); }
.notif-empty { text-align: center; padding: 20px; color: var(--grey-text); font-size: 13px; }
</style>
