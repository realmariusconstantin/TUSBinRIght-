import { createRouter, createWebHistory } from 'vue-router';

// Correct file locations
import Home from '@/view/Home.vue';
import RegistrationForm from '@/components/Registration/RegistrationForm.vue';
import LoginForm from '@/components/Login/Login.vue';

const routes = [
    { path: '/', redirect: '/register' },
    { path: '/home', component: Home },
    { path: '/register', component: RegistrationForm },
    { path: '/login', component: LoginForm },
    { path: '/:pathMatch(.*)*', redirect: '/register' }
];

export default createRouter({
    history: createWebHistory(),
    routes
});
