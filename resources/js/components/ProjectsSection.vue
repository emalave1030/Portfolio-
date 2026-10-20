<script setup>
import SectionHeader from './SectionHeader.vue'
import ProjectCard from './ProjectCard.vue'

defineProps({
    number: { type: String, required: true },
    projects: { type: Array, required: true },
})
</script>

<template>
    <section id="projects" class="section py-16 sm:py-24" aria-labelledby="projects-title">
        <SectionHeader :number="number" title="Projects" heading-id="projects-title" />

        <ul class="grid list-none grid-cols-1 gap-5 p-0 sm:grid-cols-2">
            <ProjectCard v-for="project in projects" :key="project.id" :project="project" />
        </ul>
    </section>
</template>
