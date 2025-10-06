<template>
  <div id="app">
    <Navbar />
    <RouterView />
    <Footer />
  </div>
</template>

<script>
import { RouterView } from 'vue-router';
import Navbar from './components/navbar/nav.vue';
import Footer from './components/Footer/Footer.vue';

export default {
  name: 'App',
  components: { RouterView, Navbar, Footer }
};
</script>
