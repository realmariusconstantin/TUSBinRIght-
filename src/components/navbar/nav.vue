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
            <template v-if="!user">
                <router-link to="/register">Register</router-link>
                <router-link to="/login">Log in</router-link>
            </template>
            
            <template v-else>
                <div class="profile-dropdown" ref="dropdownRef">
                    <button @click="toggleDropdown" class="profile-btn" :aria-expanded="isDropdownOpen">
                        <div class="profile-avatar">
                            <img v-if="user.avatar" :src="user.avatar" :alt="user.name" />
                            <span v-else class="avatar-initials">{{ userInitials }}</span>
                        </div>
                        <span class="username-display">{{ user.name || 'User' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="dropdown-arrow" :class="{ 'arrow-open': isDropdownOpen }">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    
                    <div v-if="isDropdownOpen" class="dropdown-menu">
                        <router-link to="/profile" class="dropdown-item" @click="closeDropdown">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Profile
                        </router-link>
                        
                        <router-link v-if="isAdmin" to="/admin" class="dropdown-item" @click="closeDropdown">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                            Admin Panel
                        </router-link>
                        
                        <div class="dropdown-divider"></div>
                        
                        <button @click="handleLogout" class="dropdown-item logout-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Logout
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import logo from '@/images/RenovaLogo.png';

const router = useRouter();
const { user, logout, fetchUser, initUser } = useAuth();

const isDropdownOpen = ref(false);
const dropdownRef = ref(null);

// Compute user initials for avatar fallback
const userInitials = computed(() => {
    if (!user.value?.name) return 'U';
    const names = user.value.name.split(' ');
    if (names.length >= 2) {
        return (names[0][0] + names[names.length - 1][0]).toUpperCase();
    }
    return user.value.name.substring(0, 2).toUpperCase();
});

// Check if user is admin
const isAdmin = computed(() => {
    if (!user.value) {
        console.log('No user found');
        return false;
    }
    console.log('User data:', user.value);
    console.log('User role:', user.value.role);
    console.log('User role_id:', user.value.role_id);
    
    // Check multiple possible admin indicators
    const role = user.value.role?.toLowerCase();
    const roleId = user.value.role_id;
    
    const result = role === 'admin' || roleId === 1 || roleId === '1';
    console.log('Is admin?', result);
    return result;
});

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

const closeDropdown = () => {
    isDropdownOpen.value = false;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        closeDropdown();
    }
};

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
    
    // Add click outside listener
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    // Remove click outside listener
    document.removeEventListener('click', handleClickOutside);
});

const handleLogout = async () => {
    closeDropdown();
    const result = await logout();
    if (result.success) {
        router.push('/login');
    }
};
</script>

<style src="./nav.css" scoped></style>