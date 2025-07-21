<script setup>
import { onMounted } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useCurrentFundStore } from '@/stores/fund/currentFundStore'
import { storeToRefs } from 'pinia'

const page = usePage()
const currentFundStore = useCurrentFundStore()
const { form, errors, isLoading } = storeToRefs(currentFundStore)

const submit = () => {
  currentFundStore.update(page.props.currentfund.current_fund_id)
}

onMounted(() => {
  Object.assign(currentFundStore.form, page.props.currentfund)
})
</script>

<template>
  <Head title="Fondo circulante" />
  <AuthenticatedLayout>
    <div class="mb-3">
      <h5 class="text-h5 font-weight-bold">Actualizacion de fondo circulante</h5>
      <Breadcrumbs :items="breadcrumbs" class="pa-0 mt-1" />
    </div>
    <VCard>
      <VForm @submit.prevent="submit">
        <VCardText>
          <VRow>
            <VCol cols="12" md="6" sm="12">
              <VTextField v-model="form.fund_name" label="Nombre del fondo" :error-messages="errors.fund_name" />
            </VCol>
            <VCol cols="12" md="6" sm="12">
              <VTextField
                v-model="form.total_amount"
                label="Monto total"
                :error-messages="errors.total_amount"
              ></VTextField>
            </VCol>
          </VRow>
          <VRow>
            <VCol cols="12" md="6" sm="12">
              <VTextField
                v-model="form.assignment_date"
                type="date"
                label="Fecha de asignacion"
                :error-messages="errors.assignment_date"
              ></VTextField>
            </VCol>
            <VCol cols="12" md="6" sm="12">
              <VRadioGroup v-model="form.status" label="Estatus" :error-messages="errors.status" inline>
                <VRadio value="ACTIVO" label="ACTIVO"></VRadio>
                <VRadio value="CERRADO" label="CERRADO"></VRadio>
              </VRadioGroup>
            </VCol>
          </VRow>
        </VCardText>
        <VCardActions>
          <VBtn
            prepend-icon="mdi-database"
            :disabled="isLoading"
            type="submit"
            text="Guardar"
            variant="tonal"
            color="primary"
          ></VBtn>
          <Link href="/curfu/currentfund" as="div">
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
        { title: 'Fondo circulante', disabled: false, href: '/fund/currentfund' },
        { title: 'Actualizar', disabled: true },
      ],
    }
  },
}
</script>
