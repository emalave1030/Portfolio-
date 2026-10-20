<script setup>
import { onBeforeUnmount, ref } from 'vue'

const props = defineProps({
    number: { type: String, required: true },
    profile: { type: Object, required: true },
})

/*
 * The mailto link does nothing on a machine with no mail app set up, which
 * is most work laptops on webmail. Copying always works.
 */
const copied = ref(false)
let resetTimer = null

async function copyEmail() {
    try {
        await navigator.clipboard.writeText(props.profile.email)
        copied.value = true
        clearTimeout(resetTimer)
        resetTimer = setTimeout(() => (copied.value = false), 2000)
    } catch {
        // Clipboard blocked: the address is on screen and selectable.
    }
}

onBeforeUnmount(() => clearTimeout(resetTimer))
</script>

<template>
    <section id="contact" class="section py-16 sm:py-24" aria-labelledby="contact-title">
        <header class="mb-10 border-t border-line pt-6">
            <p class="section-number">{{ number }}</p>
            <h2 id="contact-title" class="section-title mt-2">Contact</h2>
        </header>

        <div class="flex flex-wrap items-center gap-x-5 gap-y-3">
            <a class="contact-email font-display font-semibold text-orange no-underline" :href="profile.mailto">
                {{ profile.email }}
            </a>

            <button type="button" class="btn btn--small" @click="copyEmail">
                {{ copied ? 'Copied!' : 'Copy email' }}
            </button>

            <span class="sr-only" aria-live="polite">{{ copied ? 'Email address copied' : '' }}</span>
        </div>

        <dl class="mt-10 grid grid-cols-1 gap-6 text-ink-dim sm:grid-cols-3">
            <div>
                <dt class="eyebrow">Location</dt>
                <dd class="mt-1">{{ profile.location }}</dd>
            </div>
            <div>
                <dt class="eyebrow">Languages</dt>
                <dd class="mt-1">{{ profile.languages }}</dd>
            </div>
            <div>
                <dt class="eyebrow">Site</dt>
                <dd class="mt-1">
                    <a class="link-underline" :href="profile.site_url">{{ profile.site }}</a>
                </dd>
            </div>
            <div v-for="link in profile.links" :key="link.label">
                <dt class="eyebrow">{{ link.label }}</dt>
                <dd class="mt-1 break-words">
                    <a class="link-underline" :href="link.url" target="_blank" rel="noopener noreferrer">
                        {{ link.display }}<span class="sr-only"> (opens in a new tab)</span>
                    </a>
                </dd>
            </div>
        </dl>
    </section>
</template>
