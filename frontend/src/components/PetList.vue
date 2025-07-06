<script setup>
import { computed } from 'vue'

const props = defineProps({
  pets: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['edit', 'delete', 'view', 'refresh'])

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

const sortedPets = computed(() => {
  return [...props.pets].sort((a, b) => {
    const nameA = a.name || '';
    const nameB = b.name || '';
    return nameA.localeCompare(nameB);
  })
})
</script>

<template>
  <div class="pet-list">
    <div class="list-header">
      <h2>Pet List ({{ pets.length }} pets)</h2>
      <button @click="$emit('refresh')" class="btn btn-secondary">
        🔄 Refresh
      </button>
    </div>

    <div v-if="pets.length === 0" class="empty-state">
      <p>No pets found. Add your first pet!</p>
    </div>

    <div v-else class="pets-grid">
      <div 
        v-for="pet in sortedPets" 
        :key="pet.id" 
        class="pet-card"
      >
        <div class="pet-image">
          <img 
            v-if="pet.photoUrls && pet.photoUrls.length > 0" 
            :src="pet.photoUrls[0]" 
            :alt="pet.name"
            @error="$event.target.style.display='none'"
          />
          <div v-else class="no-image">
            🐾
          </div>
        </div>

        <div class="pet-info">
          <h3 class="pet-name">{{ pet.name }}</h3>
          
          <div class="pet-category" v-if="pet.category">
            <strong>Category:</strong> {{ pet.category.name }}
          </div>

          <div class="pet-status">
            <span 
              class="status-badge"
              :style="{ backgroundColor: statusColors[pet.status] }"
            >
              {{ statusLabels[pet.status] }}
            </span>
          </div>

          <div class="pet-tags" v-if="pet.tags && pet.tags.length > 0">
            <span 
              v-for="tag in pet.tags.slice(0, 3)" 
              :key="tag.id || tag.name"
              class="tag"
            >
              {{ tag.name }}
            </span>
            <span v-if="pet.tags.length > 3" class="tag-more">
              +{{ pet.tags.length - 3 }} more
            </span>
          </div>
        </div>

        <div class="pet-actions">
          <button 
            @click="$emit('view', pet.id)"
            class="btn btn-info"
            title="View Details"
          >
            👁️ View
          </button>
          <button 
            @click="$emit('edit', pet)"
            class="btn btn-warning"
            title="Edit Pet"
          >
            ✏️ Edit
          </button>
          <button 
            @click="$emit('delete', pet.id)"
            class="btn btn-danger"
            title="Delete Pet"
          >
            🗑️ Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.pet-list {
  width: 100%;
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.list-header h2 {
  color: #2c3e50;
  margin: 0;
}

.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s ease;
  margin-left: 8px;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

.btn-info {
  background-color: #17a2b8;
  color: white;
}

.btn-info:hover {
  background-color: #138496;
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

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #666;
  font-size: 18px;
}

.pets-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.pet-card {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.pet-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.pet-image {
  height: 200px;
  background-color: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.pet-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.no-image {
  font-size: 48px;
  color: #ccc;
}

.pet-info {
  padding: 20px;
}

.pet-name {
  margin: 0 0 10px 0;
  color: #2c3e50;
  font-size: 1.4rem;
}

.pet-category {
  margin-bottom: 10px;
  color: #666;
  font-size: 14px;
}

.pet-status {
  margin-bottom: 15px;
}

.status-badge {
  padding: 4px 12px;
  border-radius: 20px;
  color: white;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.pet-tags {
  margin-bottom: 15px;
}

.tag {
  display: inline-block;
  background-color: #e9ecef;
  color: #495057;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  margin-right: 5px;
  margin-bottom: 5px;
}

.tag-more {
  color: #6c757d;
  font-size: 11px;
  font-style: italic;
}

.pet-actions {
  padding: 0 20px 20px 20px;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.pet-actions .btn {
  flex: 1;
  min-width: 80px;
  font-size: 12px;
  padding: 6px 12px;
}

@media (max-width: 768px) {
  .pets-grid {
    grid-template-columns: 1fr;
  }
  
  .list-header {
    flex-direction: column;
    gap: 15px;
    align-items: stretch;
  }
  
  .pet-actions {
    flex-direction: column;
  }
}
</style> 