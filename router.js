import { createRouter, createWebHistory } from 'vue-router';

// Correct file locations
import Home from '@/view/Home/Home.vue';
import RegistrationForm from '@/components/Registration/RegistrationForm.vue';
import LoginForm from '@/components/Login/Login.vue';

const routes = [
    { path: '/', component: Home },
    { path: '/home', component: Home },
    { path: '/register', component: RegistrationForm },
    { path: '/login', component: LoginForm },
    { path: '/:pathMatch(.*)*', redirect: '/' }
];

export default createRouter({
    history: createWebHistory(),
    routes
});
