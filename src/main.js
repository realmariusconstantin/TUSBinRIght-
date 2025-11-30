import { createApp } from 'vue';
import router from '../router';
import App from './App.vue';
import './style.css';
import '@fortawesome/fontawesome-free/css/all.css'

createApp(App).use(router).mount('#app');