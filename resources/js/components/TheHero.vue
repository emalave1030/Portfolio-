<script setup>
import headshot from '../../images/headshot.jpg'

defineProps({
    profile: { type: Object, required: true },
})

// Twelve rays for the atomic starburst, every 30°.
const rays = Array.from({ length: 12 }, (_, index) => index * 30)
</script>

<template>
    <header id="top" class="flex min-h-svh flex-col justify-center overflow-hidden py-20">
        <div class="mx-auto w-full max-w-[60rem] px-5 sm:px-8">
            <div class="flex flex-col gap-10 lg:flex-row-reverse lg:items-center lg:justify-between">
                <!-- The portrait sits in the mid-century composition: an offset
                     mustard disc, a teal disc and a starburst behind a round
                     crop. Screen only; the printed résumé leaves it out. -->
                <div class="portrait relative h-32 w-32 shrink-0 sm:h-40 sm:w-40 lg:h-72 lg:w-72">
                    <span class="absolute inset-0 translate-x-3 -translate-y-3 rounded-full bg-mustard lg:translate-x-6 lg:-translate-y-6" aria-hidden="true"></span>
                    <span class="absolute -bottom-2 -left-3 h-1/2 w-1/2 rounded-full bg-teal lg:-bottom-4 lg:-left-8" aria-hidden="true"></span>
                    <svg
                        viewBox="-32 -32 64 64"
                        class="absolute -top-6 -left-6 hidden h-16 w-16 lg:block"
                        aria-hidden="true"
                    >
                        <g class="stroke-ink" stroke-width="3" stroke-linecap="round">
                            <line v-for="angle in rays" :key="angle" x1="0" y1="8" x2="0" y2="28" :transform="`rotate(${angle})`" />
                        </g>
                        <circle r="5" class="fill-orange" />
                    </svg>
                    <img
                        :src="headshot"
                        :alt="`Portrait of ${profile.name}`"
                        width="720"
                        height="720"
                        fetchpriority="high"
                        class="relative h-full w-full rounded-full border-2 border-ink object-cover object-[50%_30%]"
                    >
                </div>

                <div>
                    <p class="eyebrow">{{ profile.eyebrow }}</p>

                    <h1 class="display-name mt-6">
                        {{ profile.given_name }}
                        <span class="text-orange">{{ profile.family_name }}</span>
                    </h1>

                    <p class="mt-6 max-w-2xl font-display text-2xl font-medium uppercase tracking-[0.08em] text-ink-dim sm:text-3xl">
                        {{ profile.title }}
                    </p>

                    <p class="print-contact mt-2">
                        {{ profile.email }} · {{ profile.site }}<template v-for="link in profile.links" :key="link.label"> · {{ link.display }}</template>
                    </p>
                </div>
            </div>

            <dl class="hero-stats mt-12 grid grid-cols-1 gap-6 border-t-2 border-ink pt-8 sm:grid-cols-3 sm:gap-8">
                <!-- dt precedes dd in the markup; the stat is flipped visually in CSS. -->
                <div v-for="stat in profile.stats" :key="stat.label" class="stat">
                    <dt class="eyebrow">{{ stat.label }}</dt>
                    <dd
                        class="m-0 font-display font-semibold uppercase leading-none text-ink"
                        :class="stat.value.length > 12 ? 'text-lg sm:text-xl' : 'text-4xl sm:text-5xl'"
                    >
                        {{ stat.value }}
                    </dd>
                </div>
            </dl>

            <div class="hero-actions mt-12 flex flex-col gap-3 sm:flex-row sm:gap-4">
                <a class="btn btn--primary" href="#experience">See the work</a>
                <a class="btn" :href="profile.mailto">Get in touch</a>
            </div>

            <ul v-if="profile.links.length" class="hero-actions mt-6 flex list-none flex-wrap gap-x-6 gap-y-2 p-0">
                <li v-for="link in profile.links" :key="link.label">
                    <a class="link-underline font-medium" :href="link.url" target="_blank" rel="noopener noreferrer">
                        {{ link.label }}<span class="sr-only"> (opens in a new tab)</span><span aria-hidden="true"> ↗</span>
                    </a>
                </li>
            </ul>
        </div>
    </header>
</template>
