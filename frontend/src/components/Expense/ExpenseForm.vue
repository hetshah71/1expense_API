<template>
  <div class="bg-container">
    <h2 class="text-lg font-semibold mb-4">{{ editMode ? 'Edit Expense' : 'Add Expense' }}</h2>
    <form @submit.prevent="handleSubmit">
      <InputField id="expenseName" label="Expense Name" v-model="form.name" placeholder="Expense Name" required />

      <InputField id="expenseAmount" label="Amount" v-model="form.amount" type="number" placeholder="Amount" min="0"
        step="0.01" required />

      <div class="form-group">
        <label for="expenseGroup">Group</label>
        <select id="expenseGroup" v-model="form.group_id" class="form-control" required>
          <option value="" disabled>Select a group</option>
          <option v-for="(group, index) in safeGroups" :key="group ? group.id : `group-${index}`"
            :value="group ? group.id : ''">
            {{ group ? group.name : 'Unnamed Group' }}
          </option>
        </select>
      </div>

      <InputField id="expenseDate" label="Date" v-model="form.date" type="date" required />

      <div class="form-actions">
        <Button :variant="editMode ? 'success' : 'primary'" type="submit">
          {{ editMode ? 'Update' : 'Add' }} Expense
        </Button>
        <Button v-if="editMode" variant="secondary" @click="$emit('cancel')">
          Cancel
        </Button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, onMounted, computed } from 'vue'
import Button from '../Shared/Button.vue'
import InputField from '../Shared/InputField.vue'
import moment from 'moment'

const props = defineProps({
  editMode: {
    type: Boolean,
    default: false
  },
  expenseData: {
    type: Object,
    default: () => ({})
  },
  groups: {
    type: Array,
    required: true,
    default: () => []
  }
})

const emit = defineEmits(['submit', 'cancel'])

const form = ref({
  name: '',
  amount: '',
  group_id: '',
  date: moment().format('YYYY-MM-DD')
})

// Create a computed property to safely handle potentially invalid groups
const safeGroups = computed(() => {
  if (!props.groups) return []
  return props.groups.filter(group => group != null)
})

onMounted(() => {
  if (props.editMode && props.expenseData) {
    // Create a safe copy with defaults for any missing properties
    form.value = {
      name: props.expenseData.name || '',
      amount: props.expenseData.amount || '',
      group_id: props.expenseData.group_id || '',
      date: props.expenseData.date ? moment(props.expenseData.date).format('YYYY-MM-DD') : moment().format('YYYY-MM-DD')
    }
  }
})

const handleSubmit = () => {
  emit('submit', { ...form.value })

  if (!props.editMode) {
    form.value = {
      name: '',
      amount: '',
      group_id: '',
      date: moment().format('YYYY-MM-DD')
    }
  }
}
</script>

<style scoped>
.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-control {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 1rem;
}

.form-control:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

.form-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
}
</style>