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

<script>
import api from '@/lib/api';

export default {
    name: 'LoginForm',
    data() {
        return {
            form: {
                email: '',
                password: ''
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
                const { data } = await api.post('/login', {
                    email: this.form.email,
                    password: this.form.password
                });

                // Backend doesn't return a JWT/token yet — it returns user info
                if (data.status === 'success') {
                    this.successMessage = data.message || 'Login successful!';
                    // Optionally store user details locally
                    localStorage.setItem('user', JSON.stringify(data.user));
                    
                    // Redirect after a short delay so user sees success message
                    setTimeout(() => {
                        this.$router.push('/home');
                    }, 1000);
                } else {
                    alert(data.message || 'Invalid credentials');
                }
            } catch (e) {
                // 🎯 Catch backend validation errors from CodeIgniter
                // CodeIgniter returns: { status: "error", errors: { email: "...", password: "..." } }
                if (e.response && e.response.data && e.response.data.errors) {
                    // Store the errors object to display under each input field
                    this.errors = e.response.data.errors;
                } else {
                    // Handle other types of errors (network, server errors, etc.)
                    const msg =
                        e?.response?.data?.message ||
                        e?.response?.data ||
                        'Invalid email or password';
                    alert(typeof msg === 'string' ? msg : JSON.stringify(msg));
                }
            }
        },
        goBack() {
            if (this.$router) this.$router.back();
        }
    }
};
</script>

<style src="./Login.css" scoped></style>