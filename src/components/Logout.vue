<template>
  <button @click="handleLogout" :disabled="isLoading" class="btn-logout">
    {{ isLoading ? 'Logging out...' : 'Logout' }}
  </button>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth';
import { useRouter } from 'vue-router';

// Use auth composable
const { logout, isLoading } = useAuth();
const router = useRouter();

// Handle logout
const handleLogout = async () => {
    // Call backend /logout endpoint to clear HttpOnly cookie
    const result = await logout();
    
    if (result.success) {
        // Redirect to login page after logout
        router.push('/login');
    } else {
        // Even if backend call fails, redirect anyway (local data cleared)
        router.push('/login');
    }
};
</script>

<style scoped>
.btn-logout {
    padding: 8px 16px;
    background: #dc2626;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-logout:hover {
    background: #b91c1c;
}

.btn-logout:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}
</style>

<!--
Explanation: Logout Component

This component handles user logout by:
1. Calling the backend /logout endpoint with { withCredentials: true }
2. Backend clears the HttpOnly cookie containing the JWT
3. Frontend clears localStorage user data
4. Redirects to /login page
-->
