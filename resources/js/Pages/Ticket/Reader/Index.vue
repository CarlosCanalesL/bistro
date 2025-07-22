<script setup>
import { storeToRefs } from 'pinia'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ScannerCodeQR from '@/Components/ScannerCodeQR.vue'
import { useQrStore } from '@/Stores/Ticket/qrStore'

const qrStore = useQrStore()
const { form, result, message, isLoading } = storeToRefs(qrStore)

const onScanResult = async (decodeText) => {
  await qrStore.validate(decodeText)
}

const submit = () => {
  qrStore.store()
}
</script>
<template>
  <Head title="LectorQR" />
  <AuthenticatedLayout>
    <VCard title="Escaneo de codigos">
      <VCardText>
        <ScannerCodeQR :fps="10" :qrbox="275" :reader-on="true" @result="onScanResult"></ScannerCodeQR>
      </VCardText>
      <VCardText v-if="form.product_name">
        <VForm @submit.prevent="submit">
          <VRow dense>
            <VCol cols="12" md="6" sm="12">
              Producto: <VLabel style="font-weight: bold">{{ form.product_name }}</VLabel>
            </VCol>
            <VCol cols="12" md="6" sm="12">
              Precio: <VLabel style="font-weight: bold">{{ form.unit_price }}</VLabel>
            </VCol>
          </VRow>
          <VRow>
            <VCol cols="12" md="6" sm="12">
              <VBtn
                prepend-icon="mdi-database"
                type="submit"
                text="Canjear"
                variant="tonal"
                color="primary"
                :disabled="form.processing"
              ></VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </AuthenticatedLayout>
</template>
