<template>
  <div class="expense-list-container">
    <h2 class="text-xl font-semibold mb-4">Expenses</h2>
    
    <div v-if="expenses.length === 0" class="empty-state">
      <p>No expenses found.</p>
    </div>
    
    <ul v-else class="expense-list">
      <ExpenseItem
        v-for="(expense, index) in expenses"
        :key="expense.id"
        :expense="expense"
        @edit="handleEdit({ expense, index })"
        @delete="handleDelete(expense.id)" 
      />
    </ul>
    
    <Modal v-model="showEditModal" title="Edit Expense">
      <ExpenseForm
        v-if="selectedExpense"
        :edit-mode="true"
        :expense-data="selectedExpense.expense"
        :groups="groups"
        @submit="handleUpdate"
        @cancel="cancelEdit"
      />
    </Modal>
    
    <Modal v-model="showDeleteModal" title="Confirm Deletion">
      <p class="mb-4">Are you sure you want to delete this expense?</p>
      <template #footer>
        <Button variant="danger" @click="confirmDelete">Delete</Button>
        <Button variant="secondary" @click="cancelDelete">Cancel</Button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import ExpenseItem from './ExpenseItem.vue';
import ExpenseForm from './ExpenseForm.vue';
import Modal from '../Shared/Modal.vue';
import Button from '../Shared/Button.vue';
import { useExpenseStore } from '../../stores/expense.js';

const props = defineProps({
  expenses: {
    type: Array,
    required: true
  },
  groups: {
    type: Array,
    required: true
  }
});

const emit = defineEmits(['refresh','edit']);

const expenseStore = useExpenseStore();
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedExpense = ref(null);
const selectedIndex = ref(null);

const handleEdit = ({ expense, index }) => {
  console.log('Editing expense:', expense, 'at index:', index);
  selectedExpense.value = { expense, index };
  showEditModal.value = true;
};

const handleUpdate = async (updatedExpense) => {

  const result = await expenseStore.editExpense(selectedExpense.value.expense.id, updatedExpense);

  if (result.success) {
    showEditModal.value = false;
    selectedExpense.value = null;
    emit('refresh');
  }
};

const cancelEdit = () => {
  showEditModal.value = false;
  selectedExpense.value = null;
};

const handleDelete = (expenseId) => {
  selectedIndex.value = expenseId;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  expenseStore.deleteExpense(selectedIndex.value);  // Ensure deleteExpense works in your store
  showDeleteModal.value = false;
  selectedIndex.value = null;
  emit('refresh');
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  selectedIndex.value = null;
};

// Expose functions to parent
defineExpose({
  handleEdit,
  handleDelete
});
</script>
