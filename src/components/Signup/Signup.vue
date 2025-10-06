<template>
    <div class="reg-wrapper">
        <form class="Signup-form" @submit.prevent="handleSubmit">
            <div class="field">
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" v-model="form.email" placeholder="Enter your email address"
                        required />
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" v-model="form.password" placeholder="Create a password"
                        required />
                </div>
                <div class="buttons">
                    <button type="submit" class="btn btn-primary">Sign up</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    name: 'SignupForm',
    data() {
        return {
            form: {
                email: '',
                password: ''
            }
        };
    },
    methods: {
        handleSubmit() {
            console.log('Form submitted:', this.form);
        }
    }
};
</script>

<style src="./Signup.css" scoped></style>
