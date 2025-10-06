import { createRouter, createWebHistory } from 'vue-router'
import RegistrationForm from '@/components/Registration/RegistrationForm.vue'
import LoginForm from '@/components/Login/Login.vue'

const routes = [
    { path: '/register', component: RegistrationForm },
    { path: '/Login', component: LoginForm }
]

export default createRouter({
    history: createWebHistory(),
    routes
})