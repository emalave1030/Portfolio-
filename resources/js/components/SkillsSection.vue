<script setup>
import SectionHeader from './SectionHeader.vue'

defineProps({
    number: { type: String, required: true },
    groups: { type: Array, required: true },
})
</script>

<template>
    <section id="skills" class="section py-16 sm:py-24" aria-labelledby="skills-title">
        <SectionHeader :number="number" title="Skills" heading-id="skills-title" />

        <div class="space-y-8">
            <div v-for="group in groups" :key="group.name">
                <h3
                    class="text-sm font-semibold tracking-[0.18em]"
                    :class="group.is_highlighted ? 'text-teal' : 'text-ink-faint'"
                >
                    {{ group.name }}
                </h3>

                <ul class="mt-3 flex list-none flex-wrap gap-2 p-0">
                    <li
                        v-for="skill in group.items"
                        :key="skill.id"
                        class="chip"
                        :class="{ 'chip--highlighted': group.is_highlighted }"
                    >
                        {{ skill.name }}
                    </li>
                </ul>
            </div>
        </div>
    </section>
</template>
