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
  background: var(--bg-primary);
  transition: background-color 0.3s ease;
}

/* Error Container */
.error-container {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  min-height: 100vh;
  background: var(--bg-primary);
  transition: background-color 0.3s ease;
}

.error-box {
  text-align: center;
  padding: 60px 40px;
  background: var(--bg-secondary);
  border-radius: 22px;
  border: 1.5px solid var(--border-color);
  box-shadow: 0 20px 48px var(--shadow);
  max-width: 500px;
  transition: background-color 0.3s ease, border-color 0.3s ease;
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
  color: var(--text-primary);
  margin: 0 0 12px 0;
}

.error-box p {
  font-size: 16px;
  color: var(--text-secondary);
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
  background: var(--bg-secondary);
  color: var(--text-primary);
  padding: 2rem 1rem;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  border-right: 1px solid var(--border-color);
  transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
}

.sidebar h2 {
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: var(--text-primary);
}

.link {
  color: var(--text-secondary);
  text-decoration: none;
  margin: 0.5rem 0;
  padding: 0.6rem 1rem;
  border-radius: 8px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 0.6rem;
  width: 100%;
}

.link:hover {
  background: var(--accent-green);
  color: white;
}

.active {
  background: var(--accent-green);
  color: white;
}

.icon {
  width: 18px;
  text-align: center;
}

.content {
  flex: 1;
  padding: 2rem;
  background: var(--bg-primary);
  transition: background-color 0.3s ease;
}
</style>