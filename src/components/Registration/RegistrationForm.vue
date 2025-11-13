<template>
  <section class="registration-page">
    <form class="reg-form" @submit.prevent="handleSubmit">
      <h1>Registration</h1>

      <div class="field">
        <label for="name">Name</label>
        <input 
          type="text" 
          id="username" 
          v-model="form.name" 
          placeholder="Enter your full name"
          @blur="validateNameField"
          @input="clearFieldError('name')"
          required 
        />
        <p v-if="fieldErrors.name" class="error">{{ fieldErrors.name }}</p>
        <p v-if="errors.name && !fieldErrors.name" class="error">{{ errors.name }}</p>
      </div>

      <div class="field">
        <label for="email">Email</label>
        <input
          type="email"
          id="email"
          v-model="form.email"
          placeholder="Enter your email address"
          @blur="validateEmailField"
          @input="clearFieldError('email')"
          required
        />
        <p v-if="fieldErrors.email" class="error">{{ fieldErrors.email }}</p>
        <p v-if="errors.email && !fieldErrors.email" class="error">{{ errors.email }}</p>
      </div>

      <div class="field">
        <label for="password">Password</label>
        <div class="password-input-wrapper">
          <input
            :type="showPassword ? 'text' : 'password'"
            id="password"
            v-model="form.password"
            placeholder="Create a password"
            @blur="validatePasswordField"
            @input="clearFieldError('password')"
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
        <!-- Password Strength Meter -->
        <PasswordStrengthMeter :password="form.password" @strength-change="passwordStrength = $event" />
        <p v-if="fieldErrors.password" class="error">{{ fieldErrors.password }}</p>
        <p v-if="errors.password && !fieldErrors.password" class="error">{{ errors.password }}</p>
      </div>

      <div class="field">
        <label for="confirm">Confirm Password</label>
        <div class="password-input-wrapper">
          <input
            :type="showConfirmPassword ? 'text' : 'password'"
            id="confirm"
            v-model="form.confirmPassword"
            placeholder="Re-enter your password"
            @blur="validateConfirmPasswordField"
            @input="clearFieldError('confirmPassword')"
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
        <p v-if="fieldErrors.confirmPassword" class="error">{{ fieldErrors.confirmPassword }}</p>
        <p v-if="errors.confirmPassword && !fieldErrors.confirmPassword" class="error">{{ errors.confirmPassword }}</p>
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
import PasswordStrengthMeter from '@/components/PasswordStrengthMeter.vue';

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

// Field-level validation errors
const fieldErrors = ref({
    name: '',
    email: '',
    password: '',
    confirmPassword: ''
});

// Password strength data
const passwordStrength = ref({
    valid: false,
    score: 0,
    strength: 'weak'
});

// Password visibility toggles
const showPassword = ref(false);
const showConfirmPassword = ref(false);

// Email validation regex
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Validate name field
const validateNameField = () => {
    const name = form.value.name.trim();
    
    if (!name) {
        fieldErrors.value.name = 'Name is required';
        return false;
    }
    
    if (name.length < 3) {
        fieldErrors.value.name = 'Name must be at least 3 characters';
        return false;
    }
    
    if (name.length > 150) {
        fieldErrors.value.name = 'Name must not exceed 150 characters';
        return false;
    }
    
    if (!/^[a-zA-Z0-9\s\-'.]+$/.test(name)) {
        fieldErrors.value.name = 'Name contains invalid characters';
        return false;
    }
    
    if (/\s{2,}/.test(name)) {
        fieldErrors.value.name = 'Name contains excessive spacing';
        return false;
    }
    
    fieldErrors.value.name = '';
    return true;
};

// Validate email field
const validateEmailField = () => {
    const email = form.value.email.trim();
    
    if (!email) {
        fieldErrors.value.email = 'Email is required';
        return false;
    }
    
    if (!emailRegex.test(email)) {
        fieldErrors.value.email = 'Please enter a valid email address';
        return false;
    }
    
    if (email.length > 255) {
        fieldErrors.value.email = 'Email is too long';
        return false;
    }
    
    fieldErrors.value.email = '';
    return true;
};

// Validate password field
const validatePasswordField = () => {
    const password = form.value.password;
    
    if (!password) {
        fieldErrors.value.password = 'Password is required';
        return false;
    }
    
    if (password.length < 8) {
        fieldErrors.value.password = 'Password must be at least 8 characters';
        return false;
    }
    
    if (password.length > 128) {
        fieldErrors.value.password = 'Password is too long';
        return false;
    }
    
    fieldErrors.value.password = '';
    return true;
};

// Validate confirm password field
const validateConfirmPasswordField = () => {
    const confirmPassword = form.value.confirmPassword;
    
    if (!confirmPassword) {
        fieldErrors.value.confirmPassword = 'Confirm password is required';
        return false;
    }
    
    if (form.value.password !== confirmPassword) {
        fieldErrors.value.confirmPassword = 'Passwords do not match';
        return false;
    }
    
    fieldErrors.value.confirmPassword = '';
    return true;
};

// Clear field error
const clearFieldError = (field) => {
    fieldErrors.value[field] = '';
};

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
    // Validate all fields
    const nameValid = validateNameField();
    const emailValid = validateEmailField();
    const passwordValid = validatePasswordField();
    const confirmPasswordValid = validateConfirmPasswordField();
    
    if (!nameValid || !emailValid || !passwordValid || !confirmPasswordValid) {
        return;
    }
    
    // Check password strength before submitting
    if (!passwordStrength.value.valid) {
        fieldErrors.value.password = 'Password does not meet security requirements. Check all requirements above.';
        return;
    }

    // Trim whitespace
    const trimmedName = form.value.name.trim();
    const trimmedEmail = form.value.email.trim();
    
    // Send registration data to backend
    const result = await register(
        trimmedName,
        trimmedEmail,
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