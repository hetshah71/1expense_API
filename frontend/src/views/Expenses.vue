<template>
  <div class="expenses-page">
    <h1 class="page-title">Manage Your Expenses</h1>
    
    <div class="grid-container">
      <!-- Row 1: Add Group and Groups List -->
      <div class="grid-item">
        <GroupForm @submit="handleGroupSubmit" />
      </div>
      
      <div class="grid-item">
        <GroupList 
          :groups="expenseStore.groups" 
          @refresh="refreshData"
          @edit="handleGroupEdit"
          @delete="handleGroupDelete"
          ref="groupListRef"
        />
      </div>
      
      <!-- Row 2: Add Expense and Expenses List -->
      <div class="grid-item">
        <ExpenseForm 
          :groups="expenseStore.groups"
          @submit="handleExpenseSubmit"
        />
      </div>
      
      <div class="grid-item">
        <ExpenseList 
          :expenses="expenseStore.expenses" 
          :groups="expenseStore.groups"
          @refresh="refreshData"
          @edit="handleExpenseEdit"
          @delete="handleExpenseDelete"
          ref="expenseListRef"
        />
      </div>
      
      <!-- Row 3: Monthly Summary -->
      <div class="grid-item full-width">
        <MonthlyExpenses />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useExpenseStore } from '../stores/expense.js';
import GroupForm from '../components/Group/GroupForm.vue';
import GroupList from '../components/Group/GroupList.vue';
import ExpenseForm from '../components/Expense/ExpenseForm.vue';
import ExpenseList from '../components/Expense/ExpenseList.vue';
import MonthlyExpenses from '../components/Expense/MonthlyExpenses.vue';

const expenseStore = useExpenseStore();
const groupListRef = ref(null);
const expenseListRef = ref(null);

const handleGroupSubmit = (groupData) => {
  // Check if we're editing or adding
  if (groupData.originalName) {
    const result = expenseStore.updateGroup(groupData.originalName, groupData.name);
  } else {
    const result = expenseStore.addGroup(groupData.name);
  }
};

const handleExpenseSubmit = (expenseData) => {
  const result = expenseStore.addExpense(expenseData);

};

const handleGroupEdit = (group) => {
 
  groupListRef.value.handleEdit(group);
};

const handleGroupDelete = async (groupId) => {
  const result = await expenseStore.deleteGroup(groupId);
  if (!result.success) {
    alert(result.message);
  }
};

// const handleGroupDelete = (groupName) => {
//   groupListRef.value.handleDelete(groupName);
// };

const handleExpenseEdit = ({ expense }) => {
  // Pass the expense object directly, removing the index
  expenseListRef.value.handleEdit({ expense });
};


const handleExpenseDelete = (index) => {
  expenseListRef.value.handleDelete(index);
};

const refreshData = () => {
  // Placeholder
};

onMounted(() => {
  expenseStore.fetchGroups();
  expenseStore.fetchExpenses();
});
</script>

<style scoped>
.expenses-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
  color: #1f2937;
}

.grid-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.grid-item {
  width: 100%;
}

.full-width {
  grid-column: span 2;
}

@media (max-width: 768px) {
  .grid-container {
    grid-template-columns: 1fr;
  }
  
  .full-width {
    grid-column: 1;
  }
}
</style>
