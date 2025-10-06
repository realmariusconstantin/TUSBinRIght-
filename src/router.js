import { createRouter, createWebHistory } from 'vue-router'
import RegistrationForm from '@/components/Registration/RegistrationForm.vue'
import SignupForm from '@/components/Signup/Signup.vue'

const routes = [
    { path: '/register', component: RegistrationForm },
    { path: '/signup', component: SignupForm }
]

export default createRouter({
    history: createWebHistory(),
    routes
})