import { ref } from 'vue'
import api from '@/lib/api'

// Cache for community data
const communityDataCache = ref(null)
const communityDataLoading = ref(false)

export function useCharts() {
  // Carbon savings per item (kg CO2) - must match backend values
  const carbonPerItem = {
    plastic: 0.08,  // ~80g CO2 per plastic bottle
    glass: 0.05,    // ~50g CO2 per glass item
    can: 0.06,      // ~60g CO2 per can
    cans: 0.06,
    paper: 0.03     // ~30g CO2 per paper item
  }

  // Convert CO2 to driving minutes (average car emits ~0.21 kg CO2/km)
  const convertCO2ToDrivingMinutes = (kgCO2) => {
    const kmDriven = kgCO2 / 0.21
    const minsAtAveragePace = (kmDriven / 80) * 60 // 80 km/h average
    return Math.round(minsAtAveragePace)
  }

  // Calculate carbon saved from recycling count
  const calculateCarbonSaved = (material, count) => {
    const key = material.toLowerCase()
    return count * (carbonPerItem[key] || 0.05)
  }

  // Fetch community stats from API
  const fetchCommunityStats = async () => {
    if (communityDataCache.value) {
      return communityDataCache.value
    }

    if (communityDataLoading.value) {
      // Wait for existing request
      return new Promise(resolve => {
        const check = setInterval(() => {
          if (!communityDataLoading.value) {
            clearInterval(check)
            resolve(communityDataCache.value)
          }
        }, 100)
      })
    }

    communityDataLoading.value = true
    try {
      const { data } = await api.get('/community-stats')
      if (data?.status === 'success') {
        communityDataCache.value = data.community
      }
      return communityDataCache.value
    } catch (error) {
      console.error('Failed to fetch community stats:', error)
      return null
    } finally {
      communityDataLoading.value = false
    }
  }

  // Clear cache (call when new scan is added)
  const clearCommunityCache = () => {
    communityDataCache.value = null
  }

  // Get user-specific chart data (for profile page)
  // Now accepts user's recycling summary
  const getUserMaterialChart = (material, recyclingSummary = null) => {
    let count = 0
    
    // Get count from recycling summary if available
    if (recyclingSummary) {
      const key = material.toLowerCase() === 'cans' ? 'can' : material.toLowerCase()
      count = recyclingSummary[key] || 0
    }
    
    const carbonSaved = calculateCarbonSaved(material, count)
    const drivingMinutes = convertCO2ToDrivingMinutes(carbonSaved)
    
    // Target is double what they've saved (motivation)
    const target = Math.max(carbonSaved * 2, 1)

    return {
      labels: ['Saved', 'Target'],
      datasets: [
        {
          label: `${material} - Carbon Saved (kg CO2)`,
          data: [carbonSaved, target - carbonSaved],
          backgroundColor: [
            '#4caf50',
            '#e0e0e0'
          ],
          borderColor: [
            '#2e7d32',
            '#999'
          ],
          borderWidth: 2
        }
      ],
      carbonSaved: carbonSaved.toFixed(1),
      drivingMinutes,
      count
    }
  }

  // Get community-wide chart data (for recycling info page)
  const getCommunityMaterialChart = (material, communityData = null) => {
    if (!communityData) {
      // Return empty data while loading
      return {
        labels: ['Saved', 'Target'],
        datasets: [{ data: [0, 1], backgroundColor: ['#4caf50', '#81c784'] }],
        users: 0,
        communityDrivingMinutes: 0,
        carbonSaved: 0
      }
    }

    const key = material.toLowerCase() === 'cans' ? 'can' : material.toLowerCase()
    const materialData = communityData[key] || { saved: 0, users: 0 }
    
    // Target is 150% of what's saved (community goal)
    const target = Math.max(materialData.saved * 1.5, 10)

    return {
      labels: ['Saved', 'Remaining'],
      datasets: [
        {
          label: `${material} - Community Impact (kg CO2)`,
          data: [materialData.saved, Math.max(0, target - materialData.saved)],
          backgroundColor: [
            '#4caf50',
            '#81c784'
          ],
          borderColor: '#2e7d32',
          borderWidth: 2
        }
      ],
      users: materialData.users || 0,
      count: materialData.count || 0,
      carbonSaved: materialData.saved,
      communityDrivingMinutes: convertCO2ToDrivingMinutes(materialData.saved)
    }
  }

  // Chart options with dark mode support
  const getChartOptions = (isDarkMode = false) => ({
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: {
        labels: {
          color: isDarkMode ? '#e0e0e0' : '#333',
          font: {
            size: 14,
            weight: '600'
          },
          padding: 20
        }
      },
      tooltip: {
        backgroundColor: isDarkMode ? '#2a2a2a' : 'rgba(0, 0, 0, 0.8)',
        titleColor: isDarkMode ? '#e0e0e0' : '#fff',
        bodyColor: isDarkMode ? '#d0d0d0' : '#fff',
        padding: 12,
        cornerRadius: 8
      }
    },
    scales: {
      y: {
        ticks: {
          color: isDarkMode ? '#b0b0b0' : '#666'
        },
        grid: {
          color: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
        }
      },
      x: {
        ticks: {
          color: isDarkMode ? '#b0b0b0' : '#666'
        },
        grid: {
          color: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
        }
      }
    }
  })

  // Pie chart options
  const getPieChartOptions = (isDarkMode = false) => ({
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: {
        labels: {
          color: isDarkMode ? '#e0e0e0' : '#333',
          font: {
            size: 14,
            weight: '600'
          },
          padding: 20
        }
      },
      tooltip: {
        backgroundColor: isDarkMode ? '#2a2a2a' : 'rgba(0, 0, 0, 0.8)',
        titleColor: isDarkMode ? '#e0e0e0' : '#fff',
        bodyColor: isDarkMode ? '#d0d0d0' : '#fff',
        padding: 12,
        cornerRadius: 8
      }
    }
  })

  return {
    calculateCarbonSaved,
    convertCO2ToDrivingMinutes,
    fetchCommunityStats,
    clearCommunityCache,
    getUserMaterialChart,
    getCommunityMaterialChart,
    getChartOptions,
    getPieChartOptions
  }
}
