<template>
  <section class="registration-page">
    <h1>Registration</h1>

    <form class="reg-form" @submit.prevent="handleSubmit">
      <div class="field">
        <label for="username">Name</label>
        <input type="text" id="username" v-model="form.username" placeholder="Enter your full name" required />
      </div>

      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" v-model="form.email" placeholder="Enter your email address" required />
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" v-model="form.password" placeholder="Create a password" required />
      </div>

      <div class="field">
        <label for="confirm">Confirm Password</label>
        <input type="password" id="confirm" v-model="form.confirmPassword" placeholder="Re-enter your password" required />
      </div>

      <div class="buttons">
        <button type="button" class="btn btn-secondary" @click="goBack">Back</button>
        <button type="submit" class="btn btn-primary">Register</button>
      </div>
    </form>
  </section>
</template>

<script>
export default {
  name: 'RegistrationForm',
  data() {
    return {
      form: {
        username: '',
        email: '',
        password: '',
        confirmPassword: ''
      }
    };
  },
  methods: {
    handleSubmit() {
      console.log('Form submitted:', this.form);
    },
    goBack() {
      if (this.$router) {
        this.$router.back();
      }
    }
  }
};
</script>

<style src="./RegistrationForm.css" scoped></style>
