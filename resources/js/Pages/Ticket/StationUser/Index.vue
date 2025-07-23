<script setup>
import { reactive, ref, inject, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DeleteDialog from '@/Components/DeleteDialog.vue'
import { useStationUserStore } from '@/Stores/Ticket/stationUserStore'
import { useStationStore } from '@/Stores/Ticket/stationStore'
import { useUserStore } from '@/Stores/Admin/User/userStore'
import { storeToRefs } from 'pinia'

const search = ref(null)
const deleteId = ref(null)
const deleteDialog = ref(false)
const helpers = inject('helpers')
const stationUserStore = useStationUserStore()
const stationStore = useStationStore()
const userStore = useUserStore()
const { stations } = storeToRefs(stationStore)
const { users } = storeToRefs(userStore)
const { items, totalItems, isLoading } = storeToRefs(stationUserStore)

const filterForm = reactive({
  station_id: null,
  user_id: null,
})

const deleteItem = (item) => {
  deleteId.value = item.station_user_id
  deleteDialog.value = true
}

const submitDelete = () => {
  stationUserStore.destroy(deleteId.value)
  deleteDialog.value = false
}

const loadItems = ({ page, itemsPerPage, sortBy }) => {
  if (search != null && search.length < 3) return

  let filters = {
    page: page,
    limit: itemsPerPage,
    sort: sortBy[0],
  }

  filters.search = helpers.removeEmptyAttribute(filterForm)

  stationUserStore.index(filters)
}

const applyFilter = () => {
  search.value = String(Date.now())
}

onMounted(() => {
  stationStore.ajaxList('Activo')
  userStore.ajaxList()
})
</script>
<template>
  <Head title="Usuario por estacion" />
  <AuthenticatedLayout>
    <div class="mb-3">
      <h5 class="text-h5 font-weight-bold">Consulta de usuario por estacion</h5>
      <Breadcrumbs :items="breadcrumbs" class="pa-0 mt-1" />
    </div>
    <VCard title="Formulario de filtro">
      <VCardText>
        <VRow dense>
          <VCol cols="12" md="6" sm="12">
            <VAutocomplete
              v-model="filterForm.station_id"
              label="Estacion"
              :items="stations"
              item-title="station_name"
              item-value="station_id"
              clearable
              hide-details
            >
            </VAutocomplete>
          </VCol>
          <VCol cols="12" md="6" sm="12">
            <VAutocomplete
              v-model="filterForm.user_id"
              label="Usuario"
              :items="users"
              item-title="name"
              item-value="user_id"
              clearable
              hide-details
            >
            </VAutocomplete>
          </VCol>
        </VRow>
        <VRow>
          <VCol cols="12" md="12" sm="12">
            <VBtnToggle variant="tonal" divided>
              <VBtn prepend-icon="mdi-filter" text="Filtrar" color="primary" @click="applyFilter"></VBtn>
              <VBtn prepend-icon="mdi-plus" text="Agregar" @click="router.visit('/ticket/stationUser/create')"></VBtn>
            </VBtnToggle>
          </VCol>
        </VRow>
        <VRow dense>
          <VCol cols="12" md="12" sm="12">
            <VDataTableServer
              :items="items"
              :items-length="totalItems"
              :headers="headers"
              :search="search"
              :loading="isLoading"
              @update:options="loadItems"
            >
              <template #[`item.action`]="{ item }">
                <Link :href="`/ticket/stationUser/${item.station_user_id}/edit`" as="button">
                  <VIcon color="warning" icon="mdi-pencil" />
                </Link>
                <VIcon class="ml-2" color="error" icon="mdi-delete" @click="deleteItem(item)" />
              </template>
            </VDataTableServer>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>
    <DeleteDialog
      v-model="deleteDialog"
      title="Eliminar el usuario"
      @close-delete-dialog="deleteDialog = false"
      @delete-item="submitDelete"
    ></DeleteDialog>
  </AuthenticatedLayout>
</template>
<script>
export default {
  data() {
    return {
      headers: [
        { title: 'Estacion', key: 'station_name' },
        { title: 'Usuario', key: 'name' },
        { title: 'Acción', key: 'action', sortable: false },
      ],
      breadcrumbs: [
        { title: 'Panel', disabled: false, href: '/dashboard' },
        { title: 'Usuario por estacion', disabled: true },
      ],
    }
  },
}
</script>
