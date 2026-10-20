<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { usePortfolio } from '../composables/usePortfolio'
import TheTopbar from './TheTopbar.vue'
import TheHero from './TheHero.vue'
import TodaysLight from './TodaysLight.vue'
import AboutSection from './AboutSection.vue'
import ExperienceSection from './ExperienceSection.vue'
import SkillsSection from './SkillsSection.vue'
import ProjectsSection from './ProjectsSection.vue'
import ContactSection from './ContactSection.vue'

const { data, loading, error, reload } = usePortfolio()

const profile = computed(() => data.value?.profile ?? null)
const experiences = computed(() => data.value?.experiences ?? [])
const skillGroups = computed(() => data.value?.skill_groups ?? [])
const projects = computed(() => data.value?.projects ?? [])

/*
 * Which roles have their full bullet list open. Owned here, at the top, and
 * handed down as a prop: the timeline items render it and emit back, they
 * never hold it themselves.
 */
const expandedExperienceIds = ref([])

function toggleExperience(id) {
    expandedExperienceIds.value = expandedExperienceIds.value.includes(id)
        ? expandedExperienceIds.value.filter((current) => current !== id)
        : [...expandedExperienceIds.value, id]
}

/*
 * The topbar appears once the hero has been scrolled past. The sentinel only
 * exists after the payload arrives, so it is watched rather than read on
 * mount.
 */
const heroSentinel = ref(null)
const topbarVisible = ref(false)
let observer = null

watch(heroSentinel, (element) => {
    observer?.disconnect()

    if (! element || ! ('IntersectionObserver' in window)) {
        return
    }

    observer = new IntersectionObserver(([entry]) => {
        topbarVisible.value = ! entry.isIntersecting
    })

    observer.observe(element)
})

onBeforeUnmount(() => observer?.disconnect())

function print() {
    window.print()
}
</script>

<template>
    <TheTopbar
        v-if="profile"
        :name="profile.name"
        :visible="topbarVisible"
        @print="print"
    />

    <p v-if="loading" class="p-8 text-ink-dim" role="status">Loading…</p>

    <div v-else-if="error" class="p-8" role="alert">
        <p class="text-ink">{{ error }}</p>
        <button type="button" class="btn btn--small mt-4" @click="reload">Try again</button>
    </div>

    <template v-else-if="profile">
        <TheHero :profile="profile" />

        <div ref="heroSentinel" aria-hidden="true" class="h-px"></div>

        <TodaysLight />

        <main class="mx-auto w-full max-w-[60rem] px-5 sm:px-8">
            <AboutSection number="01" :paragraphs="profile.about" />

            <ExperienceSection
                number="02"
                :experiences="experiences"
                :expanded-ids="expandedExperienceIds"
                @toggle="toggleExperience"
            />

            <SkillsSection number="03" :groups="skillGroups" />

            <ProjectsSection number="04" :projects="projects" />

            <ContactSection number="05" :profile="profile" />
        </main>

        <footer class="mx-auto w-full max-w-[60rem] border-t border-line px-5 py-8 text-sm text-ink-faint sm:px-8">
            <p>{{ profile.name }} · {{ profile.location }}</p>
        </footer>
    </template>
</template>
