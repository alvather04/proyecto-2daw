// ============================================================
// INICIO DE LA APLICACIÓN
// ============================================================

// Trae las herramientas de Vue
import { createApp } from 'vue';

// Trae el sistema de navegación entre páginas
import { createRouter, createWebHistory } from 'vue-router';

// Trae el componente principal de la aplicación
import App from './App.vue';

// ============================================================
// LAS PÁGINAS DE LA APLICACIÓN
// ============================================================
import LoginView from './views/LoginView.vue';
import HomeView from './views/HomeView.vue';
import ForumView from './views/ForumView.vue';
import ProfileView from './views/ProfileView.vue';
import ContactView from './views/ContactView.vue';
import SkinsView from './views/SkinsView.vue';
import RotationView from './views/RotationView.vue';
import PatchesView from './views/PatchesView.vue';
import LiveGameView from './views/LiveGameView.vue';
import SettingsView from './views/SettingsView.vue';
import ItemsView from './views/ItemsView.vue';
import StreamsView from './views/StreamsView.vue';
import LeaderboardView from './views/LeaderboardView.vue';

const routes = [
  { path: '/login', component: LoginView },
  { path: '/', component: HomeView },
  { path: '/forum', component: ForumView },
  { path: '/profile', component: ProfileView },
  { path: '/contact', component: ContactView },
  { path: '/skins', component: SkinsView },
  { path: '/rotation', component: RotationView },
  { path: '/patches', component: PatchesView },
  { path: '/live-game', component: LiveGameView },
  { path: '/settings', component: SettingsView },
  { path: '/items', component: ItemsView },
  { path: '/streams', component: StreamsView },
  { path: '/leaderboard', component: LeaderboardView },
  { path: '/:pathMatch(.*)*', redirect: '/login' }
];

// ============================================================
// PREPARA EL NAVEGADOR DE PÁGINAS
// ============================================================
const router = createRouter({
  history: createWebHistory(),
  routes
});

// ============================================================
// CREA Y MUESTRA LA APLICACIÓN
// ============================================================
const app = createApp(App);
app.use(router);
app.mount('#app');
