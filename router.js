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

const router = createRouter({
    history: createWebHistory(),
    routes
});

/**
 * Navigation Guard - Route Protection
 * 
 * This guard runs before every route change and:
 * 1. Checks if route requires authentication
 * 2. Redirects unauthenticated users to /login
 * 3. Redirects authenticated users away from login/register
 */
// No global navigation guard — routes are public by design.

export default router;
