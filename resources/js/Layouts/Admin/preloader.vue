<script setup>
import { ref } from 'vue'
const loaded = ref(true)
setTimeout(() => loaded.value = false, 500)
</script>
<template>
    <div v-if="loaded" class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white">
        <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-primary border-t-transparent"></div>
    </div>
</template>