<script setup>
import { computed } from 'vue'

const props = defineProps({
    experience: { type: Object, required: true },
    expanded: { type: Boolean, default: false },
})

defineEmits(['toggle'])

// Roles lead with four bullets; the rest stay in the DOM so print can show
// them and screen readers can reach them through the toggle.
const VISIBLE_BULLETS = 4

const hiddenCount = computed(() => Math.max(props.experience.bullets.length - VISIBLE_BULLETS, 0))

const bulletsId = computed(() => `experience-${props.experience.id}-bullets`)

function isHidden(index) {
    return ! props.expanded && index >= VISIBLE_BULLETS
}
</script>

<template>
    <li class="experience-item relative pb-12 pl-8 last:pb-0 sm:pl-10">
        <span
            class="timeline-dot absolute left-0 top-[0.55rem] block h-4 w-4 rounded-full"
            :class="{ 'timeline-dot--current': experience.is_current }"
            aria-hidden="true"
        ></span>

        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
            <h3 class="text-xl font-semibold tracking-[0.04em] text-ink sm:text-2xl">
                {{ experience.role }}
            </h3>
            <span v-if="experience.is_current" class="badge-current">Current</span>
        </div>

        <p class="mt-1 text-ink-dim">
            <span class="font-medium text-ink">{{ experience.company }}</span>
            <span class="text-ink-faint"> · {{ experience.location }}</span>
        </p>

        <p class="eyebrow mt-2">{{ experience.date_range }}</p>

        <ul :id="bulletsId" class="mt-5 space-y-3 text-ink-dim">
            <li
                v-for="(bullet, index) in experience.bullets"
                :key="index"
                class="relative pl-5"
                :class="{ 'bullet--hidden': isHidden(index) }"
            >
                <span class="absolute left-0 top-[0.6em] block h-1 w-2 bg-line" aria-hidden="true"></span>
                {{ bullet }}
            </li>
        </ul>

        <button
            v-if="hiddenCount > 0"
            type="button"
            class="btn btn--small mt-5"
            :aria-expanded="expanded"
            :aria-controls="bulletsId"
            @click="$emit('toggle')"
        >
            <span v-if="expanded">Show less</span>
            <span v-else>+{{ hiddenCount }} more</span>
            <span class="sr-only"> for {{ experience.role }} at {{ experience.company }}</span>
        </button>
    </li>
</template>
