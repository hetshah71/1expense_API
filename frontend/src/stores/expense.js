import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import moment from 'moment'
import api from '@/services/api.js'

export const useExpenseStore = defineStore('expenses', () => {
  // State
  const groups = ref([])
  const isLoading = ref(false)
  const error = ref(null)
  const expenses = ref([])
  // const groups = ref(JSON.parse(localStorage.getItem('groups')) || [])
  // const expenses = ref(JSON.parse(localStorage.getItem('expenses')) || [])

  // Getters
  const totalExpense = computed(() => {
    return expenses.value.reduce((total, expense) => total + expense.amount, 0)
  })

  const highestExpense = computed(() => {
    if (expenses.value.length === 0) return null
    return [...expenses.value].sort((a, b) => b.amount - a.amount)[0]
  })

  // Functions
  function getMonthlyExpenses(monthStr) {
    return expenses.value.filter((exp) => moment(exp.date).format('YYYY-MM') === monthStr)
  }

  function getMonthlyTotal(monthStr) {
    const monthlyExpenses = getMonthlyExpenses(monthStr)
    return monthlyExpenses.reduce((total, exp) => total + exp.amount, 0)
  }

  // Actions

  async function fetchGroups() {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.get('/groups')
      groups.value = Array.isArray(response.data) ? response.data : []
      return groups.value
    } catch (err) {
      console.error('Error fetching groups:', err)
      error.value = 'Failed to load groups'
      return []
    } finally {
      isLoading.value = false
    }
  }
  async function fetchExpenses() {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.get('/expenses')
      expenses.value = Array.isArray(response.data) ? response.data : []
      return expenses.value
    } catch (err) {
      console.error('Error fetching expenses:', err)
      error.value = 'Failed to load expenses'
      return []
    } finally {
      isLoading.value = false
    }
  }

  async function addGroup(groupData) {
    try {
      const response = await api.post('/groups', { name: groupData }) // remove extra wrapper
      groups.value.push(response.data.group) // Add the new group to the local stat
      return response.data
    } catch (error) {
      console.error('Error adding group:', error.response?.data || error.message)
      throw error
    }
  }

  async function editGroup(groupId, oldName, newName) {
    if (!newName || newName.trim() === '') {
      return { success: false, message: 'Group name cannot be empty' }
    }

    try {
      // console.log('Editing group:', groupId, oldName, newName)
      const response = await api.put(`/groups/${groupId}`, {
        name: newName,
      })

      // console.log('Edit group response:', response.data)
      if (response.data) {
        // Add the updated group to the local state
        const updatedGroup = response.data.group
        // Replace the old group with the updated group in the local array
        const index = groups.value.findIndex((group) => group.id === updatedGroup.id)
        if (index !== -1) {
          groups.value[index] = updatedGroup
        }

        return { success: true }
      } else {
        return { success: false, message: response.data.message }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to update group',
      }
    }
  }

  async function deleteGroup(groupId) {
    try {
      await api.delete(`/groups/${groupId}`)

      groups.value = groups.value.filter((group) => group.id !== groupId)
      expenses.value = expenses.value.filter((exp) => exp.group_id !== groupId)

      return { success: true }
    } catch (error) {
      console.error('Error deleting group:', error.response?.data || error.message)
      return { success: false, message: 'Failed to delete group.' }
    }
  }

  async function addExpense(expenseData) {
    const { name, amount, group_id, date } = expenseData

    if (name.trim() === '' || isNaN(amount) || !group_id || !date) {
      return { success: false, message: 'Please enter valid expense details' }
    }

    try {
      const response = await api.post('/expenses', {
        name,
        amount: parseFloat(amount),
        group_id,
        date,
      })
      console.log('Expense added:', response.data.expense)
      expenses.value.push(response.data.expense) // assuming Laravel returns { expense: {...} }

      // Optional: Update group timestamp if needed
      const groupObj = groups.value.find((g) => g.id === group_id)
      if (groupObj) {
        groupObj.updatedAt = moment().format('YYYY-MM-DD HH:mm:ss')
      }
      return { success: true }
    } catch (error) {
      console.error('Error adding expense:', error.response?.data || error.message)
      return { success: false, message: 'Failed to add expense.' }
    }
  }

  async function editExpense(expenseId, updatedExpense) {
    const { name, amount, group_id, date } = updatedExpense

    if (name.trim() === '' || isNaN(amount) || !group_id || !date) {
      return { success: false, message: 'Please enter valid expense details' }
    }

    try {
      const response = await api.put(`/expenses/${expenseId}`, {
        name,
        amount: parseFloat(amount),
        group_id,
        date,
      })
      console.log('Expense updated:', response.data.expense)
      if (response.data.expense) {
        const updated = response.data.expense

        // ✅ Replace existing expense by ID
        const index = expenses.value.findIndex((e) => e.id === expenseId)
        // console.log(expenses)
        console.log('Expense index:', index)
        if (index !== -1) {
          expenses.value[index] = updatedExpense
        }

        // ✅ Optionally update the group's `updatedAt` field
        const group = groups.value.find((g) => g.id === group_id)
        if (group) {
          group.updatedAt = moment().format('YYYY-MM-DD HH:mm:ss')
        }

        return { success: true }
      } else {
        return {
          success: false,
          message: response.data.message || 'Failed to update expense',
        }
      }
    } catch (error) {
      console.error('API error while editing expense:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to update expense',
      }
    }
  }

  async function deleteExpense(expenseId) {
    try {
      await api.delete(`/expenses/${expenseId}`)

      // Remove from local list
      expenses.value = expenses.value.filter((e) => e.id !== expenseId)

      return { success: true }
    } catch (error) {
      console.error('Error deleting expense:', error.response?.data || error.message)
      return { success: false, message: 'Failed to delete expense.' }
    }
  }

  // Return all the exposed state and functions
  return {
    // State
    groups,
    expenses,
    // Getters
    totalExpense,
    highestExpense,
    isLoading,
    error,
    // Functions
    getMonthlyExpenses,
    getMonthlyTotal,
    // Actions
    addGroup,
    editGroup,
    deleteGroup,
    addExpense,
    editExpense,
    deleteExpense,
    fetchGroups,
    fetchExpenses,
  }
})
