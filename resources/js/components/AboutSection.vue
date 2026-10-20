<script setup>
import SectionHeader from './SectionHeader.vue'

defineProps({
    number: { type: String, required: true },
    paragraphs: { type: Array, required: true },
})
</script>

<template>
    <section id="about" class="section py-16 sm:py-24" aria-labelledby="about-title">
        <SectionHeader :number="number" title="About" heading-id="about-title" />

        <div class="max-w-[46rem] space-y-6 text-lg text-ink-dim">
            <p v-for="(paragraph, index) in paragraphs" :key="index">{{ paragraph }}</p>
        </div>
    </section>
</template>
