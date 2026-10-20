<script setup>
import SectionHeader from './SectionHeader.vue'
import ExperienceItem from './ExperienceItem.vue'

defineProps({
    number: { type: String, required: true },
    experiences: { type: Array, required: true },
    expandedIds: { type: Array, required: true },
})

defineEmits(['toggle'])
</script>

<template>
    <section id="experience" class="section py-16 sm:py-24" aria-labelledby="experience-title">
        <SectionHeader :number="number" title="Experience" heading-id="experience-title" />

        <div class="relative">
            <!-- The rail runs behind the dots; decorative, so hidden from AT.
                 It sits outside the list, which may only contain list items. -->
            <span class="timeline-rail absolute left-[7px] top-2 bottom-2 w-px" aria-hidden="true"></span>

            <ol class="m-0 list-none p-0">
                <ExperienceItem
                    v-for="experience in experiences"
                    :key="experience.id"
                    :experience="experience"
                    :expanded="expandedIds.includes(experience.id)"
                    @toggle="$emit('toggle', experience.id)"
                />
            </ol>
        </div>
    </section>
</template>
