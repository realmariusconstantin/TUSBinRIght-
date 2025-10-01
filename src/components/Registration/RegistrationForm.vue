<template>

<form @submit.prevent="handleSubmit">
  <div>
    <label for="username">Username:</label>
    <input type="text" id="username" v-model="form.username" required />
  </div>
  <div>
    <label for="email">Email:</label>
    <input type="email" id="email" v-model="form.email" required />
  </div>
  <div>
    <label for="password">Password:</label>
    <input type="password" id="password" v-model="form.password" required />
  </div>
  <button type="submit">Register</button>
  </form>

</template>

<script>
export default {
    data() {
        return {
        form: {
            username: '',
            email: '',
            password: ''
        }
        };
    },
    methods: {
        handleSubmit() {
        // Handle form submission logic here
        console.log('Form submitted:', this.form);
        }
    }
    };
</script>
