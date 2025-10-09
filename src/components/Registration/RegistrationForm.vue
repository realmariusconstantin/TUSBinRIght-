<template>
  <section class="registration-page">
    <form class="reg-form" @submit.prevent="handleSubmit">
      <h1>Registration</h1>

      <div class="field">
        <label for="name">Name</label>
        <input type="text" id="username" v-model="form.name" placeholder="Enter your full name" required />
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
      </div>

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
      }
    };
  },
  methods: {
    async handleSubmit() {
      try {
        const payload = {
          name: this.form.name,
          email: this.form.email,
          password: this.form.password,
          confirmPassword: this.form.confirmPassword
        };

        const { data } = await api.post('/register', payload);
  alert(data.message || 'Registered successfully!');
  // After successful registration, send user to public home page
  this.$router.push('/');
      } catch (e) {
        const msgs =
          e?.response?.data?.messages || e?.response?.data || 'Registration failed';
        alert(typeof msgs === 'string' ? msgs : JSON.stringify(msgs));
      }
    },
    goBack() {
      if (this.$router) this.$router.back();
    }
  }
};
</script>

<style src="./RegistrationForm.css" scoped></style>
