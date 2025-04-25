<template>
  <li class="expense-item">
    <div class="expense-details">
      <div class="expense-name">{{ expense.name }}</div>
      <div class="expense-meta">
        <span class="expense-group">{{ expense.group.name}}</span>
        <span class="expense-date">{{ formatDate(expense.date) }}</span>
      </div>
    </div>
    
    <div class="expense-actions">
      <div class="expense-amount">₹{{ expense.amount }}</div>
      <div class="action-buttons">
        <Button variant="warning" @click="$emit('edit', expense)">Edit</Button>
        <Button variant="danger" @click="$emit('delete', expense.id)">Delete</Button> <!-- Emit expense.id -->
      </div>
    </div>
  </li>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import Button from '../Shared/Button.vue';
import moment from 'moment';

defineProps({
  expense: {
    type: Object,
    required: true
  }
});

defineEmits(['edit', 'delete']);

const formatDate = (date) => {
  return moment(date).format('MMM D, YYYY');
};
</script>

<style scoped>
.expense-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #e2e8f0;
}

.expense-details {
  flex: 1;
}

.expense-name {
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.expense-meta {
  display: flex;
  font-size: 0.875rem;
  color: #6b7280;
  gap: 0.5rem;
}

.expense-group {
  padding: 0.125rem 0.375rem;
  background-color: #e5e7eb;
  border-radius: 0.25rem;
}

.expense-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.expense-amount {
  font-weight: 600;
  color: #059669;
}
</style>
