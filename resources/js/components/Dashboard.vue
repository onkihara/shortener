<template>
  <div>
    <DashboardHeader />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <CreateButtonBar
        :create-route="createRoute"
      />
      
      <ErrorAlert 
        v-if="errorMessage" 
        :message="errorMessage"
        @close="errorMessage = ''"
      />

      <SuccessAlert 
        v-if="successMessage" 
        :message="successMessage"
        @close="successMessage = ''"
      />

      <SearchBar 
        v-model="searchQuery"
        @update:model-value="handleSearch"
      />

      <LoadingSpinner v-if="isLoading" />

      <ShortenerTable 
        v-else-if="shorteners.length > 0"
        :shorteners="shorteners"
        :timezone="timezone"
        @edit="openEditModal"
        @delete="openDeleteModal"
      />

      <EmptyState v-else />

      <Pagination 
        v-if="pagination.last_page > 1 && !isLoading"
        :pagination="pagination"
        @go-to-page="goToPage"
      />
    </div>

    <EditModal 
      v-if="editingItem"
      :item="editingItem"
      :timezone="timezone"
      :error-message="editErrorMessage"
      :is-saving="isSaving"
      @save="saveChanges"
      @close="editingItem = null"
    />

    <DeleteModal 
      v-if="deletingItem"
      :item="deletingItem"
      @confirm="confirmDelete"
      @close="deletingItem = null"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import DashboardHeader from './Dashboard/DashboardHeader.vue'
import CreateButtonBar from './Dashboard/CreateButtonBar.vue'
import ErrorAlert from './Dashboard/ErrorAlert.vue'
import SuccessAlert from './Dashboard/SuccessAlert.vue'
import SearchBar from './Dashboard/SearchBar.vue'
import LoadingSpinner from './Dashboard/LoadingSpinner.vue'
import ShortenerTable from './Dashboard/ShortenerTable.vue'
import EmptyState from './Dashboard/EmptyState.vue'
import Pagination from './Dashboard/Pagination.vue'
import EditModal from './Dashboard/EditModal.vue'
import DeleteModal from './Dashboard/DeleteModal.vue'

const props = defineProps({
  csrfToken: {
    type: String,
    required: true
  },
  timezone: {
    type: String,
    default: 'UTC'
  },
  createRoute: {
    type: String,
    default: 'create'
  }
})

const shorteners = ref([])
const searchQuery = ref('')
const errorMessage = ref('')
const successMessage = ref('')
const isLoading = ref(false)
const currentPage = ref(1)
const editingItem = ref(null)
const editErrorMessage = ref('')
const isSaving = ref(false)
const deletingItem = ref(null)

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0
})

const loadShorteners = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const params = new URLSearchParams({
      page: currentPage.value
    })

    if (searchQuery.value) {
      params.append('search', searchQuery.value)
    }

    const response = await fetch(`/api/shorteners?${params}`, {
      headers: {
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error('Failed to load shorteners')
    }

    const data = await response.json()
    shorteners.value = data.data
    pagination.value = data.pagination
  } catch (error) {
    errorMessage.value = 'Fehler beim Laden der Shortcuts: ' + error.message
  } finally {
    isLoading.value = false
  }
}

const handleSearch = () => {
  currentPage.value = 1
  loadShorteners()
}

const goToPage = (page) => {
  currentPage.value = page
  loadShorteners()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const openEditModal = (item) => {
  editErrorMessage.value = ''
  editingItem.value = item
}

const openDeleteModal = (item) => {
  deletingItem.value = item
}

const saveChanges = async (formData) => {
  if (isSaving.value) return

  editErrorMessage.value = ''
  isSaving.value = true
  
  try {
    const response = await fetch(`/api/shorteners/${editingItem.value.ID}`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': props.csrfToken
      },
      body: JSON.stringify(formData)
    })

    if (!response.headers.get('content-type')?.includes('application/json')) {
      throw new Error(`Unerwartete Antwort beim Speichern (HTTP ${response.status})`)
    }

    const data = await response.json()

    if (!response.ok) {
      const validationMessages = Object.values(data.errors || {}).flat().filter(message => typeof message === 'string')
      throw new Error(validationMessages.join(' ') || data.message || `Fehler beim Speichern (HTTP ${response.status})`)
    }

    successMessage.value = data.message
    editingItem.value = null
    loadShorteners()
  } catch (error) {
    editErrorMessage.value = 'Fehler: ' + (error.message || 'Ein unbekannter Fehler ist aufgetreten.')
  } finally {
    isSaving.value = false
  }
}

const confirmDelete = async () => {
  errorMessage.value = ''

  try {
    const response = await fetch(`/api/shorteners/${deletingItem.value.ID}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': props.csrfToken
      }
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Fehler beim Löschen')
    }

    successMessage.value = data.message
    deletingItem.value = null
    loadShorteners()
  } catch (error) {
    errorMessage.value = 'Fehler: ' + error.message
  }
}

onMounted(() => {
  loadShorteners()
})
</script>
