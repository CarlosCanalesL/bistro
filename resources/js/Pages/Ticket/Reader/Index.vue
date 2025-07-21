<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ScannerCodeQR from '@/Components/ScannerCodeQR.vue'

const readerDialog = ref(false)

const onScanResult = (decodeText) => {
  readerDialog.value = false
  console.log(decodeText)
}

const codeqrClose = () => {
  document.getElementById('html5-qrcode-button-camera-stop').click()
}
</script>
<template>
  <Head title="LectorQR" />
  <AuthenticatedLayout>
    <VCard title="Escaneo de codigos">
      <VCardText>
        <ScannerCodeQR
          :fps="10"
          :qrcode="275"
          :reader-on="true"
          style="width: 275px"
          @result="onScanResult"
        ></ScannerCodeQR>
      </VCardText>
      <VCardActions>
        <VBtn prepend-icon="mdi-stop-circle" text="Detener" variant="tonal" @click="codeqrClose()"></VBtn>
      </VCardActions>
    </VCard>
  </AuthenticatedLayout>
</template>
