<template>
  <section class="registration-page">
    <form class="reg-form" @submit.prevent="handleSubmit">
      <h1>Registration</h1>

      <div class="field">
        <label for="name">Name</label>
        <input type="text" id="username" v-model="form.name" placeholder="Enter your full name" required />
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
        <p v-if="errors.email" class="error">{{ errors.email }}</p>
      </div>

      <div class="field">
        <label for="password">Password</label>
        <div class="password-input-wrapper">
          <input
            :type="showPassword ? 'text' : 'password'"
            id="password"
            v-model="form.password"
            placeholder="Create a password"
            required
          />
          <button
            type="button"
            class="toggle-password"
            @click="togglePassword"
            :aria-pressed="showPassword"
            :aria-label="showPassword ? 'Hide password' : 'Show password'"
          >
            <!-- eye open -->
            <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <!-- eye closed -->
            <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.81 21.81 0 0 1 5.11-6.06"></path>
              <path d="M1 1l22 22"></path>
            </svg>
          </button>
        </div>
        <p v-if="errors.password" class="error">{{ errors.password }}</p>
      </div>

      <div class="field">
        <label for="confirm">Confirm Password</label>
        <div class="password-input-wrapper">
          <input
            :type="showConfirmPassword ? 'text' : 'password'"
            id="confirm"
            v-model="form.confirmPassword"
            placeholder="Re-enter your password"
            required
          />
          <button
            type="button"
            class="toggle-password"
            @click="toggleConfirmPassword"
            :aria-pressed="showConfirmPassword"
            :aria-label="showConfirmPassword ? 'Hide confirm password' : 'Show confirm password'"
          >
            <svg v-if="showConfirmPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.81 21.81 0 0 1 5.11-6.06"></path>
              <path d="M1 1l22 22"></path>
            </svg>
          </button>
        </div>
        <p v-if="errors.confirmPassword" class="error">{{ errors.confirmPassword }}</p>
      </div>

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

// Password visibility toggles
const showPassword = ref(false);
const showConfirmPassword = ref(false);

// Toggle password visibility
const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

// Toggle confirm password visibility
const toggleConfirmPassword = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
};

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

<!-- подключаем исходный css для формы регистрации -->
<style src="./RegistrationForm.css" scoped></style>

<!-- небольшой scoped CSS только для корректного размещения кнопки-глазика -->
<style scoped>
.password-input-wrapper {
  display: flex;
  align-items: center;
  gap: 8px;
}
/* input внутри wrapper занимает доступное место, не ломая ширину .field input */
.password-input-wrapper input {
  flex: 1;
  min-width: 0;
  /* не меняем padding/width — базовые стили идут из RegistrationForm.css */
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