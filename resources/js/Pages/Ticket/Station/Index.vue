<script setup>
import { reactive, ref, inject } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DeleteDialog from '@/Components/DeleteDialog.vue'
import { useCurrentFundStore } from '@/stores/fund/currentFundStore.js'
import { storeToRefs } from 'pinia'

const search = ref(null)
const deleteId = ref(null)
const deleteDialog = ref(false)
const helpers = inject('helpers')
const currentFundStore = useCurrentFundStore()
const { items, totalItems, loading } = storeToRefs(currentFundStore)

const filterForm = reactive({
  fund_name: null,
  start_assignment_datee: null,
  end_assignment_date: null,
})

const deleteItem = (item) => {
  deleteId.value = item.current_fund_id
  deleteDialog.value = true
}

const submitDelete = () => {
  currentFundStore.destroy(deleteId.value)
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

  currentFundStore.index(filters)
}

const applyFilter = () => {
  search.value = String(Date.now())
}
</script>
<template>
  <Head title="Fondo circulante" />
  <AuthenticatedLayout>
    <div class="mb-3">
      <h5 class="text-h5 font-weight-bold">Consulta de fondo circulante</h5>
      <Breadcrumbs :items="breadcrumbs" class="pa-0 mt-1" />
    </div>
    <VCard title="Filtro de fondo circulante">
      <VCardText>
        <VRow dense>
          <VCol cols="12" md="12" sm="12">
            <VTextField v-model="filterForm.fund_name" label="Nombre del fondo" hide-details clearable></VTextField>
          </VCol>
        </VRow>
        <VRow>
          <VCol cols="12" md="6" sm="12">
            <VTextField
              v-model="filterForm.start_assignment_datee"
              type="date"
              label="Fecha de asignacion inicial"
              hide-details
              clearable
            ></VTextField>
          </VCol>
          <VCol cols="12" md="6" sm="12">
            <VTextField
              v-model="filterForm.end_assignment_date"
              type="date"
              label="Fecha de asignacion final"
              hide-details
              clearable
            ></VTextField>
          </VCol>
        </VRow>
        <VRow>
          <VCol cols="12" md="12" sm="12">
            <VBtnToggle variant="tonal" divided>
              <VBtn prepend-icon="mdi-filter" text="Filtrar" color="primary" @click="applyFilter"></VBtn>
              <VBtn prepend-icon="mdi-plus" text="Agregar" @click="router.visit('/fund/currentfund/create')"></VBtn>
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
              :loading="loading"
              @update:options="loadItems"
            >
              <template #[`item.action`]="{ item }">
                <Link :href="`/fund/currentfund/${item.current_fund_id}/edit`" as="button">
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
        { title: 'Nombre del fondo', key: 'fund_name' },
        { title: 'Monto total', key: 'total_amount' },
        { title: 'Asignado', key: 'assignment_date' },
        { title: 'Estatus', key: 'status' },
        { title: 'Acción', key: 'action', sortable: false },
      ],
      breadcrumbs: [
        { title: 'Panel', disabled: false, href: '/dashboard' },
        { title: 'Fondo circulante', disabled: true },
      ],
    }
  },
}
</script>
