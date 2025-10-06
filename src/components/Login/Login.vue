<template>
    <section class="registration-page signup-page">
        <h1>Log in</h1>

        <form class="reg-form" @submit.prevent="handleSubmit">
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
export default {
    name: 'LoginForm',
    data() {
        return {
            form: {
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

<style src="./Login.css" scoped></style>
