<template>
  <div class="admin-layout">
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
  </div>
</template>

<script>
export default {
  name: 'AdminLayout'
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

.admin-layout {
  display: flex;
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
}

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