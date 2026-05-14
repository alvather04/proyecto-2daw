import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';

import LoginView from './views/LoginView.vue';
import HomeView from './views/HomeView.vue';
import ForumView from './views/ForumView.vue';
import ProfileView from './views/ProfileView.vue';
import ContactView from './views/ContactView.vue';

const routes = [
  { path: '/login', component: LoginView },
  { path: '/', component: HomeView },
  { path: '/forum', component: ForumView },
  { path: '/profile', component: ProfileView },
  { path: '/contact', component: ContactView },
  { path: '/:pathMatch(.*)*', redirect: '/login' }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

const app = createApp(App);
app.use(router);
app.mount('#app');