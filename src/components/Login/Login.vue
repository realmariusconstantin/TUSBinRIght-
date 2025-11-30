<template>
    <section class="registration-page signup-page">
        <form class="reg-form" @submit.prevent="handleSubmit">
            <h1>Log in</h1>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" v-model="form.email" placeholder="Enter your email address"
                    @blur="validateEmailField" @input="clearFieldError('email')" required />
                <p v-if="fieldErrors.email" class="error">{{ fieldErrors.email }}</p>
                <p v-if="errors.email && !fieldErrors.email" class="error">{{ errors.email }}</p>
                <p v-if="errors.general && !fieldErrors.email" class="error">{{ errors.general }}</p>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="password-input-wrapper">
                    <input :type="showPassword ? 'text' : 'password'" id="password" v-model="form.password"
                        placeholder="Enter your password" @blur="validatePasswordField"
                        @input="clearFieldError('password')" required />
                    <button type="button" class="toggle-password" @click="togglePassword" :aria-pressed="showPassword"
                        aria-label="Toggle password visibility">
                        <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.81 21.81 0 0 1 5.11-6.06">
                            </path>
                            <path d="M1 1l22 22"></path>
                        </svg>
                    </button>
                </div>
                <p v-if="fieldErrors.password" class="error">{{ fieldErrors.password }}</p>
                <p v-if="errors.password && !fieldErrors.password" class="error">{{ errors.password }}</p>
            </div>

            <p v-if="successMessage" class="success">{{ successMessage }}</p>

            <!-- Rate Limit Warning -->
            <div v-if="rateLimited" class="rate-limit-warning">
                <i class="fas fa-lock"></i>
                <div class="warning-content">
                    <p class="warning-title">Account Temporarily Locked</p>
                    <p class="warning-message">{{ rateLimitMessage }}</p>
                    <div v-if="remainingTime > 0" class="countdown">
                        <span>Try again in: <strong>{{ formatCountdown }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Attempts Remaining -->
            <div v-if="attemptsRemaining > 0 && attemptsRemaining < 5 && !rateLimited" class="attempts-warning">
                <i class="fas fa-exclamation-circle"></i>
                <p>{{ attemptsRemaining }} attempt{{ attemptsRemaining !== 1 ? 's' : '' }} remaining before account
                    lockout</p>
            </div>

            <div class="buttons">
                <button type="button" class="btn btn-secondary" @click="goBack">Back</button>
                <button type="submit" class="btn btn-primary" :disabled="rateLimited">Log in</button>
            </div>
        </form>
    </section>
</template>
<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
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

// Field-level validation errors
const fieldErrors = ref({
    email: '',
    password: ''
});

// Password visibility toggle
const showPassword = ref(false);

// Rate limiting state
const rateLimited = ref(false);
const rateLimitMessage = ref('');
const remainingTime = ref(0);
let countdownInterval = null;

// Attempts remaining
const attemptsRemaining = ref(0);

// Computed countdown display
const formatCountdown = computed(() => {
    if (remainingTime.value <= 0) return '0:00';
    const minutes = Math.floor(remainingTime.value / 60);
    const seconds = remainingTime.value % 60;
    return `${minutes}:${seconds.toString().padStart(2, '0')}`;
});

// Email validation regex
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

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

    if (password.length < 6) {
        fieldErrors.value.password = 'Password must be at least 6 characters';
        return false;
    }

    fieldErrors.value.password = '';
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

// Start countdown timer
const startCountdown = (seconds) => {
    remainingTime.value = seconds;
    if (countdownInterval) clearInterval(countdownInterval);

    countdownInterval = setInterval(() => {
        remainingTime.value--;
        if (remainingTime.value <= 0) {
            clearInterval(countdownInterval);
            rateLimited.value = false;
        }
    }, 1000);
};

// Handle form submission
const handleSubmit = async () => {
    // Validate fields
    const emailValid = validateEmailField();
    const passwordValid = validatePasswordField();

    if (!emailValid || !passwordValid) {
        return;
    }

    // Trim whitespace
    const email = form.value.email.trim();
    const password = form.value.password;

    // Send credentials to backend (JWT stored in HttpOnly cookie)
    const result = await login(email, password);

    if (result.success) {
        // Clear rate limit state on successful login
        rateLimited.value = false;
        attemptsRemaining.value = 0;
        if (countdownInterval) clearInterval(countdownInterval);

        // Redirect to home after successful login
        setTimeout(() => {
            router.push('/home');
        }, 1000);
    } else {
        // Check for rate limiting
        if (result.data?.rateLimited) {
            rateLimited.value = true;
            rateLimitMessage.value = result.data?.message || 'Too many failed attempts';
            startCountdown(result.data?.remainingTime || 900); // Default 15 minutes
        } else if (result.data?.attemptsRemaining !== undefined) {
            attemptsRemaining.value = result.data.attemptsRemaining;
        }
    }
};

// Go back navigation
const goBack = () => {
    router.back();
};

// Cleanup interval on unmount
onUnmounted(() => {
    if (countdownInterval) clearInterval(countdownInterval);
});
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
    border: 1px solid rgba(0, 0, 0, 0.12);
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

.toggle-password svg {
    display: block;
}

.toggle-password:active {
    transform: scale(0.98);
}
</style>