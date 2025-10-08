<template>
    <section class="registration-page signup-page">
      

        <form class="reg-form" @submit.prevent="handleSubmit">
            <h1>Log in</h1>
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
                localStorage.setItem('token', data.token);
                this.$router.push('/home');
            } catch (e) {
                console.error('Login error:', e);
                alert('Invalid email or password');
            }
        },
        goBack() {
            if (this.$router) {
                this.$router.back();
            }
        }
    }
};
</script>

<style src="./Login.css" scoped></style>
