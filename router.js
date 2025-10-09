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
    { 
        path: '/', 
        component: Home
        // No authentication required - accessible to everyone
    },
    { 
        path: '/home', 
        component: Home
        // No authentication required - accessible to everyone
    },
    { 
        path: '/register', 
        component: RegistrationForm,
        meta: { guestOnly: true } // Redirect to home if already logged in
    },
    { 
        path: '/login', 
        component: LoginForm,
        meta: { guestOnly: true }
    },
    { 
        path: '/material/plastic', 
        component: PlasticPage
        // No authentication required - accessible to everyone
    },
    { 
        path: '/material/can', 
        component: CanPage
        // No authentication required - accessible to everyone
    },
    { 
        path: '/material/glass', 
        component: GlassPage
        // No authentication required - accessible to everyone
    },
    { 
        path: '/material/paper', 
        component: PaperPage
        // No authentication required - accessible to everyone
    },
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
router.beforeEach((to, from, next) => {
    // Check if user has authentication token
    const token = localStorage.getItem('auth_token');
    const isAuthenticated = !!token;
    
    // Check if route requires authentication
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
    
    // Check if route is for guests only (login/register)
    const guestOnly = to.matched.some(record => record.meta.guestOnly);
    
    console.log('Navigation Guard:', {
        to: to.path,
        requiresAuth,
        guestOnly,
        isAuthenticated
    });
    
    // If route requires auth and user is not authenticated
    if (requiresAuth && !isAuthenticated) {
        console.log('Redirecting to login - authentication required');
        next('/login');
        return;
    }
    
    // If route is guest-only and user is authenticated
    if (guestOnly && isAuthenticated) {
        console.log('Redirecting to home - already authenticated');
        next('/');
        return;
    }
    
    // Allow navigation
    next();
});

export default router;
