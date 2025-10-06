<template>
  <div id="app">
    <navbar />
    <router-view />
  </div>
</template>

<script>
import { RouterView } from 'vue-router';
import navbar from './components/navbar/nav.vue';

export default {
  name: 'App',
  components: { RouterView, navbar }
};
</script>