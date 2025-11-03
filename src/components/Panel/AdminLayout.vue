<template>
  <div class="admin-layout">
    <!-- Error message if user is not admin -->
    <div v-if="!isUserAdmin" class="error-container">
      <div class="error-box">
        <i class="fas fa-lock-open"></i>
        <h2>Access Denied</h2>
        <p>You do not have permission to access the admin panel.</p>
        <router-link to="/home" class="home-btn">
          <i class="fas fa-arrow-left"></i> Back to Home
        </router-link>
      </div>
    </div>

    <!-- Admin Panel -->
    <template v-else>
      <aside class="sidebar">
        <h2>♻️ Admin Panel</h2>
        <nav>
          <router-link to="/admin/users" class="link" active-class="active">
            <i class="fa-solid fa-user icon"></i>
            Users
          </router-link>

          <router-link to="/admin/disposal-rules" class="link" active-class="active">
            <i class="fa-solid fa-trash icon"></i>
            Disposal Rules
          </router-link>

          <router-link to="/admin/user-scans" class="link" active-class="active">
            <i class="fa-solid fa-qrcode icon"></i>
            User Scans
          </router-link>
        </nav>
      </aside>

      <main class="content">
        <router-view />
      </main>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuth } from '@/composables/useAuth'

const { user } = useAuth()

// Check if user is admin
const isUserAdmin = computed(() => {
  if (!user.value) return false
  const role = user.value.role?.toLowerCase() || ''
  return role === 'admin'
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

.admin-layout {
  display: flex;
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
}

/* Error Container */
.error-container {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  min-height: 100vh;
  background: radial-gradient(160% 120% at 50% 0%, rgba(15, 23, 42, 0.05) 0%, rgba(15, 23, 42, 0) 60%);
}

.error-box {
  text-align: center;
  padding: 60px 40px;
  background: linear-gradient(145deg, #ffffff 0%, #f7f9fc 100%);
  border-radius: 22px;
  border: 1.5px solid rgba(15, 23, 42, 0.18);
  box-shadow:
    0 20px 48px rgba(15, 23, 42, 0.14),
    0 3px 12px rgba(15, 23, 42, 0.1);
  max-width: 500px;
}

.error-box i {
  font-size: 64px;
  color: #dc3545;
  margin-bottom: 20px;
  display: block;
}

.error-box h2 {
  font-size: 32px;
  font-weight: 700;
  color: #222;
  margin: 0 0 12px 0;
}

.error-box p {
  font-size: 16px;
  color: #555;
  margin: 0 0 30px 0;
}

.home-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 12px 28px;
  background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
  color: white;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.home-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(76, 175, 80, 0.3);
}

/* Sidebar */
.sidebar {
  width: 230px;
  background: linear-gradient(180deg, #30475E, #222831);
  color: white;
  padding: 2rem 1rem;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.sidebar h2 {
  font-weight: 600;
  margin-bottom: 1.5rem;
}

.link {
  color: #ddd;
  text-decoration: none;
  margin: 0.5rem 0;
  padding: 0.6rem 1rem;
  border-radius: 8px;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  gap: 0.6rem;
  width: 100%;
}

.link:hover {
  background: #3A506B;
  color: white;
}

.active {
  background: #00ADB5;
  color: white;
}

.icon {
  width: 18px;
  text-align: center;
}

.content {
  flex: 1;
  padding: 2rem;
  background: #f8f9fa;
}
</style>