<script setup>
import { computed } from 'vue'

const props = defineProps({
  pet: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['edit', 'delete', 'back'])

const statusColors = {
  available: '#28a745',
  pending: '#ffc107',
  sold: '#dc3545'
}

const statusLabels = {
  available: 'Available',
  pending: 'Pending',
  sold: 'Sold'
}

const hasPhotos = computed(() => {
  return props.pet.photoUrls && props.pet.photoUrls.length > 0
})

const hasTags = computed(() => {
  return props.pet.tags && props.pet.tags.length > 0
})
</script>

<template>
  <div class="pet-details">
    <div class="details-header">
      <button @click="$emit('back')" class="btn btn-secondary">
        ← Back to List
      </button>
      <div class="header-actions">
        <button @click="$emit('edit', pet)" class="btn btn-warning">
          ✏️ Edit
        </button>
        <button @click="$emit('delete', pet.id)" class="btn btn-danger">
          🗑️ Delete
        </button>
      </div>
    </div>

    <div class="details-content">
      <!-- Pet Images -->
      <div v-if="hasPhotos" class="pet-images">
        <h3>Photos</h3>
        <div class="image-gallery">
          <img
            v-for="(url, index) in pet.photoUrls"
            :key="index"
            :src="url"
            :alt="`${pet.name} - Photo ${index + 1}`"
            class="pet-image"
            @error="$event.target.style.display='none'"
          />
        </div>
      </div>

      <!-- Pet Info -->
      <div class="pet-info-section">
        <div class="info-header">
          <h2>{{ pet.name }}</h2>
          <span 
            class="status-badge"
            :style="{ backgroundColor: statusColors[pet.status] }"
          >
            {{ statusLabels[pet.status] }}
          </span>
        </div>

        <div class="info-grid">
          <!-- Basic Info -->
          <div class="info-card">
            <h3>Basic Information</h3>
            <div class="info-item">
              <strong>ID:</strong> {{ pet.id }}
            </div>
            <div class="info-item">
              <strong>Name:</strong> {{ pet.name }}
            </div>
            <div class="info-item">
              <strong>Status:</strong> 
              <span 
                class="status-text"
                :style="{ color: statusColors[pet.status] }"
              >
                {{ statusLabels[pet.status] }}
              </span>
            </div>
            <div v-if="pet.category" class="info-item">
              <strong>Category:</strong> {{ pet.category.name }}
            </div>
          </div>

          <!-- Tags -->
          <div v-if="hasTags" class="info-card">
            <h3>Tags</h3>
            <div class="tags-container">
              <span 
                v-for="tag in pet.tags" 
                :key="tag.id || tag.name"
                class="tag"
              >
                {{ tag.name }}
              </span>
            </div>
          </div>

          <!-- Photo URLs -->
          <div v-if="hasPhotos" class="info-card">
            <h3>Photo URLs</h3>
            <div class="urls-container">
              <a 
                v-for="(url, index) in pet.photoUrls" 
                :key="index"
                :href="url"
                target="_blank"
                class="photo-url"
              >
                Photo {{ index + 1 }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.pet-details {
  max-width: 800px;
  margin: 0 auto;
}

.details-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 2px solid #e0e0e0;
}

.header-actions {
  display: flex;
  gap: 10px;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

.btn-warning {
  background-color: #ffc107;
  color: #212529;
}

.btn-warning:hover {
  background-color: #e0a800;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
}

.btn-danger:hover {
  background-color: #c82333;
}

.details-content {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
}

.pet-images {
  padding: 20px;
  border-bottom: 1px solid #e0e0e0;
}

.pet-images h3 {
  margin: 0 0 15px 0;
  color: #2c3e50;
}

.image-gallery {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.pet-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid #e0e0e0;
}

.pet-info-section {
  padding: 30px;
}

.info-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #e0e0e0;
}

.info-header h2 {
  margin: 0;
  color: #2c3e50;
  font-size: 2rem;
}

.status-badge {
  padding: 6px 16px;
  border-radius: 20px;
  color: white;
  font-size: 14px;
  font-weight: 600;
  text-transform: uppercase;
}

.info-grid {
  display: grid;
  gap: 20px;
}

.info-card {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 6px;
  border-left: 4px solid #3498db;
}

.info-card h3 {
  margin: 0 0 15px 0;
  color: #2c3e50;
  font-size: 1.2rem;
}

.info-item {
  margin-bottom: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.info-item strong {
  color: #495057;
  min-width: 80px;
}

.status-text {
  font-weight: 600;
}

.tags-container {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.tag {
  background-color: #e9ecef;
  color: #495057;
  padding: 4px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 500;
}

.urls-container {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.photo-url {
  color: #3498db;
  text-decoration: none;
  padding: 8px 12px;
  background: white;
  border-radius: 4px;
  border: 1px solid #e0e0e0;
  transition: all 0.3s ease;
}

.photo-url:hover {
  background: #f8f9fa;
  border-color: #3498db;
  color: #2980b9;
}

@media (max-width: 768px) {
  .details-header {
    flex-direction: column;
    gap: 15px;
    align-items: stretch;
  }
  
  .header-actions {
    justify-content: center;
  }
  
  .info-header {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }
  
  .image-gallery {
    grid-template-columns: 1fr;
  }
  
  .info-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
  }
}
</style> 