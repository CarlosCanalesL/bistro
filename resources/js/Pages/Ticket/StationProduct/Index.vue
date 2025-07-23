<script setup>
import { reactive, ref, inject, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DeleteDialog from '@/Components/DeleteDialog.vue'
import { useStationProductStore } from '@/Stores/Ticket/stationProductStore'
import { useStationStore } from '@/Stores/Ticket/stationStore'
import { useProductStore } from '@/Stores/Ticket/productStore'
import { storeToRefs } from 'pinia'

const search = ref(null)
const deleteId = ref(null)
const deleteDialog = ref(false)
const helpers = inject('helpers')
const stationProductStore = useStationProductStore()
const stationStore = useStationStore()
const productStore = useProductStore()
const { stations } = storeToRefs(stationStore)
const { products } = storeToRefs(productStore)
const { items, totalItems, isLoading } = storeToRefs(stationProductStore)

const filterForm = reactive({
  product_id: null,
  station_id: null,
  status: null,
})

const deleteItem = (item) => {
  deleteId.value = item.station_product_id
  deleteDialog.value = true
}

const submitDelete = () => {
  stationProductStore.destroy(deleteId.value)
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

  stationProductStore.index(filters)
}

const applyFilter = () => {
  search.value = String(Date.now())
}

onMounted(() => {
  stationStore.ajaxList('Activo')
  productStore.ajaxList('Activo')
})
</script>
<template>
  <Head title="Productos por estacion" />
  <AuthenticatedLayout>
    <div class="mb-3">
      <h5 class="text-h5 font-weight-bold">Consulta de productos por estacion</h5>
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
              v-model="filterForm.product_id"
              label="Producto"
              :items="products"
              item-title="product_name"
              item-value="product_id"
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
              <VBtn
                prepend-icon="mdi-plus"
                text="Agregar"
                @click="router.visit('/ticket/stationProduct/create')"
              ></VBtn>
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
                <Link :href="`/ticket/stationProduct/${item.station_product_id}/edit`" as="button">
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
      title="Eliminar el producto"
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
        { title: 'Producto', key: 'product_name' },
        { title: 'Estatus', key: 'status' },
        { title: 'Acción', key: 'action', sortable: false },
      ],
      breadcrumbs: [
        { title: 'Panel', disabled: false, href: '/dashboard' },
        { title: 'Producto por estacion', disabled: true },
      ],
    }
  },
}
</script>
