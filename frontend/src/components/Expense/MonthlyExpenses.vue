<template>
  <div class="monthly-expenses">
    <h2 class="section-title">Monthly Summary</h2>
    
    <div class="month-selector">
      <label for="monthSelector">Select Month:</label>
      <input type="month" id="monthSelector" v-model="selectedMonth" class="month-input">
    </div>
    
    <div v-if="monthlyExpenses.length" class="monthly-content">
      <div class="monthly-table">
        <table class="expense-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Amount</th>
              <th>Group</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="expense in monthlyExpenses" :key="expense.name + expense.date">
              <td>{{ expense.name }}</td>
              <td class="amount">₹{{ expense.amount }}</td>
              <td>{{ expense.group.name }}</td>
              <td>{{ formatDate(expense.date) }}</td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="1" class="total-label">Total</td>
              <td colspan="3" class="total-amount">₹{{ monthlyTotal }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    
    <div v-else class="empty-month">
      <p>No expenses found for {{ formatMonthYear(selectedMonth) }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useExpenseStore } from '../../stores/expense.js';
import moment from 'moment';

const expenseStore = useExpenseStore();
const selectedMonth = ref(moment().format('YYYY-MM'));

// Now we use the store's methods directly
const monthlyExpenses = computed(() => {
  return expenseStore.getMonthlyExpenses(selectedMonth.value);
});

const monthlyTotal = computed(() => {
  return expenseStore.getMonthlyTotal(selectedMonth.value);
});

const formatDate = (date) => {
  return moment(date).format('MMM D, YYYY');
};

const formatMonthYear = (monthStr) => {
  return moment(monthStr).format('MMMM YYYY');
};
</script>

<style scoped>
.monthly-expenses {
  background-color: white;
  padding: 1.5rem;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.month-selector {
  display: flex;
  align-items: center;
  margin-bottom: 1.5rem;
}

.month-input {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  margin-left: 0.5rem;
}

.monthly-content {
  margin-top: 1rem;
}

.expense-table {
  width: 100%;
  border-collapse: collapse;
}

.expense-table th,
.expense-table td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.expense-table th {
  font-weight: 600;
  color: #4b5563;
  background-color: #f9fafb;
}

.amount {
  font-weight: 500;
  color: #059669;
}

.total-label {
  font-weight: 600;
  text-align: right;
}

.total-amount {
  font-weight: 600;
  color: #059669;
}

.empty-month {
  padding: 3rem 0;
  text-align: center;
  color: #6b7280;
}
</style>