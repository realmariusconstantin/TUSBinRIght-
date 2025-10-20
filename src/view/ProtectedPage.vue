<template>
  <div class="protected-page">
    <h1>Protected Page</h1>
    
    <!-- Loading state -->
    <div v-if="isLoading" class="loading">
      <p>Loading profile...</p>
    </div>
    
    <!-- User authenticated - show content -->
    <div v-else-if="user" class="user-profile">
      <h2>Welcome, {{ user.name }}!</h2>
      <div class="user-info">
        <p><strong>Email:</strong> {{ user.email }}</p>
        <p><strong>ID:</strong> {{ user.id }}</p>
      </div>
      
      <div class="actions">
        <Logout />
      </div>
    </div>
    
    <!-- Not authenticated - show error -->
    <div v-else class="error-state">
      <p class="error">⚠️ You must be logged in to view this page.</p>
      <button @click="goToLogin" class="btn-primary">Go to Login</button>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import { useRouter } from 'vue-router';
import Logout from '@/components/Logout.vue';

// Use auth composable
const { user, isLoading, fetchUser } = useAuth();
const router = useRouter();

// Check authentication when component mounts
onMounted(async () => {
    // Call protected endpoint /api/profile
    // JWT cookie is automatically sent with request (withCredentials: true)
    const result = await fetchUser();
    
    if (!result.success) {
        // JWT invalid/expired - redirect to login after 2 seconds
        setTimeout(() => {
            router.push('/login');
        }, 2000);
    }
});

// Navigate to login
const goToLogin = () => {
    router.push('/login');
};
</script>

<style scoped>
.protected-page {
    max-width: 600px;
    margin: 40px auto;
    padding: 24px;
}

.protected-page h1 {
    font-size: 32px;
    margin-bottom: 24px;
    color: #111;
}

.loading {
    text-align: center;
    padding: 40px;
    color: #666;
}

.user-profile {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
}

.user-profile h2 {
    font-size: 24px;
    margin-bottom: 16px;
    color: #111;
}

.user-info {
    margin: 20px 0;
}

.user-info p {
    margin: 8px 0;
    font-size: 16px;
    color: #374151;
}

.actions {
    margin-top: 24px;
}

.error-state {
    text-align: center;
    padding: 40px;
}

.error {
    color: #dc2626;
    font-size: 16px;
    margin-bottom: 16px;
}

.btn-primary {
    padding: 10px 20px;
    background: #111;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary:hover {
    background: #000;
}
</style>

<!--
🧠 Explanation: Protected Page Component

This component demonstrates how to protect routes using HttpOnly cookies:

1. MOUNTING:
   - Component calls fetchUser() which makes GET request to /api/profile
   - Browser automatically sends JWT cookie with request (withCredentials: true)
   
2. BACKEND VERIFICATION:
   - CodeIgniter reads JWT from HttpOnly cookie
   - Validates the token signature and expiration
   - Returns user data if valid, or 401 error if invalid/expired

3. FRONTEND RESPONSE:
   - If successful: Display user profile data
   - If 401 error: Axios interceptor automatically redirects to /login
   - If other error: Show error message and redirect button

SECURITY:
- JWT never exposed to JavaScript (stored in HttpOnly cookie)
- XSS attacks cannot steal the token
- CSRF protection via SameSite cookie attribute (set by backend)
- Automatic expiration handling by backend

NO MANUAL TOKEN MANAGEMENT:
- No localStorage.getItem('token')
- No Authorization headers to manually set
- Browser handles everything automatically via cookies
-->
