import { ref, watch, onMounted } from 'vue'

// Global reactive state for dark mode
const isDarkMode = ref(false)

export function useDarkMode() {
  // Initialize dark mode on mount
  const initializeDarkMode = () => {
    // Check localStorage for saved preference
    const savedMode = localStorage.getItem('darkMode')
    
    if (savedMode !== null) {
      // Use saved preference
      isDarkMode.value = JSON.parse(savedMode)
    } else {
      // Check system preference
      isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }
    
    // Apply the theme
    applyTheme(isDarkMode.value)
  }

  // Apply theme to document
  const applyTheme = (dark) => {
    if (dark) {
      document.documentElement.setAttribute('data-theme', 'dark')
      document.body.classList.add('dark-mode')
    } else {
      document.documentElement.setAttribute('data-theme', 'light')
      document.body.classList.remove('dark-mode')
    }
  }

  // Toggle dark mode
  const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value
    localStorage.setItem('darkMode', JSON.stringify(isDarkMode.value))
    applyTheme(isDarkMode.value)
    console.log(`✅ Dark mode ${isDarkMode.value ? 'enabled' : 'disabled'}`)
  }

  // Watch for changes and update localStorage
  watch(isDarkMode, (newValue) => {
    localStorage.setItem('darkMode', JSON.stringify(newValue))
    applyTheme(newValue)
  })

  return {
    isDarkMode,
    toggleDarkMode,
    initializeDarkMode
  }
}
