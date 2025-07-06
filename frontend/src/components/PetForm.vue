<script setup>
import { ref, reactive, onMounted, watch } from 'vue'

const props = defineProps({
  pet: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['submit', 'cancel'])

const isEditing = ref(false)
const errors = ref({})

const form = reactive({
  name: '',
  status: 'available',
  category: {
    name: ''
  },
  photoUrls: [''],
  tags: [{ name: '' }]
})

const statusOptions = [
  { value: 'available', label: 'Available' },
  { value: 'pending', label: 'Pending' },
  { value: 'sold', label: 'Sold' }
]

const resetForm = () => {
  form.name = ''
  form.status = 'available'
  form.category = { name: '' }
  form.photoUrls = ['']
  form.tags = [{ name: '' }]
  errors.value = {}
}

// Initialize form when pet prop changes
watch(() => props.pet, (newPet) => {
  if (newPet) {
    isEditing.value = true
    form.name = newPet.name || ''
    form.status = newPet.status || 'available'
    form.category = { name: newPet.category?.name || '' }
    form.photoUrls = newPet.photoUrls?.length ? [...newPet.photoUrls] : ['']
    form.tags = newPet.tags?.length ? [...newPet.tags] : [{ name: '' }]
  } else {
    isEditing.value = false
    resetForm()
  }
}, { immediate: true })

const addPhotoUrl = () => {
  form.photoUrls.push('')
}

const removePhotoUrl = (index) => {
  if (form.photoUrls.length > 1) {
    form.photoUrls.splice(index, 1)
  }
}

const addTag = () => {
  form.tags.push({ name: '' })
}

const removeTag = (index) => {
  if (form.tags.length > 1) {
    form.tags.splice(index, 1)
  }
}

const validateForm = () => {
  errors.value = {}
  
  if (!form.name.trim()) {
    errors.value.name = 'Name is required'
  }
  
  if (!form.status) {
    errors.value.status = 'Status is required'
  }
  
  // Validate photo URLs
  form.photoUrls.forEach((url, index) => {
    if (url && !isValidUrl(url)) {
      errors.value[`photoUrl${index}`] = 'Please enter a valid URL'
    }
  })
  
  return Object.keys(errors.value).length === 0
}

const isValidUrl = (string) => {
  try {
    new URL(string)
    return true
  } catch (_) {
    return false
  }
}

const handleSubmit = () => {
  console.log('handleSubmit called')
  if (!validateForm()) {
    console.log('Validation failed')
    return
  }
  
  const petData = {
    name: form.name.trim(),
    status: form.status,
    category: form.category.name ? { name: form.category.name.trim() } : undefined,
    photoUrls: form.photoUrls.filter(url => url.trim()),
    tags: form.tags.filter(tag => tag.name.trim()).map(tag => ({ name: tag.name.trim() }))
  }
  
  // Always include ID - use existing ID for editing, generate new for creating
  if (isEditing.value && props.pet) {
    petData.id = props.pet.id
  } else {
    // Generate a temporary ID for new pets (will be replaced by backend)
    petData.id = Date.now()
  }
  
  console.log('Emitting submit with data:', petData)
  emit('submit', petData)
}

const handleCancel = () => {
  resetForm()
  emit('cancel')
}
</script>

<template>
  <div class="pet-form">
    <div class="form-header">
      <h2>{{ isEditing ? 'Edit Pet' : 'Add New Pet' }}</h2>
      <button @click="handleCancel" class="btn btn-secondary">
        ✕ Cancel
      </button>
    </div>

    <form @submit.prevent="handleSubmit" class="form">
      <!-- Name -->
      <div class="form-group">
        <label for="name">Pet Name *</label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          class="form-control"
          :class="{ 'error': errors.name }"
          placeholder="Enter pet name"
        />
        <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
      </div>

      <!-- Status -->
      <div class="form-group">
        <label for="status">Status *</label>
        <select
          id="status"
          v-model="form.status"
          class="form-control"
          :class="{ 'error': errors.status }"
        >
          <option v-for="option in statusOptions" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
        <span v-if="errors.status" class="error-message">{{ errors.status }}</span>
      </div>

      <!-- Category -->
      <div class="form-group">
        <label for="category">Category</label>
        <input
          id="category"
          v-model="form.category.name"
          type="text"
          class="form-control"
          placeholder="Enter category (e.g., Dogs, Cats)"
        />
      </div>

      <!-- Photo URLs -->
      <div class="form-group">
        <label>Photo URLs</label>
        <div v-for="(url, index) in form.photoUrls" :key="index" class="url-input-group">
          <input
            v-model="form.photoUrls[index]"
            type="url"
            class="form-control"
            :class="{ 'error': errors[`photoUrl${index}`] }"
            placeholder="Enter photo URL"
          />
          <button
            v-if="form.photoUrls.length > 1"
            @click="removePhotoUrl(index)"
            type="button"
            class="btn btn-danger btn-small"
            title="Remove URL"
          >
            ✕
          </button>
          <span v-if="errors[`photoUrl${index}`]" class="error-message">{{ errors[`photoUrl${index}`] }}</span>
        </div>
        <button @click="addPhotoUrl" type="button" class="btn btn-secondary btn-small">
          + Add Photo URL
        </button>
      </div>

      <!-- Tags -->
      <div class="form-group">
        <label>Tags</label>
        <div v-for="(tag, index) in form.tags" :key="index" class="tag-input-group">
          <input
            v-model="form.tags[index].name"
            type="text"
            class="form-control"
            placeholder="Enter tag name"
          />
          <button
            v-if="form.tags.length > 1"
            @click="removeTag(index)"
            type="button"
            class="btn btn-danger btn-small"
            title="Remove tag"
          >
            ✕
          </button>
        </div>
        <button @click="addTag" type="button" class="btn btn-secondary btn-small">
          + Add Tag
        </button>
      </div>

      <!-- Submit Button -->
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          {{ isEditing ? 'Update Pet' : 'Create Pet' }}
        </button>
        <button @click="handleCancel" type="button" class="btn btn-secondary">
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>

<style scoped>
.pet-form {
  max-width: 600px;
  margin: 0 auto;
  background: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #e0e0e0;
}

.form-header h2 {
  margin: 0;
  color: #2c3e50;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #2c3e50;
}

.form-control {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
  transition: border-color 0.3s ease;
}

.form-control:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.form-control.error {
  border-color: #dc3545;
}

.error-message {
  color: #dc3545;
  font-size: 14px;
  margin-top: 5px;
  display: block;
}

.url-input-group,
.tag-input-group {
  display: flex;
  gap: 10px;
  margin-bottom: 10px;
  align-items: flex-start;
}

.url-input-group .form-control,
.tag-input-group .form-control {
  flex: 1;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-primary {
  background-color: #3498db;
  color: white;
}

.btn-primary:hover {
  background-color: #2980b9;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
}

.btn-danger:hover {
  background-color: #c82333;
}

.btn-small {
  padding: 8px 12px;
  font-size: 14px;
  min-width: 40px;
}

.form-actions {
  display: flex;
  gap: 15px;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #e0e0e0;
}

.form-actions .btn {
  flex: 1;
}

@media (max-width: 768px) {
  .pet-form {
    padding: 20px;
    margin: 0 10px;
  }
  
  .form-header {
    flex-direction: column;
    gap: 15px;
    align-items: stretch;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .url-input-group,
  .tag-input-group {
    flex-direction: column;
  }
}
</style> 