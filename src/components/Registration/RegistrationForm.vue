<template>
  <section class="registration-page">
    <form class="reg-form" @submit.prevent="handleSubmit">
      <h1>Registration</h1>

      <div class="field">
        <label for="name">Name</label>
        <input type="text" id="username" v-model="form.name" placeholder="Enter your full name" required />
        <!-- Display backend validation error for name field -->
        <p v-if="errors.name" class="error">{{ errors.name }}</p>
      </div>

      <div class="field">
        <label for="email">Email</label>
        <input
          type="email"
          id="email"
          v-model="form.email"
          placeholder="Enter your email address"
          required
        />
        <!-- Display backend validation error for email field -->
        <p v-if="errors.email" class="error">{{ errors.email }}</p>
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          v-model="form.password"
          placeholder="Create a password"
          required
        />
        <!-- Display backend validation error for password field -->
        <p v-if="errors.password" class="error">{{ errors.password }}</p>
      </div>

      <div class="field">
        <label for="confirm">Confirm Password</label>
        <input
          type="password"
          id="confirm"
          v-model="form.confirmPassword"
          placeholder="Re-enter your password"
          required
        />
        <!-- Display backend validation error for confirmPassword field -->
        <p v-if="errors.confirmPassword" class="error">{{ errors.confirmPassword }}</p>
      </div>

      <!-- Display success message from backend -->
      <p v-if="successMessage" class="success">{{ successMessage }}</p>

      <div class="buttons">
        <button type="button" class="btn btn-secondary" @click="goBack">
          Back
        </button>
        <button type="submit" class="btn btn-primary">
          Register
        </button>
      </div>
    </form>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { useAuth } from '@/composables/useAuth';
import { useRouter } from 'vue-router';

// Use auth composable
const { register, errors, successMessage, isLoading } = useAuth();
const router = useRouter();

// Form data
const form = ref({
    name: '',
    email: '',
    password: '',
    confirmPassword: ''
});

// Handle form submission
const handleSubmit = async () => {
    // Send registration data to backend
    const result = await register(
        form.value.name,
        form.value.email,
        form.value.password,
        form.value.confirmPassword
    );
    
    if (result.success) {
        // Registration now auto-logs in, redirect to home
        setTimeout(() => {
            router.push('/home');
        }, 1500);
    }
    // Errors are automatically handled by useAuth composable
};

// Go back navigation
const goBack = () => {
    router.back();
};
</script>

<style src="./RegistrationForm.css" scoped></style>
