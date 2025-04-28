<template>
  <div class="dashboard">
    <h1 class="dashboard-title">Expense Tracker Dashboard</h1>

    <div class="dashboard-stats">
      <div class="stat-card">
        <h3 class="stat-title">Total Expenses</h3>
        <p class="stat-value">₹{{ expenseStore.totalExpense }}</p>
      </div>

      <div class="stat-card">
        <h3 class="stat-title">Highest Expense</h3>
        <p v-if="expenseStore.highestExpense" class="stat-value">
          ₹{{ expenseStore.highestExpense.amount }}
          <span class="stat-label">{{ expenseStore.highestExpense.name }}</span>
        </p>
        <p v-else class="stat-empty">No expenses yet</p>
      </div>

      <div class="stat-card">
        <h3 class="stat-title">Total Groups</h3>
        <p class="stat-value">{{ expenseStore.groups.length }}</p>
      </div>

      <div class="stat-card">
        <h3 class="stat-title">This Month</h3>
        <p class="stat-value">₹{{ currentMonthTotal }}</p>
      </div>
    </div>

    <div class="dashboard-monthly">
      <h2 class="section-title">Monthly Summary</h2>

      <div class="monthly-selector">
        <label for="monthSelector">Select Month:</label>
        <input type="month" id="monthSelector" v-model="selectedMonth" class="month-input" />
      </div>

      <div v-if="monthlyExpenses.length" class="monthly-breakdown">
        <div class="monthly-chart">
          <ExpenseChart :expenses="monthlyExpenses" />
        </div>

        <div class="monthly-list">
          <h3 class="subsection-title">Expenses for {{ formatMonthYear(selectedMonth) }}</h3>
          <ul class="expense-brief-list">
            <li v-for="(expense, index) in monthlyExpenses" :key="index" class="expense-brief-item">
              <span class="expense-brief-name">{{ expense.name }}</span>
              <span class="expense-brief-amount">₹{{ expense.amount }}</span>
            </li>
          </ul>
          <div class="monthly-total">
            <p>
              Total: <strong>₹{{ monthlyTotal }}</strong>
            </p>
          </div>
        </div>
      </div>

      <div v-else class="empty-month">
        <p>No expenses found for {{ formatMonthYear(selectedMonth) }}</p>
      </div>
    </div>

    <div class="dashboard-actions">
      <Button @click="handleExportToPDF" class="action-button">Export PDF</Button>
      <Button @click="handleExportToCSV" class="action-button">Export CSV</Button>
      <Button @click="$router.push('/expenses')" class="action-button">Manage Expenses</Button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useExpenseStore } from '../stores/expense.js'
import ExpenseChart from '../components/Expense/ExpenseChart.vue'
import Button from '../components/Shared/Button.vue'
import moment from 'moment'
import { exportToPDF, exportToCSV } from '../utils/exportUtils'

const expenseStore = useExpenseStore()
const selectedMonth = ref(moment().format('YYYY-MM'))

const monthlyExpenses = computed(() => expenseStore.getMonthlyExpenses(selectedMonth.value))
const monthlyTotal = computed(() => expenseStore.getMonthlyTotal(selectedMonth.value))

const handleExportToPDF = () => {
  exportToPDF(monthlyExpenses.value, selectedMonth.value)
}

const handleExportToCSV = () => {
  exportToCSV(monthlyExpenses.value, selectedMonth.value)
}

const currentMonthTotal = computed(() => {
  const currentMonth = moment().format('YYYY-MM')
  return expenseStore.getMonthlyTotal(currentMonth)
})

const formatMonthYear = (monthStr) => {
  return moment(monthStr).format('MMMM YYYY')
}

onMounted(() => {
  selectedMonth.value = moment().format('YYYY-MM')
})
</script>

<style scoped>
.dashboard {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.dashboard-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
  color: #1f2937;
}

.dashboard-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}

.stat-card {
  background-color: white;
  padding: 1.5rem;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stat-title {
  font-size: 1rem;
  color: #6b7280;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
}

.stat-label {
  display: block;
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: normal;
}

.stat-empty {
  font-size: 1rem;
  color: #9ca3af;
  font-style: italic;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.dashboard-monthly {
  background-color: white;
  padding: 1.5rem;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.monthly-selector {
  margin-bottom: 1.5rem;
}

.month-input {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  margin-left: 0.5rem;
}

.monthly-breakdown {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.subsection-title {
  font-size: 1.125rem;
  font-weight: 500;
  margin-bottom: 1rem;
  color: #1f2937;
}

.expense-brief-list {
  list-style: none;
  padding: 0;
  margin: 0;
  max-height: 300px;
  overflow-y: auto;
}

.expense-brief-item {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid #e5e7eb;
}

.expense-brief-name {
  font-weight: 500;
}

.expense-brief-amount {
  color: #059669;
  font-weight: 500;
}

.monthly-total {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 2px solid #e5e7eb;
  text-align: right;
}

.empty-month {
  padding: 3rem 0;
  text-align: center;
  color: #6b7280;
}

.dashboard-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.action-button {
  min-width: 120px;
}

@media (max-width: 768px) {
  .monthly-breakdown {
    grid-template-columns: 1fr;
  }

  .dashboard-stats {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }
}
</style>
