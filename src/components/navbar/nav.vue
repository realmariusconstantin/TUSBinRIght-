<template>
    <nav>
        <div>
            <h1 class="logo">
                <a href="/home">
                    <img :src="logo" alt="" aria-hidden="true">
                </a>

            </h1>
        </div>


        <div class="nav-links">
            <router-link to="/register">Register</router-link>
            <router-link to="/Login">Log in</router-link>
        </div>
    </nav>
</template>
<script>
import logo from '@/images/Logo.png';

export default {
    name: 'navbar',
    data() {
        return {
            logo
        };
    }
};
</script>
<style src="./nav.css" scoped></style>