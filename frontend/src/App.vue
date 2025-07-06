<script setup>
import { ref, onMounted } from 'vue'
import PetList from './components/PetList.vue'
import PetForm from './components/PetForm.vue'
import PetDetails from './components/PetDetails.vue'

const pets = ref([])
const selectedPet = ref(null)
const showForm = ref(false)
const showDetails = ref(false)
const loading = ref(false)
const error = ref('')
const success = ref('')

const API_BASE_URL = 'http://localhost:8000/api'

// Fetch all pets
const fetchPets = async () => {
  loading.value = true
  error.value = ''
  try {
    const response = await fetch(`${API_BASE_URL}/pets`)
    const result = await response.json()
    
    if (result.success) {
      pets.value = result.data
    } else {
      error.value = result.message || 'Failed to fetch pets'
    }
  } catch (err) {
    error.value = 'Network error: ' + err.message
  } finally {
    loading.value = false
  }
}

// Create new pet
const createPet = async (petData) => {
  console.log('createPet function called with:', petData)
  loading.value = true
  error.value = ''
  try {
    console.log('Making POST request to:', `${API_BASE_URL}/pets`)
    const response = await fetch(`${API_BASE_URL}/pets`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(petData)
    })
    console.log('Response status:', response.status)
    const result = await response.json()
    console.log('Response data:', result)
    
    if (result.success) {
      success.value = 'Pet created successfully!'
      showForm.value = false
      await fetchPets()
    } else {
      error.value = result.message || 'Failed to create pet'
    }
  } catch (err) {
    console.error('Error creating pet:', err)
    error.value = 'Network error: ' + err.message
  } finally {
    loading.value = false
  }
}

// Update pet
const updatePet = async (petData) => {
  console.log('updatePet function called with:', petData)
  loading.value = true
  error.value = ''
  try {
    console.log('Making PUT request to:', `${API_BASE_URL}/pets/${petData.id}`)
    const response = await fetch(`${API_BASE_URL}/pets/${petData.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(petData)
    })
    console.log('Response status:', response.status)
    const result = await response.json()
    console.log('Response data:', result)
    
    if (result.success) {
      success.value = 'Pet updated successfully!'
      showForm.value = false
      showDetails.value = false
      selectedPet.value = null
      await fetchPets()
    } else {
      error.value = result.message || 'Failed to update pet'
    }
  } catch (err) {
    console.error('Error updating pet:', err)
    error.value = 'Network error: ' + err.message
  } finally {
    loading.value = false
  }
}

// Delete pet
const deletePet = async (id) => {
  if (!confirm('Are you sure you want to delete this pet?')) return
  
  loading.value = true
  error.value = ''
  try {
    const response = await fetch(`${API_BASE_URL}/pets/${id}`, {
      method: 'DELETE'
    })
    
    // Handle 204 No Content response
    if (response.status === 204) {
      success.value = 'Pet deleted successfully!'
      if (selectedPet.value?.id === id) {
        selectedPet.value = null
        showDetails.value = false
      }
      await fetchPets()
      return
    }
    
    // Handle other responses with JSON
    const result = await response.json()
    
    if (result.success) {
      success.value = 'Pet deleted successfully!'
      if (selectedPet.value?.id === id) {
        selectedPet.value = null
        showDetails.value = false
      }
      await fetchPets()
    } else {
      error.value = result.message || 'Failed to delete pet'
    }
  } catch (err) {
    error.value = 'Network error: ' + err.message
  } finally {
    loading.value = false
  }
}

// Show pet details
const showPetDetails = async (id) => {
  loading.value = true
  error.value = ''
  try {
    const response = await fetch(`${API_BASE_URL}/pets/${id}`)
    const result = await response.json()
    
    if (result.success) {
      selectedPet.value = result.data
      showDetails.value = true
      showForm.value = false
    } else {
      error.value = result.message || 'Failed to fetch pet details'
    }
  } catch (err) {
    error.value = 'Network error: ' + err.message
  } finally {
    loading.value = false
  }
}

// Edit pet
const editPet = (pet) => {
  selectedPet.value = pet
  showForm.value = true
  showDetails.value = false
}

// Clear messages
const clearMessages = () => {
  error.value = ''
  success.value = ''
}

onMounted(() => {
  fetchPets()
})
</script>

<template>
  <div class="app">
    <header class="header">
      <h1>🐾 Pet Store Manager</h1>
      <button 
        @click="showForm = true; selectedPet = null; showDetails = false; clearMessages()"
        class="btn btn-primary"
      >
        Add New Pet
      </button>
    </header>

    <!-- Messages -->
    <div v-if="error" class="alert alert-error">
      {{ error }}
      <button @click="clearMessages" class="close-btn">&times;</button>
    </div>
    
    <div v-if="success" class="alert alert-success">
      {{ success }}
      <button @click="clearMessages" class="close-btn">&times;</button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading">
      Loading...
    </div>

    <main class="main">
      <!-- Pet List -->
      <PetList 
        v-if="!showForm && !showDetails"
        :pets="pets"
        @edit="editPet"
        @delete="deletePet"
        @view="showPetDetails"
        @refresh="fetchPets"
      />

      <!-- Pet Form -->
      <PetForm 
        v-if="showForm"
        :pet="selectedPet"
        @submit="(petData) => selectedPet ? updatePet(petData) : createPet(petData)"
        @cancel="showForm = false; selectedPet = null; clearMessages()"
      />

      <!-- Pet Details -->
      <PetDetails 
        v-if="showDetails"
        :pet="selectedPet"
        @edit="editPet"
        @delete="deletePet"
        @back="showDetails = false; selectedPet = null; clearMessages()"
      />
    </main>
  </div>
</template>

<style scoped>
.app {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 2px solid #e0e0e0;
}

.header h1 {
  color: #2c3e50;
  margin: 0;
  font-size: 2.5rem;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 6px;
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
  transform: translateY(-2px);
}

.alert {
  padding: 15px 20px;
  border-radius: 6px;
  margin-bottom: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.alert-error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.alert-success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.close-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: inherit;
}

.loading {
  text-align: center;
  padding: 40px;
  font-size: 18px;
  color: #666;
}

.main {
  min-height: 400px;
}
</style>
