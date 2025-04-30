<template>
  <div class="expenses-page">
    <div class="page-header">
      <h1 class="page-title">Manage Your Expenses</h1>
    </div>

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
        <ExpenseForm :groups="expenseStore.groups" @submit="handleExpenseSubmit" />
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
import { ref, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useExpenseStore } from '../stores/expense.js'
import GroupForm from '../components/Group/GroupForm.vue'
import GroupList from '../components/Group/GroupList.vue'
import ExpenseForm from '../components/Expense/ExpenseForm.vue'
import ExpenseList from '../components/Expense/ExpenseList.vue'
import MonthlyExpenses from '../components/Expense/MonthlyExpenses.vue'

const expenseStore = useExpenseStore()
const { groups } = storeToRefs(expenseStore)
const groupListRef = ref(null)
const expenseListRef = ref(null)

const handleGroupSubmit = async (groupData) => {
  try {
    if (groupData.originalName) {
      const groupToEdit = expenseStore.groups.find((g) => g.name === groupData.originalName)
      if (groupToEdit) {
        await expenseStore.editGroup(groupToEdit.id, groupData.originalName, groupData.name)
        await refreshData()
      } else {
        throw new Error('Group not found')
      }
    } else {
      await expenseStore.addGroup(groupData.name)
      await refreshData()
    }
  } catch (error) {
    console.error('Group operation error:', error)
    alert(error.response?.data?.message || error.message || 'Failed to handle group operation')
  }
}

const handleExpenseSubmit = async (expenseData) => {
  try {
    await expenseStore.addExpense(expenseData)
    await refreshData()
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to create expense')
  }
}

const handleGroupEdit = (group) => {
  groupListRef.value.handleEdit(group)
}

const handleGroupDelete = async (groupId) => {
  try {
    await expenseStore.deleteGroup(groupId)
    await refreshData()
  } catch (error) {
    console.error('Delete group error:', error)
    alert(error.response?.data?.message || error.message || 'Failed to delete group')
  }
}

const handleExpenseEdit = ({ expense }) => {
  // Pass the expense object directly, removing the index
  expenseListRef.value.handleEdit({ expense })
}

const handleExpenseDelete = (index) => {
  expenseListRef.value.handleDelete(index)
}

const refreshData = async () => {
  await expenseStore.fetchGroups()
  await expenseStore.fetchExpenses()
}

const handleExport = async () => {
  const result = await expenseStore.exportCsv()
  if (!result.success) {
    alert(result.message)
  }
}

const handleExportPdf = async () => {
  const result = await expenseStore.exportPdf()
  if (!result.success) {
    alert(result.message)
  }
}

onMounted(async () => {
  await expenseStore.fetchGroups()
  await expenseStore.fetchExpenses()
})
</script>

<style scoped>
.expenses-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.export-buttons {
  display: flex;
  gap: 1rem;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
}

.export-button {
  padding: 0.5rem 1rem;
  background-color: #4caf50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s;
}

.export-button:hover {
  background-color: #45a049;
}

.pdf-button {
  background-color: #2196f3;
}

.pdf-button:hover {
  background-color: #1976d2;
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
