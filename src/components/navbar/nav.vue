<template>
    <nav>
        <div>
            <h1 class="logo">
                <a href="/home">
                    <img :src="logo" alt="" aria-hidden="true">
                </a>
            </h1>
        </div>

        <div class="nav-links">
            <router-link to="/home">Home</router-link>
            
            <template v-if="!user">
                <router-link to="/register">Register</router-link>
                <router-link to="/login">Log in</router-link>
            </template>
            
            <template v-else>
                <router-link v-if="user.role === 'admin'" to="/admin">Admin Page</router-link>
                <button @click="handleLogout" class="logout-btn">Logout</button>
            </template>
        </div>
    </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import logo from '@/images/Logo.png';

const router = useRouter();
const { user, logout, fetchUser, initUser } = useAuth();

onMounted(async () => {
    // Try to load user from localStorage first
    initUser();
    
    // If user exists in localStorage, verify with backend
    // Only fetch if we think we're logged in
    if (user.value) {
        const result = await fetchUser();
        // If fetch fails, user will be cleared automatically
        if (!result.success) {
            console.log('Session expired or invalid');
        }
    }
});

const handleLogout = async () => {
    const result = await logout();
    if (result.success) {
        router.push('/login');
    }
};
</script>

<style src="./nav.css" scoped></style>