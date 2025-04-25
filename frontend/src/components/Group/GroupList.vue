<template>
  <div class="group-list-container">
    <h2 class="text-xl font-semibold mb-4">Groups</h2>

    <div v-if="groups.length === 0" class="empty-state">
      <p>No groups found. Create your first group to get started.</p>
    </div>

    <ul v-else class="group-list">
      <li v-for="group in groups" :key="group.id" class="group-item">
        <div class="group-info">
          <h3 class="group-name">{{ group.name }}</h3>
          <div class="group-meta">
            <span class="group-date">Created: {{ formatDate(group.createdAt) }}</span>
            <span class="group-date">Updated: {{ formatDate(group.updatedAt) }}</span>
          </div>
        </div>

        <div class="group-actions">
          <Button variant="warning" @click="$emit('edit', group)">Edit</Button>
          <Button variant="danger" @click="handleDelete(group)">Delete</Button>
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
      <p class="mb-4">Are you sure you want to delete this group? All expenses in this group will also be deleted.</p>
      <template #footer>
        <Button variant="danger" @click="confirmDelete">Yes, Delete</Button>
        <Button variant="secondary" @click="cancelDelete">Cancel</Button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import GroupForm from './GroupForm.vue';
import Modal from '../Shared/Modal.vue';
import Button from '../Shared/Button.vue';
import { useExpenseStore } from '../../stores/expense.js';
import moment from 'moment';

const props = defineProps({
  groups: {
    type: Array,
    required: true
  }
});

const emit = defineEmits(['refresh','edit']);

const expenseStore = useExpenseStore();
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedGroup = ref(null);
const groupToDelete = ref(null);

const formatDate = (date) => {
  return moment(date).format('MMM D, YYYY HH:mm');
};

const handleEdit = (group) => {
  console.log('Editing group:', group);
  selectedGroup.value = group;
  showEditModal.value = true;
};

const handleUpdate = async ({ name }) => {
  console.log('Updating group:', selectedGroup.value, name);
  if (!selectedGroup.value) return;

  const result = await expenseStore.editGroup(selectedGroup.value.id, selectedGroup.value.name, name);
  if (result.success) {
    showEditModal.value = false;
    selectedGroup.value = null;
    emit('refresh');
  }
};

const cancelEdit = () => {
  showEditModal.value = false;
  selectedGroup.value = null;
};

const handleDelete = (group) => {
  groupToDelete.value = group;
  showDeleteModal.value = true;
};

const confirmDelete = async () => {
  if (!groupToDelete.value) return;

  const result = await expenseStore.deleteGroup(groupToDelete.value.id);
  if (result.success) {
    showDeleteModal.value = false;
    groupToDelete.value = null;
    emit('refresh');
  } else {
    alert(result.message || 'Failed to delete group.');
  }
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  groupToDelete.value = null;
};

// Expose methods to parent
defineExpose({
  handleEdit,
  handleDelete
});
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
