import { ref } from 'vue'

export function useCharts() {
  // Carbon savings data (in kg CO2)
  const getCarbonSavings = () => ({
    plastic: 12.5,
    glass: 8.3,
    cans: 15.7,
    paper: 20.1
  })

  // Convert CO2 to driving minutes (average car emits ~0.21 kg CO2/km)
  const convertCO2ToDrivingMinutes = (kgCO2) => {
    const kmDriven = kgCO2 / 0.21
    const minsAtAveragePace = (kmDriven / 80) * 60 // 80 km/h average
    return Math.round(minsAtAveragePace)
  }

  // Get user-specific chart data (for profile page)
  const getUserMaterialChart = (material) => {
    const data = getCarbonSavings()
    const carbonSaved = data[material.toLowerCase()] || 0
    const drivingMinutes = convertCO2ToDrivingMinutes(carbonSaved)

    return {
      labels: [material, 'Target'],
      datasets: [
        {
          label: 'Carbon Saved (kg CO2)',
          data: [carbonSaved, carbonSaved * 2], // Double as target
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
      drivingMinutes
    }
  }

  // Get community-wide chart data (for recycling info page)
  const getCommunityMaterialChart = (material) => {
    const communityData = {
      plastic: { saved: 450, target: 600, users: 156 },
      glass: { saved: 320, target: 500, users: 98 },
      cans: { saved: 580, target: 700, users: 234 },
      paper: { saved: 720, target: 900, users: 187 }
    }

    const material_data = communityData[material.toLowerCase()] || {}

    return {
      labels: ['Saved', 'Remaining to Target'],
      datasets: [
        {
          label: `${material} - Community Impact (kg CO2)`,
          data: [material_data.saved, material_data.target - material_data.saved],
          backgroundColor: [
            '#4caf50',
            '#81c784'
          ],
          borderColor: '#2e7d32',
          borderWidth: 2
        }
      ],
      users: material_data.users,
      communityDrivingMinutes: convertCO2ToDrivingMinutes(material_data.saved)
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
    getCarbonSavings,
    convertCO2ToDrivingMinutes,
    getUserMaterialChart,
    getCommunityMaterialChart,
    getChartOptions,
    getPieChartOptions
  }
}
