<script setup>
import { onMounted } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useStationUserStore } from '@/Stores/Ticket/stationUserStore'
import { useStationStore } from '@/Stores/Ticket/stationStore'
import { useUserStore } from '@/Stores/Admin/User/userStore'
import { storeToRefs } from 'pinia'

const page = usePage()
const stationStore = useStationStore()
const userStore = useUserStore()
const { stations } = storeToRefs(stationStore)
const { users } = storeToRefs(userStore)
const stationUserStore = useStationUserStore()
const { form, errors, isLoading } = storeToRefs(stationUserStore)

const submit = () => {
  stationUserStore.update(page.props.stationUser.station_user_id)
}

onMounted(() => {
  stationStore.ajaxList('A')
  userStore.ajaxList()
  Object.assign(stationUserStore.form, page.props.stationUser)
})
</script>

<template>
  <Head title="Usuario por estacion" />
  <AuthenticatedLayout>
    <div class="mb-3">
      <h5 class="text-h5 font-weight-bold">Actualizacion de usuario por estacion</h5>
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
                v-model="form.user_id"
                label="Usuario"
                :items="users"
                item-title="name"
                item-value="user_id"
                clearable
                :error-messages="errors.user_id"
              >
              </VAutocomplete>
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
          <Link href="/ticket/stationUser" as="div">
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
        { title: 'Usuario por estacion', disabled: false, href: '/ticket/stationUser' },
        { title: 'Actualizar', disabled: true },
      ],
    }
  },
}
</script>
