<template>
  <div class="group-form">
    <h2 class="text-lg font-semibold mb-4">{{ editMode ? 'Edit Group' : 'Add Group' }}</h2>
    <form @submit.prevent="handleSubmit">
      <InputField
        id="groupName"
        label="Group Name"
        v-model="name"
        placeholder="Group Name"
        required
      />
      
      <div class="form-actions">
        <Button :variant="editMode ? 'success' : 'primary'" type="submit">
          {{ editMode ? 'Update' : 'Add' }} Group
        </Button>
        <Button v-if="editMode" variant="secondary" @click="$emit('cancel')">
          Cancel
        </Button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, onMounted } from 'vue';
import Button from '../Shared/Button.vue';
import InputField from '../Shared/InputField.vue';

const props = defineProps({
  editMode: {
    type: Boolean,
    default: false
  },
  groupData: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['submit', 'cancel']);
const name = ref('');

// Set initial value if in edit mode
onMounted(() => {
  if (props.editMode && props.groupData.name) {
    name.value = props.groupData.name;
  }
});

const handleSubmit = () => {
  if (!name.value.trim()) {
    alert('Group name is required');
    return;
  }

  emit('submit', { name: name.value });

  if (!props.editMode) {
    name.value = '';
  }
};
</script>

<style scoped>
.group-form {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}

.form-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
}
</style>
