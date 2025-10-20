<template>
    <section class="registration-page signup-page">
        <form class="reg-form" @submit.prevent="handleSubmit">
            <h1>Log in</h1>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" v-model="form.email" placeholder="Enter your email address" required />
                <!-- Display backend validation error for email field -->
                <p v-if="errors.email" class="error">{{ errors.email }}</p>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" v-model="form.password" placeholder="Enter your password"
                    required />
                <!-- Display backend validation error for password field -->
                <p v-if="errors.password" class="error">{{ errors.password }}</p>
            </div>

            <!-- Display success message from backend -->
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