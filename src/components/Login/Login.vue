<template>
    <section class="registration-page signup-page">
        <form class="reg-form" @submit.prevent="handleSubmit">
            <h1>Log in</h1>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" v-model="form.email" placeholder="Enter your email address" required />
                <p v-if="errors.email" class="error">{{ errors.email }}</p>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="password-input-wrapper">
                    <input :type="showPassword ? 'text' : 'password'" id="password" v-model="form.password" placeholder="Enter your password" required />
                    <button type="button" class="toggle-password" @click="togglePassword" :aria-pressed="showPassword" aria-label="Toggle password visibility">
                        <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                          <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.81 21.81 0 0 1 5.11-6.06"></path>
                          <path d="M1 1l22 22"></path>
                        </svg>
                    </button>
                </div>
                <p v-if="errors.password" class="error">{{ errors.password }}</p>
            </div>

            <p v-if="successMessage" class="success">{{ successMessage }}</p>

            <div class="buttons">
                <button type="button" class="btn btn-secondary" @click="goBack">Back</button>
                <button type="submit" class="btn btn-primary">Log in</button>
            </div>
        </form>
    </section>
</template>

<script setup>
import { ref } from 'vue';
import { useAuth } from '@/composables/useAuth';
import { useRouter } from 'vue-router';

// Use auth composable
const { login, errors, successMessage, isLoading } = useAuth();
const router = useRouter();

// Form data
const form = ref({
    email: '',
    password: ''
});

// Password visibility toggle
const showPassword = ref(false);

// Toggle password visibility
const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

// Handle form submission
const handleSubmit = async () => {
    // Send credentials to backend (JWT stored in HttpOnly cookie)
    const result = await login(form.value.email, form.value.password);
    
    if (result.success) {
        // Redirect to home after successful login
        setTimeout(() => {
            router.push('/home');
        }, 1000);
    }
    // Errors are automatically handled by useAuth composable
};

// Go back navigation
const goBack = () => {
    router.back();
};
</script>

<style src="./Login.css" scoped></style>

<style scoped>
.password-input-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}
.password-input-wrapper input {
    flex: 1;
    min-width: 0;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid rgba(0,0,0,0.12);
}
.toggle-password {
    border: none;
    background: transparent;
    padding: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #444;
    border-radius: 8px;
}
.toggle-password svg { display: block; }
.toggle-password:active { transform: scale(0.98); }
</style>