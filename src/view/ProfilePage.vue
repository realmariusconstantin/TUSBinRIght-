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
    const { data } = await api.get('/profile')
    if (data?.status === 'success' && data?.user) {
      me.value = data.user
      newEmail.value = data.user.email
    } else {
      alert('Failed to load profile. Please log in again.')
      window.location.href = '/login'
    }
  } catch (err) {
    console.error('Failed to load profile:', err)
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
    const { data } = await api.put('/profile/email', { email: newEmail.value })
    if (data?.status === 'success') {
      emailMsg.value = '✅ Email updated successfully'
      me.value.email = newEmail.value
    } else {
      emailMsg.value = '❌ ' + (data?.message || 'Failed to update email')
    }
  } catch (err) {
    emailMsg.value = '❌ ' + (err.response?.data?.message || 'Failed to update email')
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
    const { data } = await api.put('/profile/password', {
      currentPassword: currentPassword.value,
      newPassword: newPassword.value,
    })
    if (data?.status === 'success') {
      pwdMsg.value = '✅ Password changed successfully'
      currentPassword.value = ''
      newPassword.value = ''
      confirmPassword.value = ''
    } else {
      pwdMsg.value = '❌ ' + (data?.message || 'Failed to update password')
    }
  } catch (err) {
    pwdMsg.value = '❌ ' + (err.response?.data?.message || 'Failed to update password')
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

    <div class="profile-wrapper">
      <!-- Email Section -->
      <div class="section">
        <h3>Change Email</h3>
        <div class="field-group">
          <label>Current Email</label>
          <div class="current-value">{{ me.email }}</div>
        </div>
        <div class="field-group">
          <label for="new-email">New Email Address</label>
          <input 
            id="new-email"
            v-model="newEmail" 
            type="email" 
            placeholder="Enter your new email address" 
          />
        </div>
        <div class="button-group">
          <button class="update-btn" :disabled="loading" @click="updateEmail">
            <i class="fas fa-check"></i> Update Email
          </button>
        </div>
        <p v-if="emailMsg" class="msg">{{ emailMsg }}</p>
      </div>

      <!-- Password Section -->
      <div class="section">
        <h3>Change Password</h3>
        <div class="field-group">
          <label for="current-pwd">Current Password</label>
          <input 
            id="current-pwd"
            v-model="currentPassword" 
            type="password" 
            placeholder="Enter your current password" 
          />
        </div>
        <div class="field-group">
          <label for="new-pwd">New Password</label>
          <input 
            id="new-pwd"
            v-model="newPassword" 
            type="password" 
            placeholder="Enter your new password" 
          />
        </div>
        <div class="field-group">
          <label for="confirm-pwd">Confirm New Password</label>
          <input 
            id="confirm-pwd"
            v-model="confirmPassword" 
            type="password" 
            placeholder="Confirm your new password" 
          />
        </div>
        <div class="button-group">
          <button class="update-btn" :disabled="loading" @click="updatePassword">
            <i class="fas fa-lock"></i> Update Password
          </button>
        </div>
        <p v-if="pwdMsg" class="msg">{{ pwdMsg }}</p>
      </div>

      <!-- Logout Button -->
      <button class="logout-btn" @click="logout">
        <i class="fas fa-sign-out-alt"></i> Logout
      </button>
    </div>
  </div>
</template>

<style scoped>
.container {
  min-height: calc(100vh - 120px);
  padding: 48px 16px 72px;
  background: radial-gradient(160% 120% at 50% 0%, rgba(15, 23, 42, 0.05) 0%, rgba(15, 23, 42, 0) 60%);
  box-sizing: border-box;
}

.container h2 {
  text-align: center;
  font-size: 42px;
  color: #333;
  margin: 0 0 48px;
  letter-spacing: -0.02em;
  font-weight: 800;
}

.profile-wrapper {
  max-width: 600px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.section {
  background: linear-gradient(145deg, #ffffff 0%, #f7f9fc 100%);
  border-radius: 22px;
  border: 1.5px solid rgba(15, 23, 42, 0.18);
  box-shadow:
    0 20px 48px rgba(15, 23, 42, 0.14),
    0 3px 12px rgba(15, 23, 42, 0.1);
  padding: 32px 36px;
  margin-bottom: 0;
}

.section h3 {
  font-size: 18px;
  font-weight: 700;
  color: #222;
  margin: 0 0 20px 0;
  padding-bottom: 12px;
  border-bottom: 2px solid #4CAF50;
}

.field-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 20px;
}

.field-group:last-child {
  margin-bottom: 0;
}

label {
  font-size: 13px;
  font-weight: 600;
  color: #222;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.current-value {
  font-size: 16px;
  font-weight: 700;
  color: #4CAF50;
  padding: 12px 16px;
  background: rgba(76, 175, 80, 0.08);
  border-radius: 10px;
  border-left: 3px solid #4CAF50;
}

input {
  width: 100%;
  padding: 12px 16px;
  font-size: 16px;
  border: 1.5px solid rgba(15, 23, 42, 0.18);
  border-radius: 10px;
  outline: none;
  box-sizing: border-box;
  transition: all 0.3s ease;
  font-family: inherit;
}

input:focus {
  border-color: #4CAF50;
  background: rgba(76, 175, 80, 0.02);
  box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

input::placeholder {
  color: #999;
}

.button-group {
  display: flex;
  gap: 12px;
  margin-top: 24px;
}

button {
  flex: 1;
  padding: 14px 24px;
  font-size: 16px;
  font-weight: 600;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

button:not(:disabled):hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.update-btn {
  background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
  color: white;
}

.update-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #45a049 0%, #27692a 100%);
}

.logout-btn {
  width: 100%;
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
  color: white;
  margin-top: 32px;
}

.logout-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
}

.msg {
  margin-top: 12px;
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.msg:contains("✅"),
.msg:has-text("✅") {
  background: rgba(76, 175, 80, 0.15);
  color: #2E7D32;
  border-left: 3px solid #4CAF50;
}

.msg:contains("❌"),
.msg:has-text("❌") {
  background: rgba(220, 53, 69, 0.15);
  color: #c82333;
  border-left: 3px solid #dc3545;
}
</style>
