<script setup>
import { ref, computed, watch } from 'vue'
import api from '@/lib/api'

const props = defineProps({
  password: {
    type: String,
    default: ''
  },
  showDetails: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:password', 'strength-change'])

const loading = ref(false)
const strengthData = ref({
  valid: false,
  score: 0,
  strength: 'weak',
  message: 'Too weak',
  requirements: {
    length: false,
    uppercase: false,
    lowercase: false,
    numbers: false,
    special: false
  },
  needed: {}
})

// Watch password and validate in real-time
watch(() => props.password, async (newPassword) => {
  if (!newPassword) {
    strengthData.value = {
      valid: false,
      score: 0,
      strength: 'weak',
      message: 'Enter a password',
      requirements: {
        length: false,
        uppercase: false,
        lowercase: false,
        numbers: false,
        special: false
      },
      needed: {}
    }
    emit('strength-change', strengthData.value)
    return
  }

  loading.value = true
  try {
    const { data } = await api.get('/api/password-strength', {
      params: { password: newPassword }
    })
    strengthData.value = data
    emit('strength-change', strengthData.value)
  } catch (err) {
    console.error('Failed to validate password strength:', err)
  } finally {
    loading.value = false
  }
}, { immediate: true })

const strengthColor = computed(() => {
  const score = strengthData.value.score
  if (score < 40) return '#dc3545' // Red
  if (score < 60) return '#ff9800' // Orange
  if (score < 80) return '#ffc107' // Yellow
  return '#4CAF50' // Green
})

const strengthLabel = computed(() => {
  const strength = strengthData.value.strength
  if (strength === 'weak') return 'Weak'
  if (strength === 'fair') return 'Fair'
  if (strength === 'good') return 'Good'
  if (strength === 'strong') return 'Strong'
  return 'Unknown'
})

const requirementsList = [
  { key: 'length', label: 'At least 8 characters' },
  { key: 'uppercase', label: 'Uppercase letter (A-Z)' },
  { key: 'lowercase', label: 'Lowercase letter (a-z)' },
  { key: 'numbers', label: 'Number (0-9)' },
  { key: 'special', label: 'Special character (!@#$%)' }
]
</script>

<template>
  <div class="password-strength-meter">
    <!-- Strength Bar -->
    <div class="strength-bar-container">
      <div class="strength-bar" :style="{ width: strengthData.score + '%', backgroundColor: strengthColor }"></div>
    </div>

    <!-- Strength Label -->
    <div class="strength-label">
      <span>{{ strengthLabel }}</span>
      <span class="score">({{ strengthData.score }}/100)</span>
    </div>

    <!-- Requirements Checklist -->
    <div v-if="showDetails" class="requirements">
      <p class="requirements-title">Password Requirements:</p>
      <div class="requirements-list">
        <div v-for="req in requirementsList" :key="req.key" class="requirement-item">
          <i :class="strengthData.requirements[req.key] ? 'fas fa-check' : 'fas fa-times'"></i>
          <span>{{ req.label }}</span>
        </div>
      </div>
    </div>

    <!-- Strength Message -->
    <p v-if="password" class="strength-message">{{ strengthData.message }}</p>
  </div>
</template>

<style scoped>
.password-strength-meter {
  width: 100%;
  margin-bottom: 16px;
}

.strength-bar-container {
  width: 100%;
  height: 8px;
  background: var(--border-color);
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 12px;
}

.strength-bar {
  height: 100%;
  transition: width 0.3s ease, background-color 0.3s ease;
}

.strength-label {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
}

.strength-label .score {
  color: var(--text-secondary);
  font-weight: 500;
}

.requirements {
  background: rgba(76, 175, 80, 0.05);
  border-left: 3px solid var(--accent-green);
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 12px;
}

.requirements-title {
  margin: 0 0 8px;
  font-size: 12px;
  font-weight: 600;
  color: var(--text-primary);
  text-transform: uppercase;
}

.requirements-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.requirement-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--text-secondary);
}

.requirement-item i {
  width: 16px;
  text-align: center;
  font-size: 12px;
}

.requirement-item i.fa-check {
  color: var(--accent-green);
}

.requirement-item i.fa-times {
  color: #dc3545;
}

.strength-message {
  margin: 0;
  padding: 8px 12px;
  background: rgba(76, 175, 80, 0.1);
  border-radius: 6px;
  font-size: 13px;
  color: var(--text-secondary);
  text-align: center;
}
</style>
