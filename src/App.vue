<template>
  <div id="app">
    <Navbar />
    <RouterView />
    <Footer v-if="!isAdminRoute" />
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import Navbar from './components/navbar/nav.vue'
import Footer from './components/Footer/Footer.vue'
import { useDarkMode } from '@/composables/useDarkMode'

const route = useRoute()
const { initializeDarkMode } = useDarkMode()

// Hide footer on admin routes
const isAdminRoute = computed(() => {
  return route.path.startsWith('/admin')
})

// Initialize dark mode on app mount
onMounted(() => {
  initializeDarkMode()
  console.log('✅ App initialized with dark mode support')
})
</script>

