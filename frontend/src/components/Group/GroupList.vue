<template>
  <div class="group-list-container">
    <h2 class="text-xl font-semibold mb-4">Groups</h2>

    <div v-if="!groups || groups.length === 0" class="empty-state">
      <p>No groups found. Create your first group to get started.</p>
    </div>

    <ul v-else class="group-list">
      <li
        v-for="(group, index) in groups"
        :key="group && group.id ? group.id : `group-${index}`"
        class="group-item"
      >
        <div class="group-info">
          <h3 class="group-name">{{ group.name }}</h3>
          <div class="group-meta">
            <span class="group-date"
              >Created:
              {{ formatDate(group && group.createdAt ? group.createdAt : new Date()) }}</span
            >
            <span class="group-date"
              >Updated:
              {{ formatDate(group && group.updatedAt ? group.updatedAt : new Date()) }}</span
            >
          </div>
        </div>

        <div class="group-actions">
          <Button variant="warning" @click="group && group.id ? $emit('edit', group) : null"
            >Edit</Button
          >
          <Button variant="danger" @click="group && group.id ? handleDelete(group) : null"
            >Delete</Button
          >
        </div>
      </li>
    </ul>

    <Modal v-model="showEditModal" title="Edit Group">
      <GroupForm
        v-if="selectedGroup"
        :edit-mode="true"
        :group-data="selectedGroup"
        @submit="handleUpdate"
        @cancel="cancelEdit"
      />
    </Modal>

    <Modal v-model="showDeleteModal" title="Confirm Deletion">
      <p class="mb-4">
        Are you sure you want to delete this group? All expenses in this group will also be deleted.
      </p>
      <template #footer>
        <Button variant="danger" @click="confirmDelete">Yes, Delete</Button>
        <Button variant="secondary" @click="cancelDelete">Cancel</Button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue'
import GroupForm from './GroupForm.vue'
import Modal from '../Shared/Modal.vue'
import Button from '../Shared/Button.vue'
import { useExpenseStore } from '../../stores/expense.js'
import moment from 'moment'

const props = defineProps({
  groups: {
    type: Array,
    required: true,
    default: () => [],
  },
})

const emit = defineEmits(['refresh', 'edit'])

const expenseStore = useExpenseStore()
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selectedGroup = ref(null)
const groupToDelete = ref(null)

const formatDate = (date) => {
  return moment(date).format('MMM D, YYYY HH:mm')
}

const handleEdit = (group) => {
  if (!group || !group.id) return

  console.log('Editing group:', group)
  selectedGroup.value = group
  showEditModal.value = true
}

const handleUpdate = async ({ name }) => {
  console.log('Updating group:', selectedGroup.value, name)
  if (!selectedGroup.value || !selectedGroup.value.id) return

  try {
    const result = await expenseStore.editGroup(
      selectedGroup.value.id,
      selectedGroup.value.name,
      name,
    )
    showEditModal.value = false
    selectedGroup.value = null
    emit('refresh')
  } catch (error) {
    console.error('Error updating group:', error)
    alert(error.response?.data?.message || error.message || 'Failed to update group')
  }
}

const cancelEdit = () => {
  showEditModal.value = false
  selectedGroup.value = null
}

const handleDelete = (group) => {
  if (!group || !group.id) return

  groupToDelete.value = group
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  if (!groupToDelete.value || !groupToDelete.value.id) return

  const result = await expenseStore.deleteGroup(groupToDelete.value.id)
  if (result.success) {
    showDeleteModal.value = false
    groupToDelete.value = null
    emit('refresh')
  } else {
    alert(result.message || 'Failed to delete group.')
  }
}

const cancelDelete = () => {
  showDeleteModal.value = false
  groupToDelete.value = null
}

// Expose methods to parent
defineExpose({
  handleEdit,
  handleDelete,
})
</script>

<style scoped>
.group-list-container {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}

.group-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.group-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #e2e8f0;
}

.group-item:last-child {
  border-bottom: none;
}

.group-name {
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.group-meta {
  display: flex;
  font-size: 0.75rem;
  color: #6b7280;
  gap: 1rem;
}

.group-actions {
  display: flex;
  gap: 0.5rem;
}

.empty-state {
  text-align: center;
  padding: 2rem 0;
  color: #6b7280;
}
</style>
