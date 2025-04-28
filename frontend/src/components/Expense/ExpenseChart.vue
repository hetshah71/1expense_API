<template>
  <div class="chart-container">
    <div v-if="groupedExpenses.length > 0" class="chart-wrapper">
      <canvas ref="chartCanvas"></canvas>
    </div>
    <div v-else class="chart-empty">
      <p>No data to display</p>
    </div>
    
    <div v-if="groupedExpenses.length > 0" class="chart-legend">
      <div v-for="(group, index) in groupedExpenses" :key="group.name" class="legend-item">
        <span class="legend-color" :style="{ backgroundColor: colors[index % colors.length] }"></span>
        <span class="legend-label">{{ group.name }} (₹{{ group.total }})</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  expenses: {
    type: Array,
    required: true
  }
});

const chartCanvas = ref(null);
let chartInstance = null;

// Colors for the chart
const colors = [
  '#3B82F6', // blue
  '#10B981', // green
  '#F59E0B', // amber
  '#EF4444', // red
  '#8B5CF6', // purple
  '#EC4899', // pink
  '#6366F1', // indigo
  '#14B8A6', // teal
  '#F97316', // orange
  '#6B7280', // gray
];

// Group expenses by category
const groupedExpenses = computed(() => {
  const groups = {};
  
  props.expenses.forEach(expense => {
    const groupId = expense.group.id;
    if (!groups[groupId]) {
      groups[groupId] = {
        name: expense.group.name,
        total: 0,
        expenses: []
      };
    }
    
    groups[groupId].total += expense.amount;
    groups[groupId].expenses.push(expense);
  });
  
  return Object.values(groups).sort((a, b) => b.total - a.total);
});

// Create and update chart
const createChart = () => {
  if (!chartCanvas.value) return;
  
  // Destroy previous chart if it exists
  if (chartInstance) {
    chartInstance.destroy();
  }
  
  // Data for the chart
  const data = {
    labels: groupedExpenses.value.map(group => group.name),
    datasets: [{
      data: groupedExpenses.value.map(group => group.total),
      backgroundColor: groupedExpenses.value.map((_, index) => colors[index % colors.length]),
      borderWidth: 1
    }]
  };
  
  // Chart configuration
  chartInstance = new Chart(chartCanvas.value, {
    type: 'doughnut',
    data: data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'bottom',
          labels: {
            padding: 20,
            usePointStyle: true,
            pointStyle: 'circle'
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const value = context.raw.toLocaleString('en-IN', {
                style: 'currency',
                currency: 'INR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
              });
              const percentage = ((context.raw / context.dataset.data.reduce((a, b) => a + b, 0)) * 100).toFixed(1);
              return `${context.label}: ${value} (${percentage}%)`;
            }
          }
        }
      },
      cutout: '60%',
      radius: '90%'
    }
  });
};

// Watch for changes in expenses and update chart
watch(() => props.expenses, () => {
  createChart();
}, { deep: true });

// Initialize chart when component is mounted
onMounted(() => {
  createChart();
});
</script>

<style scoped>
.chart-container {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.chart-wrapper {
  height: 300px;
  position: relative;
}

.chart-empty {
  height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  font-style: italic;
}

.chart-legend {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 0.5rem;
}

.legend-item {
  display: flex;
  align-items: center;
  font-size: 0.875rem;
}

.legend-color {
  display: inline-block;
  width: 12px;
  height: 12px;
  border-radius: 2px;
  margin-right: 0.5rem;
}

.legend-label {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>