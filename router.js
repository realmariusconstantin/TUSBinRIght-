import { createRouter, createWebHistory } from 'vue-router';

// Correct file locations
import Home from '@/view/Home/Home.vue';
import RegistrationForm from '@/components/Registration/RegistrationForm.vue';
import LoginForm from '@/components/Login/Login.vue';
import PlasticPage from '@/view/Materials/Plastic.vue';
import CanPage from '@/view/Materials/Can.vue';
import GlassPage from '@/view/Materials/Glass.vue';
import PaperPage from '@/view/Materials/Paper.vue';
import ProtectedPage from '@/view/ProtectedPage.vue';
import RecyclingInfo from '@/view/RecyclingInfo/RecyclingInfo.vue';

// Admin pages
import AdminLayout from '@/components/Panel/AdminLayout.vue'
import Users from '@/components/Panel/Users.vue'
import BinSteps from '@/components/Panel/BinSteps.vue'
import Items from '@/components/Panel/Items.vue'

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
    { 
        path: '/recycling-info', 
        component: RecyclingInfo
        // No authentication required - accessible to everyone
    },
    { 
        path: '/profile', 
        component: ProtectedPage,
        meta: { requiresAuth: true }
        // Protected route - placeholder for user profile (to be implemented by colleagues)
    },
    {
        path: '/admin',
        component: AdminLayout,
        meta: { requiresAuth: true, adminOnly: true },
        children: [
            { path: '', redirect: '/admin/users' },
            { path: 'users', component: Users },
            { path: 'bin-steps', component: BinSteps },
            { path: 'items', component: Items }
        ]
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
// No global navigation guard — routes are public by design.

export default router;
