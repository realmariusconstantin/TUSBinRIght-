<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/lib/api'
import { useCharts } from '@/composables/useCharts'
import { useDarkMode } from '@/composables/useDarkMode'
import ChartComponent from '@/components/ChartComponent.vue'

const me = ref({ id: '', name: '', email: '', avatar: '' })

const recyclingSummary = ref({
  can: 0,
  plastic: 0,
  paper: 0,
  glass: 0,
  total: 0
})

const materialChartData = ref({
  Plastic: { count: 0, carbonSaved: 0, drivingMinutes: 0 },
  Glass: { count: 0, carbonSaved: 0, drivingMinutes: 0 },
  Cans: { count: 0, carbonSaved: 0, drivingMinutes: 0 },
  Paper: { count: 0, carbonSaved: 0, drivingMinutes: 0 }
})

const MATERIAL_MAP = {
  1: 'Plastic',
  2: 'Cans',
  3: 'Glass',
  4: 'Paper'
}

const IMPACT_FACTORS = {
  Plastic: { carbon: 0.05, drive: 0.3 },
  Glass: { carbon: 0.03, drive: 0.2 },
  Cans: { carbon: 0.07, drive: 0.4 },
  Paper: { carbon: 0.02, drive: 0.1 }
}

const fileInput = ref(null)
const isUploadingAvatar = ref(false)
const avatarError = ref('')

function openFilePicker() {
  fileInput.value?.click()
}

async function handleAvatarUpload(event) {
  const file = event.target.files?.[0]
  if (!file) return
  const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
  if (!validTypes.includes(file.type)) {
    avatarError.value = 'Please select a valid image (JPEG, PNG, GIF, or WebP)'
    return
  }
  if (file.size > 5 * 1024 * 1024) {
    avatarError.value = 'Image size must be less than 5MB'
    return
  }
  avatarError.value = ''
  isUploadingAvatar.value = true
  try {
    const formData = new FormData()
    formData.append('avatar', file)
    const { data } = await api.post('/profile/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    if (data?.status === 'success') {
      me.value.avatar = data.avatar_url
      const storedUser = localStorage.getItem('user')
      if (storedUser) {
        const user = JSON.parse(storedUser)
        user.avatar = data.avatar_url
        localStorage.setItem('user', JSON.stringify(user))
      }
    } else {
      avatarError.value = data?.message || 'Failed to upload avatar'
    }
  } catch (err) {
    avatarError.value = err.response?.data?.message || 'Failed to upload avatar'
  } finally {
    isUploadingAvatar.value = false
    if (fileInput.value) fileInput.value.value = ''
  }
}

const { getPieChartOptions } = useCharts()
const { isDarkMode } = useDarkMode()

const showEmailModal = ref(false)
const showPasswordModal = ref(false)

const newEmail = ref('')
const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

const emailMsg = ref('')
const pwdMsg = ref('')
const loading = ref(false)

async function loadUserScans() {
  try {
    const userId = me.value.id
    if (!userId) return

    const { data } = await api.get(`/user-scans/${userId}`)
    if (data?.status !== 'success') return

    const counts = { Plastic: 0, Cans: 0, Paper: 0, Glass: 0 }

    data.scans.forEach(scan => {
      const type = MATERIAL_MAP[Number(scan.item_type_id)]
      if (type) counts[type]++
    })

    recyclingSummary.value = {
      can: counts.Cans,
      plastic: counts.Plastic,
      paper: counts.Paper,
      glass: counts.Glass,
      total: counts.Cans + counts.Plastic + counts.Paper + counts.Glass
    }

    materialChartData.value = Object.fromEntries(
      Object.entries(counts).map(([key, count]) => [
        key,
        {
          count,
          carbonSaved: count * IMPACT_FACTORS[key].carbon,
          drivingMinutes: count * IMPACT_FACTORS[key].drive
        }
      ])
    )
  } catch {}
}

async function loadMe() {
  try {
    const { data } = await api.get('/profile')
    if (data?.status !== 'success') {
      alert('Failed to load profile. Please log in again.')
      return (window.location.href = '/login')
    }

    me.value = { ...data.user, avatar: data.user.avatar || '' }
    newEmail.value = data.user.email

    await loadUserScans()
  } catch {
    alert('Session expired. Please log in again.')
    window.location.href = '/login'
  }
}

async function updateEmail() {
  if (!newEmail.value) {
    emailMsg.value = 'Please enter a valid email'
    return
  }
  loading.value = true
  try {
    const { data } = await api.put('/profile/email', { email: newEmail.value })
    if (data?.status === 'success') {
      emailMsg.value = 'Email updated successfully'
      me.value.email = newEmail.value
      setTimeout(() => {
        showEmailModal.value = false
        emailMsg.value = ''
      }, 1500)
    } else {
      emailMsg.value = data?.message || 'Failed to update email'
    }
  } catch (err) {
    emailMsg.value = err.response?.data?.message || 'Failed to update email'
  } finally {
    loading.value = false
  }
}

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
      newPassword: newPassword.value
    })
    if (data?.status === 'success') {
      pwdMsg.value = 'Password changed successfully'
      currentPassword.value = ''
      newPassword.value = ''
      confirmPassword.value = ''
      setTimeout(() => {
        showPasswordModal.value = false
        pwdMsg.value = ''
      }, 1500)
    } else {
      pwdMsg.value = data?.message || 'Failed to update password'
    }
  } catch (err) {
    pwdMsg.value = err.response?.data?.message || 'Failed to update password'
  } finally {
    loading.value = false
  }
}

function openEmailModal() {
  emailMsg.value = ''
  showEmailModal.value = true
}

function openPasswordModal() {
  pwdMsg.value = ''
  showPasswordModal.value = true
}

function closeEmailModal() {
  showEmailModal.value = false
  emailMsg.value = ''
}

function closePasswordModal() {
  showPasswordModal.value = false
  pwdMsg.value = ''
}

function logout() {
  window.location.href = '/login'
}

const userInitials = computed(() => {
  if (me.value.name) return me.value.name.split(' ').map(n => n[0]).join('').toUpperCase()
  return me.value.email ? me.value.email[0].toUpperCase() : '?'
})

function materialChart(type) {
  return computed(() => {
    const m = materialChartData.value[type]
    return {
      labels: ['Recycled', 'Remaining'],
      datasets: [{ data: [m.count, Math.max(50 - m.count, 0)] }],
      drivingMinutes: m.drivingMinutes,
      carbonSaved: m.carbonSaved
    }
  })
}

const plasticChartData = materialChart('Plastic')
const glassChartData = materialChart('Glass')
const cansChartData = materialChart('Cans')
const paperChartData = materialChart('Paper')

const chartOptions = computed(() => getPieChartOptions(isDarkMode.value))

onMounted(loadMe)
</script>

<template>
  <div class="container">
    <div class="profile-header">
      <div class="avatar-wrapper" @click="openFilePicker" :class="{ uploading: isUploadingAvatar }">
        <div class="avatar" v-if="!me.avatar">{{ userInitials }}</div>
        <img v-else :src="me.avatar" :alt="me.name" class="avatar-image" />
        <div class="avatar-overlay">
          <i class="fas fa-camera"></i>
        </div>
        <div v-if="isUploadingAvatar" class="upload-spinner">
          <i class="fas fa-spinner fa-spin"></i>
        </div>
      </div>
      <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none" @change="handleAvatarUpload" />
      <div class="user-info">
        <h1>{{ me.name || 'User' }}</h1>
        <p class="user-email">{{ me.email }}</p>
        <p v-if="avatarError" class="avatar-error">{{ avatarError }}</p>
      </div>
    </div>

    <div class="credentials-section">
      <div class="credential-card">
        <div class="credential-header">
          <i class="fas fa-envelope"></i>
          <div class="credential-info">
            <label>Email Address</label>
            <p>{{ me.email }}</p>
          </div>
        </div>
        <button class="edit-btn" @click="openEmailModal">
          <i class="fas fa-edit"></i>
        </button>
      </div>

      <div class="credential-card">
        <div class="credential-header">
          <i class="fas fa-lock"></i>
          <div class="credential-info">
            <label>Password</label>
            <p>••••••••</p>
          </div>
        </div>
        <button class="edit-btn" @click="openPasswordModal">
          <i class="fas fa-edit"></i>
        </button>
      </div>
    </div>

    <div class="stats-section">
      <h2>Your Recycling Impact</h2>
      <div class="stats-grid">
        <div class="stat-card can">
          <i class="fas fa-trash"></i>
          <div class="stat-info">
            <p class="stat-count">{{ recyclingSummary.can }}</p>
            <p class="stat-label">Cans Recycled</p>
          </div>
        </div>

        <div class="stat-card plastic">
          <i class="fas fa-bottle-water"></i>
          <div class="stat-info">
            <p class="stat-count">{{ recyclingSummary.plastic }}</p>
            <p class="stat-label">Plastics Recycled</p>
          </div>
        </div>

        <div class="stat-card paper">
          <i class="fas fa-file-alt"></i>
          <div class="stat-info">
            <p class="stat-count">{{ recyclingSummary.paper }}</p>
            <p class="stat-label">Papers Recycled</p>
          </div>
        </div>

        <div class="stat-card glass">
          <i class="fas fa-wine-glass-alt"></i>
          <div class="stat-info">
            <p class="stat-count">{{ recyclingSummary.glass }}</p>
            <p class="stat-label">Glass Recycled</p>
          </div>
        </div>
      </div>

      <div class="total-stat">
        <div class="total-count">{{ recyclingSummary.total }}</div>
        <div class="total-label">Total Items Recycled</div>
      </div>
    </div>

    <div class="charts-section">
      <h2>Your Carbon Savings by Material</h2>
      <p class="charts-subtitle">See how much carbon you've saved by recycling each material</p>
      <div class="charts-grid">
        <div class="chart-card">
          <div class="chart-title">🥤Plastic</div>
          <ChartComponent
            chartType="doughnut"
            :chartData="plasticChartData.datasets ? { labels: plasticChartData.labels, datasets: plasticChartData.datasets } : {}"
            :chartOptions="chartOptions"
            :stats="{ carbonSaved: plasticChartData.carbonSaved || 0, drivingMinutes: plasticChartData.drivingMinutes }"
          />
        </div>

        <div class="chart-card">
          <div class="chart-title">🍾 Glass</div>
          <ChartComponent
            chartType="doughnut"
            :chartData="glassChartData.datasets ? { labels: glassChartData.labels, datasets: glassChartData.datasets } : {}"
            :chartOptions="chartOptions"
            :stats="{ carbonSaved: glassChartData.carbonSaved || 0, drivingMinutes: glassChartData.drivingMinutes }"
          />
        </div>

        <div class="chart-card">
          <div class="chart-title">Cans</div>
          <ChartComponent
            chartType="doughnut"
            :chartData="cansChartData.datasets ? { labels: cansChartData.labels, datasets: cansChartData.datasets } : {}"
            :chartOptions="chartOptions"
            :stats="{ carbonSaved: cansChartData.carbonSaved || 0, drivingMinutes: cansChartData.drivingMinutes }"
          />
        </div>

        <div class="chart-card">
          <div class="chart-title">Paper</div>
          <ChartComponent
            chartType="doughnut"
            :chartData="paperChartData.datasets ? { labels: paperChartData.labels, datasets: paperChartData.datasets } : {}"
            :chartOptions="chartOptions"
            :stats="{ carbonSaved: paperChartData.carbonSaved || 0, drivingMinutes: paperChartData.drivingMinutes }"
          />
        </div>
      </div>
    </div>

    <button class="logout-btn" @click="logout">
      <i class="fas fa-sign-out-alt"></i> Logout
    </button>

    <div v-if="showEmailModal" class="modal-overlay" @click.self="closeEmailModal">
      <div class="modal">
        <div class="modal-header">
          <h3>Update Email</h3>
          <button class="close-btn" @click="closeEmailModal">×</button>
        </div>
        <div class="modal-body">
          <div class="field-group">
            <label>Current Email</label>
            <input type="email" v-model="me.email" disabled class="disabled-input" />
          </div>
          <div class="field-group">
            <label for="new-email">New Email Address</label>
            <input id="new-email" v-model="newEmail" type="email" placeholder="Enter your new email address" />
          </div>
          <p v-if="emailMsg" class="msg" :class="{ success: emailMsg.includes('success'), error: emailMsg.includes('Failed') }">
            {{ emailMsg }}
          </p>
        </div>
        <div class="modal-footer">
          <button class="cancel-btn" @click="closeEmailModal">Cancel</button>
          <button class="update-btn" :disabled="loading" @click="updateEmail">
            <i class="fas fa-check"></i> Update
          </button>
        </div>
      </div>
    </div>

    <div v-if="showPasswordModal" class="modal-overlay" @click.self="closePasswordModal">
      <div class="modal">
        <div class="modal-header">
          <h3>Update Password</h3>
          <button class="close-btn" @click="closePasswordModal">×</button>
        </div>
        <div class="modal-body">
          <div class="field-group">
            <label for="current-pwd">Current Password</label>
            <div class="password-input-group">
              <input id="current-pwd" v-model="currentPassword" :type="showPassword ? 'text' : 'password'" placeholder="Enter your current password" />
              <button type="button" class="toggle-visibility" @click="showPassword = !showPassword">
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
          </div>

          <div class="field-group">
            <label for="new-pwd">New Password</label>
            <div class="password-input-group">
              <input id="new-pwd" v-model="newPassword" :type="showNewPassword ? 'text' : 'password'" placeholder="Enter your new password" />
              <button type="button" class="toggle-visibility" @click="showNewPassword = !showNewPassword">
                <i :class="showNewPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
          </div>

          <div class="field-group">
            <label for="confirm-pwd">Confirm New Password</label>
            <div class="password-input-group">
              <input id="confirm-pwd" v-model="confirmPassword" :type="showConfirmPassword ? 'text' : 'password'" placeholder="Confirm your new password" />
              <button type="button" class="toggle-visibility" @click="showConfirmPassword = !showConfirmPassword">
                <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
          </div>

          <p v-if="pwdMsg" class="msg" :class="{ success: pwdMsg.includes('success'), error: pwdMsg.includes('Failed') }">
            {{ pwdMsg }}
          </p>
        </div>
        <div class="modal-footer">
          <button class="cancel-btn" @click="closePasswordModal">Cancel</button>
          <button class="update-btn" :disabled="loading" @click="updatePassword">
            <i class="fas fa-check"></i> Update
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.container {
  min-height: calc(100vh - 120px);
  padding: 48px 16px 72px;
  background: var(--bg-primary);
  box-sizing: border-box;
  transition: background-color 0.3s ease;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 32px;
  margin-bottom: 48px;
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
}

.avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--accent-green) 0%, #2E7D32 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 48px;
  font-weight: 800;
  color: white;
  flex-shrink: 0;
  box-shadow: 0 8px 24px rgba(76, 175, 80, 0.3);
}

.avatar-wrapper {
  position: relative;
  cursor: pointer;
  flex-shrink: 0;
}

.avatar-wrapper:hover .avatar-overlay {
  opacity: 1;
}

.avatar-wrapper.uploading {
  pointer-events: none;
  opacity: 0.7;
}

.avatar-image {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  box-shadow: 0 8px 24px rgba(76, 175, 80, 0.3);
}

.avatar-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.avatar-overlay i {
  color: white;
  font-size: 28px;
}

.upload-spinner {
  position: absolute;
  top: 0;
  left: 0;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
}

.upload-spinner i {
  color: white;
  font-size: 32px;
}

.avatar-error {
  color: #dc3545;
  font-size: 13px;
  margin-top: 8px;
}

.user-info h1 {
  font-size: 36px;
  font-weight: 800;
  color: var(--text-primary);
  margin: 0 0 8px;
  letter-spacing: -0.02em;
}

.user-email {
  font-size: 16px;
  color: var(--text-secondary);
  margin: 0;
}

.credentials-section {
  max-width: 900px;
  margin: 0 auto 48px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}

.credential-card {
  background: var(--bg-secondary);
  border: 1.5px solid var(--border-color);
  border-radius: 16px;
  padding: 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: all 0.3s ease;
}

.credential-card:hover {
  border-color: var(--accent-green);
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.1);
}

.credential-header {
  display: flex;
  align-items: center;
  gap: 16px;
  flex: 1;
}

.credential-header i {
  font-size: 24px;
  color: var(--accent-green);
}

.credential-info label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
}

.credential-info p {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: var(--text-primary);
}

.edit-btn {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  border: none;
  background: var(--accent-green);
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.edit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

.stats-section {
  max-width: 900px;
  margin: 0 auto 48px;
}

.stats-section h2 {
  font-size: 24px;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 24px;
  text-align: center;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 32px;
}

.stat-card {
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 16px;
  border: 2px solid transparent;
  transition: all 0.3s ease;
}

.stat-card i {
  font-size: 32px;
  flex-shrink: 0;
}

.stat-card.can {
  border-color: #FF9800;
  background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(255, 152, 0, 0.05) 100%);
}

.stat-card.can i {
  color: #FF9800;
}

.stat-card.plastic {
  border-color: #2196F3;
  background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(33, 150, 243, 0.05) 100%);
}

.stat-card.plastic i {
  color: #2196F3;
}

.stat-card.paper {
  border-color: #8BC34A;
  background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(139, 195, 74, 0.05) 100%);
}

.stat-card.paper i {
  color: #8BC34A;
}

.stat-card.glass {
  border-color: #9C27B0;
  background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(156, 39, 176, 0.05) 100%);
}

.stat-card.glass i {
  color: #9C27B0;
}

.stat-info {
  flex: 1;
}

.stat-count {
  margin: 0;
  font-size: 28px;
  font-weight: 800;
  color: var(--text-primary);
}

.stat-label {
  margin: 4px 0 0;
  font-size: 14px;
  color: var(--text-secondary);
}

.total-stat {
  background: linear-gradient(135deg, var(--accent-green) 0%, #2E7D32 100%);
  border-radius: 16px;
  padding: 32px;
  text-align: center;
  color: white;
  box-shadow: 0 8px 24px rgba(76, 175, 80, 0.3);
}

.total-count {
  font-size: 48px;
  font-weight: 800;
  margin: 0 0 8px;
}

.total-label {
  font-size: 16px;
  font-weight: 600;
  margin: 0;
  opacity: 0.9;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.modal {
  background: var(--bg-secondary);
  border-radius: 20px;
  border: 1.5px solid var(--border-color);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  width: 90%;
  max-width: 500px;
  overflow: hidden;
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px;
  border-bottom: 1px solid var(--border-color);
}

.modal-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 700;
  color: var(--text-primary);
}

.close-btn {
  background: none;
  border: none;
  font-size: 28px;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.close-btn:hover {
  color: var(--text-primary);
  transform: rotate(90deg);
}

.modal-body {
  padding: 24px;
}

.modal-footer {
  display: flex;
  gap: 12px;
  padding: 24px;
  border-top: 1px solid var(--border-color);
}

.field-group {
  margin-bottom: 20px;
}

.field-group:last-child {
  margin-bottom: 0;
}

label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: var(--text-primary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

input {
  width: 100%;
  padding: 12px 16px;
  font-size: 16px;
  border: 1.5px solid var(--border-color);
  border-radius: 10px;
  outline: none;
  box-sizing: border-box;
  transition: all 0.3s ease;
  font-family: inherit;
  background: var(--bg-primary);
  color: var(--text-primary);
}

input:focus {
  border-color: var(--accent-green);
  box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

input::placeholder {
  color: var(--text-secondary);
}

.disabled-input {
  opacity: 0.6;
  cursor: not-allowed;
}

.password-input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.password-input-group input {
  padding-right: 44px;
}

.toggle-visibility {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  color: var(--text-secondary);
  cursor: pointer;
  font-size: 18px;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.toggle-visibility:hover {
  color: var(--accent-green);
}

.msg {
  margin-top: 12px;
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
  animation: slideIn 0.3s ease-out;
}

.msg.success {
  background: rgba(76, 175, 80, 0.15);
  color: var(--accent-green);
  border-left: 3px solid var(--accent-green);
}

.msg.error {
  background: rgba(220, 53, 69, 0.15);
  color: #c82333;
  border-left: 3px solid #dc3545;
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

button {
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  font-family: inherit;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.update-btn,
.cancel-btn {
  flex: 1;
  padding: 12px 24px;
  font-size: 16px;
}

.update-btn {
  background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
  color: white;
}

.update-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

.update-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.cancel-btn {
  background: var(--border-color);
  color: var(--text-primary);
}

.cancel-btn:hover {
  background: var(--accent-green);
  color: white;
}

.logout-btn {
  display: block;
  width: 200px;
  margin: 0 auto;
  padding: 14px 24px;
  font-size: 16px;
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
  color: white;
  text-align: center;
}

.logout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

@media (max-width: 768px) {
  .profile-header {
    flex-direction: column;
    text-align: center;
    gap: 24px;
    margin-bottom: 32px;
  }

  .credentials-section {
    grid-template-columns: 1fr;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .user-info h1 {
    font-size: 28px;
  }

  .total-count {
    font-size: 36px;
  }

  .charts-grid {
    grid-template-columns: 1fr;
  }
}

.charts-section {
  background: white;
  border-radius: 12px;
  padding: 40px;
  margin: 40px 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.dark-mode .charts-section {
  background: #2a2a2a;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.charts-section h2 {
  font-size: 28px;
  color: #1b5e20;
  margin: 0 0 8px 0;
  font-weight: 800;
}

.dark-mode .charts-section h2 {
  color: #81c784;
}

.charts-subtitle {
  font-size: 14px;
  color: #999;
  margin: 0 0 40px 0;
}

.dark-mode .charts-subtitle {
  color: #b0b0b0;
}

.charts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 40px;
}

.chart-card {
  background: #f9f9f9;
  border-radius: 12px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.dark-mode .chart-card {
  background: #1a1a1a;
}

.chart-title {
  font-size: 18px;
  font-weight: 700;
  color: #1b5e20;
  text-align: center;
}

.dark-mode .chart-title {
  color: #81c784;
}

@media (max-width: 768px) {
  .charts-grid {
    grid-template-columns: 1fr;
    gap: 30px;
  }
}
</style>
