<script setup>
defineProps({
    project: { type: Object, required: true },
})
</script>

<template>
    <li class="project-card flex flex-col p-6">
        <h3 class="text-xl font-semibold tracking-[0.04em] text-ink">{{ project.title }}</h3>

        <ul class="mt-3 flex list-none flex-wrap gap-2 p-0">
            <li v-for="item in project.stack" :key="item" class="chip">{{ item }}</li>
        </ul>

        <p class="mt-4 grow text-ink-dim">{{ project.description }}</p>

        <p class="mt-5">
            <a
                v-if="project.link"
                class="link-underline font-medium"
                :href="project.link"
                target="_blank"
                rel="noopener noreferrer"
            >
                {{ project.link_label }}
                <span class="sr-only"> — {{ project.title }} (opens in a new tab)</span>
                <span aria-hidden="true"> ↗</span>
            </a>
            <span v-else class="text-sm text-ink-dim">No public link yet</span>
        </p>
    </li>
</template>
