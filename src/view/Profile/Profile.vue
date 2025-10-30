<template>
  <section class="profile-wrap">
    <div class="profile-card">
      <h1 class="profile-title">Your Profile</h1>

      <!-- ✅ Read-only user summary -->
      <div class="info-card">
        <div class="info-row">
          <span class="info-label">User ID</span>
          <span class="info-value">#{{ userId }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Name</span>
          <span class="info-value">{{ name || '—' }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Email</span>
          <span class="info-value">{{ email || '—' }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Role</span>
          <span class="role-badge">{{ role || '—' }}</span>
        </div>
      </div>

      <div v-if="message" :class="['alert', messageType]">{{ message }}</div>

      <!-- Account edit -->
      <div class="field-group">
        <label>
          <span>Name</span>
          <input v-model.trim="name" type="text" placeholder="Your name" />
        </label>
        <label>
          <span>Email</span>
          <input v-model.trim="email" type="email" placeholder="you@example.com" />
        </label>
        <div class="row-actions">
          <button class="btn btn-outline" type="button" @click="goBack">Back</button>
          <button class="btn btn-primary" :disabled="saving" @click="saveProfile">
            {{ saving ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </div>

      <div class="divider"></div>

      <!-- Password -->
      <h2 class="section-title">Change Password</h2>
      <div class="field-group">
        <label>
          <span>Current Password</span>
          <input v-model="currentPassword" type="password" autocomplete="current-password" />
        </label>
        <label>
          <span>New Password</span>
          <input v-model="newPassword" type="password" autocomplete="new-password" />
        </label>
        <label>
          <span>Confirm New Password</span>
          <input v-model="confirmPassword" type="password" autocomplete="new-password" />
        </label>

        <div class="row-actions">
          <button class="btn" :disabled="changingPw" @click="changePassword">
            {{ changingPw ? 'Updating...' : 'Update Password' }}
          </button>
        </div>
      </div>

      <button class="btn btn-danger full-width" @click="logoutNow">Log out</button>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/lib/api'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const { user, refreshUser, logout } = useAuth()

// ✅ New reactive fields for summary
const userId = ref(null)
const role = ref('')

const name = ref('')
const email = ref('')

const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')

const saving = ref(false)
const changingPw = ref(false)
const message = ref('')
const messageType = ref('success')

function toast(text, type = 'success') {
  message.value = text
  messageType.value = type
  setTimeout(() => (message.value = ''), 3500)
}

onMounted(async () => {
  try {
    const { data } = await api.get('/api/me') // expects { status, user: { id, name, email, role } }
    const u = data?.user || data
    userId.value = u?.id ?? user.value?.id ?? null
    name.value = u?.name ?? user.value?.name ?? ''
    email.value = u?.email ?? user.value?.email ?? ''
    role.value = u?.role ?? user.value?.role ?? ''
  } catch (e) {
    toast(e?.response?.data?.message || 'Failed to load profile.', 'error')
  }
})

async function saveProfile() {
  if (!email.value) return toast('Email is required.', 'error')
  saving.value = true
  try {
    await api.put('/api/me/email', { email: email.value, name: name.value })
    await refreshUser()
    toast('Profile updated successfully.')
  } catch (e) {
    toast(e?.response?.data?.message || 'Could not update profile.', 'error')
  } finally {
    saving.value = false
  }
}

async function changePassword() {
  if (!currentPassword.value || !newPassword.value)
    return toast('Enter current and new password.', 'error')
  if (newPassword.value !== confirmPassword.value)
    return toast('New passwords do not match.', 'error')

  changingPw.value = true
  try {
    await api.put('/api/me/password', {
      currentPassword: currentPassword.value,
      newPassword: newPassword.value
    })
    currentPassword.value = ''
    newPassword.value = ''
    confirmPassword.value = ''
    toast('Password updated.')
  } catch (e) {
    toast(e?.response?.data?.message || 'Could not update password.', 'error')
  } finally {
    changingPw.value = false
  }
}

function goBack() {
  router.back()
}

async function logoutNow() {
  try { await logout() } finally { router.push('/login') }
}
</script>

<style scoped>
/* Page background to match login */
.profile-wrap {
  min-height: 100vh;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 64px 16px;
  background:
      radial-gradient(1200px 400px at 50% 0%, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0) 60%),
      linear-gradient(180deg, #f7f7f7 0%, #f9fbff 100%);
}

/* Center card */
.profile-card {
  width: 100%;
  max-width: 620px;
  background: #fff;
  border: 1px solid rgba(0,0,0,0.08);
  box-shadow: 0 18px 38px rgba(16, 24, 40, 0.12);
  border-radius: 18px;
  padding: 28px;
}

.profile-title {
  text-align: center;
  font-size: 36px;
  font-weight: 800;
  letter-spacing: 0.2px;
  color: #1f2937;
  margin: 8px 0 18px;
}

/* ✅ Read-only summary card */
.info-card {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px 16px;
  background: #fafafa;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 12px 14px;
  margin-bottom: 12px;
}
.info-row { display: flex; align-items: center; justify-content: space-between; }
.info-label { color: #6b7280; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
.info-value { color: #111827; font-size: 14px; font-weight: 700; }
.role-badge {
  display: inline-block;
  font-size: 12px;
  font-weight: 800;
  padding: 4px 8px;
  border-radius: 8px;
  background: #eef2ff;
  color: #3730a3;
  border: 1px solid #c7d2fe;
}

.section-title { font-size: 18px; font-weight: 800; color: #111827; margin: 6px 0 10px; }

.field-group {
  display: grid;
  grid-template-columns: 1fr;
  gap: 14px;
  margin-bottom: 8px;
}

label span {
  display: block;
  font-size: 13px;
  font-weight: 700;
  color: #6b7280;
  margin-bottom: 6px;
}

input {
  width: 100%;
  padding: 12px 14px;
  background: #fbfbfb;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  font-size: 14px;
  outline: none;
  transition: box-shadow .2s ease, border-color .2s ease, background .2s ease;
}
input:focus { background: #ffffff; border-color: #111827; box-shadow: 0 0 0 3px rgba(17,24,39,0.08); }

/* Buttons — smaller & rectangular */
.btn {
  border-radius: 8px;
  padding: 8px 16px;
  border: 1.8px solid #111827;
  background: #ffffff;
  color: #111827;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.15s ease-in-out;
}
.btn:hover { background: #111827; color: #ffffff; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.12); }
.btn:active { transform: translateY(0); box-shadow: none; }
.btn-primary { background: #111827; color: #fff; }
.btn-outline { background: #fff; color: #111827; }
.btn-danger { background: #dc2626; color: #fff; border-color: #b91c1c; }
.logout-btn, .full-width { width: 100%; margin-top: 10px; border-radius: 8px; }

.row-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 8px; }

.alert {
  width: 100%;
  margin: 6px 0 14px;
  padding: 10px 14px;
  border-radius: 12px;
  font-weight: 700;
  border: 1px solid transparent;
  text-align: center;
}
.alert.success { background: #ecfdf5; color: #065f46; border-color: #34d399; }
.alert.error   { background: #fef2f2; color: #991b1b; border-color: #fca5a5; }

.divider { height: 1px; background: linear-gradient(90deg, rgba(0,0,0,0), rgba(0,0,0,.08), rgba(0,0,0,0)); margin: 18px 0 10px; }

@media (max-width: 640px) {
  .profile-card { padding: 22px; }
  .profile-title { font-size: 30px; }
  .info-card { grid-template-columns: 1fr; }
  .row-actions { flex-direction: column; align-items: stretch; }
  .row-actions .btn { width: 100%; }
}
</style>
