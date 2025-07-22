import { ref } from "vue"
import { defineStore } from "pinia"
import { useToast } from "vue-toastification"
import { useForm } from "@inertiajs/vue3"

const toast = useToast()
const api_url = '/ticket/reader'

export const useQrStore = defineStore('qrStore', () => {
  const result = ref(true)
  const message = ref(null)
  const isLoading = ref(false)

  const form = useForm({
    ticket_id: null,
    uuid: null
  })

  const validate = async (uuid) => {
    isLoading.value = true

    try {
      const response = await window.axios.get(`${api_url}/ticket/${uuid}`)
      const { success, message, ticket } = response.data
      Object.assign(form, ticket)
      result.value = success
    } catch (error) {
      toast.error(error.message)
    } finally {
      isLoading.value = false
    }
  }

  const store = () => {
    isLoading.value = true

    form.post(`${api_url}/store`, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
      },
      onError: (error) => {
        toast.error(error.message)
      },
      onFinish: () => {
        isLoading.value = false
      }
    })
  }

  return {
    form,
    result,
    message,
    isLoading,
    validate,
    store,
  }

})
