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

<script>
import api from '@/lib/api';

export default {
  name: 'RegistrationForm',
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        confirmPassword: ''
      },
      // Store validation errors from CodeIgniter backend
      errors: {},
      // Store success message from backend
      successMessage: ''
    };
  },
  methods: {
    async handleSubmit() {
      // Clear previous errors and messages before submitting
      this.errors = {};
      this.successMessage = '';

      try {
        const payload = {
          name: this.form.name,
          email: this.form.email,
          password: this.form.password,
          confirmPassword: this.form.confirmPassword
        };

        const { data } = await api.post('/register', payload);
        
        // Handle successful registration
        if (data.status === 'success') {
          this.successMessage = data.message || 'User registered successfully!';
          
          // Store the token if returned during registration
          if (data.token) {
            localStorage.setItem('auth_token', data.token);
          }
          
          // Redirect after a short delay so user sees success message
          setTimeout(() => {
            this.$router.push('/home');
          }, 1500);
        }
      } catch (e) {
        // 🎯 Catch backend validation errors from CodeIgniter
        // CodeIgniter returns: { status: "error", errors: { email: "...", password: "..." } }
        if (e.response && e.response.data && e.response.data.errors) {
          // Store the errors object to display under each input field
          this.errors = e.response.data.errors;
        } else {
          // Handle other types of errors (network, server errors, etc.)
          const msgs =
            e?.response?.data?.message || e?.response?.data || 'Registration failed';
          alert(typeof msgs === 'string' ? msgs : JSON.stringify(msgs));
        }
      }
    },
    goBack() {
      if (this.$router) this.$router.back();
    }
  }
};
</script>

<style src="./RegistrationForm.css" scoped></style>
