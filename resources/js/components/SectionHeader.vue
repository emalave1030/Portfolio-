<script setup>
defineProps({
    number: { type: String, required: true },
    title: { type: String, required: true },
    headingId: { type: String, required: true },
})
</script>

<template>
    <header class="mb-10 border-t border-line pt-6">
        <p class="section-number">{{ number }}</p>
        <h2 :id="headingId" class="section-title mt-2">{{ title }}</h2>
    </header>
</template>
