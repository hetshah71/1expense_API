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
    return parseFloat(
      expenses.value.reduce((total, expense) => total + parseFloat(expense.amount), 0).toFixed(2),
    )
  })

  const currentMonthExpense = computed(() => {
    const currentMonth = moment().format('YYYY-MM')
    return parseFloat(
      expenses.value
        .reduce((total, expense) => {
          return moment(expense.date).format('YYYY-MM') === currentMonth
            ? total + parseFloat(expense.amount)
            : total
        }, 0)
        .toFixed(2),
    )
  })

  const monthlyExpenseSummary = computed(() => {
    const summary = {}
    expenses.value.forEach((expense) => {
      const monthKey = moment(expense.date).format('YYYY-MM')
      if (!summary[monthKey]) {
        summary[monthKey] = {
          total: 0,
          count: 0,
          month: moment(monthKey).format('MMMM YYYY'),
          expenses: [],
        }
      }
      summary[monthKey].total = parseFloat(
        (summary[monthKey].total + parseFloat(expense.amount)).toFixed(2),
      )
      summary[monthKey].count++
      summary[monthKey].expenses.push(expense)
    })
    return Object.values(summary).sort((a, b) =>
      moment(b.month, 'MMMM YYYY').diff(moment(a.month, 'MMMM YYYY')),
    )
  })

  const highestExpense = computed(() => {
    return expenses.value.reduce((highest, expense) => {
      return !highest || expense.amount > highest.amount ? expense : highest
    }, null)
  })

  // Functions
  function getMonthlyExpenses(monthStr) {
    return expenses.value
      .filter((exp) => moment(exp.date).format('YYYY-MM') === monthStr)
      .map((exp) => ({
        ...exp,
        amount: parseFloat(exp.amount),
      }))
      .sort((a, b) => moment(b.date).diff(moment(a.date)))
  }

  function getMonthlyTotal(monthStr) {
    return parseFloat(
      expenses.value
        .reduce((total, expense) => {
          return moment(expense.date).format('YYYY-MM') === monthStr
            ? total + parseFloat(expense.amount)
            : total
        }, 0)
        .toFixed(2),
    )
  }

  // Actions

  async function fetchGroups() {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.get('/groups')
      groups.value = Array.isArray(response.data.data) ? response.data.data : []
      console.log('Fetched groups:', groups.value)
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
      expenses.value = Array.isArray(response.data.data) ? response.data.data : []
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
      // Ensure we're sending the data in the correct format
      const response = await api.post('/groups', {
        name: typeof groupData === 'string' ? groupData : groupData.name,
      })

      // Log the response for debugging
      console.log('Add group response:', response.data)

      // Handle different possible response formats
      let newGroup
      if (response.data.data?.group) {
        newGroup = response.data.data.group
      } else if (response.data.group) {
        newGroup = response.data.group
      } else if (response.data.data) {
        newGroup = response.data.data
      } else if (response.data) {
        newGroup = response.data
      }

      if (newGroup && (newGroup.id || newGroup._id)) {
        groups.value.push(newGroup)
        return newGroup
      } else {
        console.error('Invalid group data:', newGroup)
        throw new Error('Invalid group data received from server')
      }
    } catch (error) {
      console.error('Error adding group:', error.response?.data || error.message)
      throw error
    }
  }

  async function editGroup(groupId, oldName, newName) {
    if (!newName || newName.trim() === '') {
      throw new Error('Group name cannot be empty')
    }

    try {
      const response = await api.put(`/groups/${groupId}`, {
        name: newName,
      })

      // Log the response for debugging
      console.log('Edit group response:', response.data)

      // Handle different possible response formats
      let updatedGroup
      if (response.data.data?.group) {
        updatedGroup = response.data.data.group
      } else if (response.data.group) {
        updatedGroup = response.data.group
      } else if (response.data.data) {
        updatedGroup = response.data.data
      } else if (response.data) {
        updatedGroup = response.data
      }

      if (updatedGroup && (updatedGroup.id || updatedGroup._id)) {
        // Update the group in the local state
        const index = groups.value.findIndex((group) => group.id === groupId)
        if (index !== -1) {
          groups.value[index] = updatedGroup
        }

        // Update group name in related expenses
        expenses.value.forEach((expense) => {
          if (expense.group_id === groupId) {
            expense.group = updatedGroup
          }
        })

        return { success: true, group: updatedGroup }
      } else {
        console.error('Invalid group data:', updatedGroup)
        throw new Error('Invalid group data received from server')
      }
    } catch (error) {
      console.error('Error editing group:', error.response?.data || error.message)
      throw error
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
      throw new Error('Please enter valid expense details')
    }

    try {
      const response = await api.post('/expenses', {
        name,
        amount: parseFloat(amount),
        group_id,
        date,
      })

      // Log the response for debugging
      console.log('Add expense response:', response.data)

      // Handle different possible response formats
      let newExpense
      if (response.data.data?.expense) {
        newExpense = response.data.data.expense
      } else if (response.data.expense) {
        newExpense = response.data.expense
      } else if (response.data.data) {
        newExpense = response.data.data
      } else if (response.data) {
        newExpense = response.data
      }

      if (newExpense && (newExpense.id || newExpense._id)) {
        // Add the group details to the expense object
        const group = groups.value.find((g) => g.id === group_id)
        if (group) {
          newExpense.group = group
        }
        expenses.value.push(newExpense)
        return newExpense
      } else {
        console.error('Invalid expense data:', newExpense)
        throw new Error('Invalid expense data received from server')
      }
    } catch (error) {
      console.error('Error adding expense:', error.response?.data || error.message)
      throw error
    }
  }

  async function editExpense(expenseId, updatedExpense) {
    const { name, amount, group_id, date } = updatedExpense

    if (name.trim() === '' || isNaN(amount) || !group_id || !date) {
      throw new Error('Please enter valid expense details')
    }

    try {
      const response = await api.put(`/expenses/${expenseId}`, {
        name,
        amount: parseFloat(amount),
        group_id,
        date,
      })

      // Log the response for debugging
      console.log('Edit expense response:', response.data)

      // Handle different possible response formats
      let updatedExpenseData
      if (response.data.data?.expense) {
        updatedExpenseData = response.data.data.expense
      } else if (response.data.expense) {
        updatedExpenseData = response.data.expense
      } else if (response.data.data) {
        updatedExpenseData = response.data.data
      } else if (response.data) {
        updatedExpenseData = response.data
      }

      if (updatedExpenseData && (updatedExpenseData.id || updatedExpenseData._id)) {
        // Add the group details to the expense object
        const group = groups.value.find((g) => g.id === group_id)
        if (group) {
          updatedExpenseData.group = group
        }

        // Update the expense in the local state
        const index = expenses.value.findIndex((expense) => expense.id === expenseId)
        if (index !== -1) {
          expenses.value[index] = updatedExpenseData
        }

        return { success: true, expense: updatedExpenseData }
      } else {
        console.error('Invalid expense data:', updatedExpenseData)
        throw new Error('Invalid expense data received from server')
      }
    } catch (error) {
      console.error('Error editing expense:', error.response?.data || error.message)
      throw error
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

  async function exportExpenses() {
    try {
      const response = await api.get('/expenses/export', {
        responseType: 'blob',
      })

      // Create a URL for the blob
      const url = window.URL.createObjectURL(new Blob([response.data.data]))

      // Create a link element
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', 'expenses.csv')

      // Append to body, click and remove
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)

      return { success: true }
    } catch (error) {
      console.error('Error exporting expenses:', error)
      return { success: false, message: 'Failed to export expenses' }
    }
  }

  async function exportPdf() {
    try {
      const response = await api.get('/expenses/export-pdf', {
        responseType: 'blob',
      })

      // Create a URL for the blob
      const url = window.URL.createObjectURL(new Blob([response.data.data]))

      // Create a link element
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', 'expenses.pdf')

      // Append to body, click and remove
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)

      return { success: true }
    } catch (error) {
      console.error('Error exporting PDF:', error)
      return { success: false, message: 'Failed to export PDF' }
    }
  }

  // Return all the exposed state and functions
  return {
    // State
    groups,
    expenses,
    // Getters
    totalExpense,
    currentMonthExpense,
    monthlyExpenseSummary,
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
    exportExpenses,
    exportPdf,
  }
})
