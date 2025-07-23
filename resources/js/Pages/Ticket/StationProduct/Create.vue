<script setup>
import { onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useStationProductStore } from '@/Stores/Ticket/stationProductStore'
import { useStationStore } from '@/Stores/Ticket/stationStore'
import { useProductStore } from '@/Stores/Ticket/productStore'
import { storeToRefs } from 'pinia'

const stationStore = useStationStore()
const productStore = useProductStore()
const { stations } = storeToRefs(stationStore)
const { products } = storeToRefs(productStore)
const stationProductStore = useStationProductStore()
const { form, errors, isLoading } = storeToRefs(stationProductStore)

const submit = () => {
  stationProductStore.store()
}

onMounted(() => {
  stationStore.ajaxList('A')
  productStore.ajaxList('A')
})
</script>

<template>
  <Head title="Producto por estacion" />
  <AuthenticatedLayout>
    <div class="mb-3">
      <h5 class="text-h5 font-weight-bold">Nuevo producto por estacion</h5>
      <Breadcrumbs :items="breadcrumbs" class="pa-0 mt-1" />
    </div>
    <VCard>
      <VForm @submit.prevent="submit">
        <VCardText>
          <VRow>
            <VCol cols="12" md="6" sm="12">
              <VAutocomplete
                v-model="form.station_id"
                label="Estacion"
                :items="stations"
                item-title="station_name"
                item-value="station_id"
                clearable
                :error-messages="errors.station_id"
              >
              </VAutocomplete>
            </VCol>
            <VCol cols="12" md="6" sm="12">
              <VAutocomplete
                v-model="form.product_id"
                label="Producto"
                :items="products"
                item-title="product_name"
                item-value="product_id"
                clearable
                :error-messages="errors.product_id"
              >
              </VAutocomplete>
            </VCol>
          </VRow>
          <VRow>
            <VCol cols="12" md="6" sm="12">
              <VRadioGroup v-model="form.status" label="Estatus" :error-messages="errors.status" inline>
                <VRadio value="Activo" label="Activo"></VRadio>
                <VRadio value="Inactivo" label="Inactivo"></VRadio>
              </VRadioGroup>
            </VCol>
          </VRow>
        </VCardText>
        <VCardActions>
          <VBtn
            prepend-icon="mdi-plus"
            :disabled="isLoading"
            type="submit"
            text="Guardar"
            variant="tonal"
            color="primary"
          ></VBtn>
          <Link href="/ticket/stationProduct" as="div">
            <VBtn prepend-icon="mdi-cancel" text="Cancelar" variant="tonal"></VBtn>
          </Link>
        </VCardActions>
      </VForm>
    </VCard>
  </AuthenticatedLayout>
</template>
<script>
export default {
  data() {
    return {
      breadcrumbs: [
        { title: 'Panel', disabled: false, href: '/dashboard' },
        { title: 'Producto por estacion', disabled: false, href: '/ticket/stationProduct' },
        { title: 'Crear', disabled: true },
      ],
    }
  },
}
</script>
