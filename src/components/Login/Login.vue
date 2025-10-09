<template>
    <section class="registration-page signup-page">
        <form class="reg-form" @submit.prevent="handleSubmit">
            <h1>Log in</h1>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" v-model="form.email" placeholder="Enter your email address" required />
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" v-model="form.password" placeholder="Enter your password"
                    required />
            </div>

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
            }
        };
    },
    methods: {
        async handleSubmit() {
            try {
                const { data } = await api.post('/login', {
                    email: this.form.email,
                    password: this.form.password
                });

                // Backend doesn't return a JWT/token yet — it returns user info
                if (data.status === 'success') {
                    // Optionally store user details locally
                    localStorage.setItem('user', JSON.stringify(data.user));
                    alert('Login successful!');
                    this.$router.push('/home');
                } else {
                    alert(data.message || 'Invalid credentials');
                }
            } catch (e) {
                const msg =
                    e?.response?.data?.message ||
                    e?.response?.data ||
                    'Invalid email or password';
                alert(typeof msg === 'string' ? msg : JSON.stringify(msg));
            }
        },
        goBack() {
            if (this.$router) this.$router.back();
        }
    }
};
</script>

<style src="./Login.css" scoped></style>
