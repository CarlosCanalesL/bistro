<script setup>
import { onMounted } from 'vue'
import { Html5QrcodeScanner, Html5QrcodeScanType } from 'html5-qrcode'

const props = defineProps({
  fps: { type: Number, default: 10 },
  qrbox: { type: Number, default: 250 },
  readerOn: { type: Boolean, required: true },
})

const emits = defineEmits(['result'])

const createScan = () => {
  const config = {
    fps: props.fps,
    qrbox: props.qrbox,
    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
  }

  const html5QrcodeScanner = new Html5QrcodeScanner('qrcode', config)
  html5QrcodeScanner.render(onScanSuccess)
}

const onScanSuccess = (decodedText, decodedResult) => {
  document.getElementById('html5-qrcode-button-camera-stop').click()

  emits('result', decodedText, decodedResult)

  if (props.readerOn) {
    setTimeout(() => {
      createScan()
    }, 2000)
  }
}

onMounted(() => {
  createScan()
})
</script>
<template>
  <div id="qrcode"></div>
</template>
