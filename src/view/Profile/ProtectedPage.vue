<script setup>
import { ref, onMounted } from 'vue'
import api from '@/lib/api'

// current user info
const me = ref({ id: '', name: '', email: '' })

// edit form
const newEmail = ref('')
const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')

// messages
const emailMsg = ref('')
const pwdMsg = ref('')
const loading = ref(false)

async function loadMe() {
  try {
    const { data } = await api.get('/me')
    if (data?.ok) {
      me.value = data.user
      newEmail.value = data.user.email
    }
  } catch (err) {
    alert('Session expired. Please log in again.')
    window.location.href = '/login'
  }
}

// update email
async function updateEmail() {
  if (!newEmail.value) {
    emailMsg.value = 'Please enter a valid email'
    return
  }
  loading.value = true
  try {
    await api.put('/me/email', { email: newEmail.value })
    emailMsg.value = '✅ Email updated successfully'
    me.value.email = newEmail.value
  } catch (err) {
    emailMsg.value = '❌ Failed to update email'
  } finally {
    loading.value = false
  }
}

// update password
async function updatePassword() {
  if (!currentPassword.value || !newPassword.value || !confirmPassword.value) {
    pwdMsg.value = 'All password fields are required'
    return
  }
  if (newPassword.value !== confirmPassword.value) {
    pwdMsg.value = 'Passwords do not match'
    return
  }

  loading.value = true
  try {
    await api.put('/me/password', {
      currentPassword: currentPassword.value,
      newPassword: newPassword.value,
    })
    pwdMsg.value = '✅ Password changed successfully'
    currentPassword.value = ''
    newPassword.value = ''
    confirmPassword.value = ''
  } catch (err) {
    pwdMsg.value = '❌ Failed to update password'
  } finally {
    loading.value = false
  }
}

function logout() {
  window.location.href = '/login'
}

onMounted(loadMe)
</script>

<template>
  <div class="container">
    <h2>Edit Profile</h2>

    <!-- Email Section -->
    <div class="section">
      <h3>Change Email</h3>
      <label>Current Email: <strong>{{ me.email }}</strong></label>
      <input v-model="newEmail" type="email" placeholder="Enter new email" />
      <button :disabled="loading" @click="updateEmail">Update Email</button>
      <p class="msg">{{ emailMsg }}</p>
    </div>

    <!-- Password Section -->
    <div class="section">
      <h3>Change Password</h3>
      <input v-model="currentPassword" type="password" placeholder="Current Password" />
      <input v-model="newPassword" type="password" placeholder="New Password" />
      <input v-model="confirmPassword" type="password" placeholder="Confirm New Password" />
      <button :disabled="loading" @click="updatePassword">Update Password</button>
      <p class="msg">{{ pwdMsg }}</p>
    </div>

    <button class="logout-btn" @click="logout">Logout</button>
  </div>
</template>

<style scoped>
.container {
  max-width: 600px;
  margin: 3rem auto;
  background: #fff;
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.section {
  margin-bottom: 2rem;
}

input {
  width: 100%;
  padding: 10px;
  margin-top: 8px;
  border: 1px solid #ccc;
  border-radius: 8px;
}

button {
  margin-top: 12px;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  background-color: #00843d;
  color: #fff;
  font-weight: bold;
}

.logout-btn {
  background-color: #d32f2f;
  width: 100%;
}

.msg {
  margin-top: 8px;
  color: #333;
  font-size: 0.9rem;
}
</style>
