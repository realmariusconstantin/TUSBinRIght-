import { createRouter, createWebHistory } from 'vue-router';

// Correct file locations
import Home from '@/view/Home/Home.vue';
import RegistrationForm from '@/components/Registration/RegistrationForm.vue';
import LoginForm from '@/components/Login/Login.vue';
import PlasticPage from '@/view/Materials/Plastic.vue';
import CanPage from '@/view/Materials/Can.vue';
import GlassPage from '@/view/Materials/Glass.vue';
import PaperPage from '@/view/Materials/Paper.vue';

const routes = [
    { path: '/', component: Home },
    { path: '/home', component: Home },
    { path: '/register', component: RegistrationForm },
    { path: '/login', component: LoginForm },
    { path: '/material/plastic', component: PlasticPage },
    { path: '/material/can', component: CanPage },
    { path: '/material/glass', component: GlassPage },
    { path: '/material/paper', component: PaperPage },
    { path: '/:pathMatch(.*)*', redirect: '/' }
];

export default createRouter({
    history: createWebHistory(),
    routes
});
