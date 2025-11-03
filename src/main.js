import { createApp } from 'vue';
import router from '../router';
import App from './App.vue';
import '@fortawesome/fontawesome-free/css/all.min.css'

createApp(App).use(router).mount('#app');