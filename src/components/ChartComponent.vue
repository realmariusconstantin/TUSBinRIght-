<template>
  <div class="chart-container">
    <div class="chart-stats-side">
      <div class="stat-side">
        <span class="stat-label">Saved</span>
        <span class="stat-value">{{ stats.carbonSaved }} kg CO2</span>
      </div>
      <div v-if="stats.drivingMinutes" class="stat-side">
        <span class="stat-label">Equivalent To</span>
        <span class="stat-value">{{ stats.drivingMinutes }} min</span>
      </div>
    </div>
    <div class="chart-wrapper">
      <canvas ref="chartRef"></canvas>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import Chart from 'chart.js/auto'
import { useDarkMode } from '@/composables/useDarkMode'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  chartType: {
    type: String,
    default: 'bar',
    validator: (value) => ['line', 'bar', 'doughnut', 'pie', 'radar'].includes(value)
  },
  chartData: {
    type: Object,
    required: true
  },
  chartOptions: {
    type: Object,
    default: () => ({})
  },
  stats: {
    type: Object,
    default: null
  }
})

const chartRef = ref(null)
let chartInstance = null
const { isDarkMode } = useDarkMode()

const updateChart = () => {
  if (chartInstance) {
    chartInstance.destroy()
  }

  if (chartRef.value) {
    const ctx = chartRef.value.getContext('2d')
    chartInstance = new Chart(ctx, {
      type: props.chartType,
      data: props.chartData,
      options: props.chartOptions
    })
  }
}

onMounted(() => {
  updateChart()
})

watch([() => props.chartData, () => props.chartOptions, isDarkMode], () => {
  updateChart()
}, { deep: true })
</script>

<style scoped>
.chart-container {
  background: transparent;
  border-radius: 0;
  padding: 0;
  box-shadow: none;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 30px;
}

:global(.dark-mode) .chart-container {
  background: transparent;
  box-shadow: none;
}

.chart-stats-side {
  display: flex;
  flex-direction: column;
  gap: 20px;
  min-width: 120px;
}

.stat-side {
  display: flex;
  flex-direction: column;
  gap: 4px;
  text-align: left;
}

.chart-wrapper {
  position: relative;
  height: 250px;
  width: 250px;
  flex-shrink: 0;
}

.stat-label {
  font-size: 11px;
  color: rgba(76, 175, 80, 0.8);
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 600;
}

:global(.dark-mode) .stat-label {
  color: rgba(129, 199, 132, 0.9);
}

.stat-value {
  font-size: 16px;
  color: #4caf50;
  font-weight: 700;
}

:global(.dark-mode) .stat-value {
  color: #81c784;
}
</style>
