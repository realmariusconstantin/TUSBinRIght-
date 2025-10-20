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
                    </button>
                    
                    <div v-if="isDropdownOpen" class="dropdown-menu">
                        <router-link to="/profile" class="dropdown-item" @click="closeDropdown">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Profile
                        </router-link>
                        
                        <router-link to="/recycling-info" class="dropdown-item" @click="closeDropdown">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            Recycling Information
                        </router-link>
                        
                        <router-link v-if="user.role === 'admin'" to="/admin" class="dropdown-item" @click="closeDropdown">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Admin Page
                        </router-link>
                        
                        <div class="dropdown-divider"></div>
                        
                        <button @click="handleLogout" class="dropdown-item logout-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
import logo from '@/images/Logo.png';

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