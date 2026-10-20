<script setup>
import { computed, onBeforeUnmount, ref } from 'vue'
import { compassPoint, sunPath, sunPosition, sunTimes } from '../geo/sun'
import { useGeolocation } from '../composables/useGeolocation'

/*
 * Until a visitor shares their location, the light is Orlando's, shown in
 * Orlando's time: everyone sees something live on first paint, with no prompt.
 */
const HOME = { name: 'Orlando, FL', lat: 28.54, lng: -81.38, timeZone: 'America/New_York' }

const visitor = useGeolocation()

const place = computed(() => (visitor.position.value
    ? { name: 'Your location', ...visitor.position.value, timeZone: undefined }
    : HOME))

// The sun moves about a degree every four minutes; half a minute is plenty.
const now = ref(new Date())
const clock = setInterval(() => (now.value = new Date()), 30000)

onBeforeUnmount(() => clearInterval(clock))

const times = computed(() => sunTimes(now.value, place.value.lat, place.value.lng))
const position = computed(() => sunPosition(now.value, place.value.lat, place.value.lng))
const isUp = computed(() => position.value.altitude > -0.833)

/*
 * The dial is a polar projection of the sky: the horizon is the rim, the
 * zenith is the centre, and azimuth is the angle with north at the top.
 */
const DIAL_RADIUS = 50

function project({ azimuth, altitude }) {
    const radius = DIAL_RADIUS * (1 - Math.max(altitude, 0) / 90)
    const angle = (azimuth * Math.PI) / 180

    return { x: radius * Math.sin(angle), y: -radius * Math.cos(angle) }
}

const pathPoints = computed(() => sunPath(times.value, place.value.lat, place.value.lng)
    .map((point) => {
        const { x, y } = project(point)

        return `${x.toFixed(1)},${y.toFixed(1)}`
    })
    .join(' '))

const sunDot = computed(() => project(position.value))

const formatter = computed(() => new Intl.DateTimeFormat(undefined, {
    hour: 'numeric',
    minute: '2-digit',
    timeZone: place.value.timeZone,
}))

const format = (date) => (date === null ? '—' : formatter.value.format(date))
const range = (start, end) => (start === null || end === null ? '—' : `${format(start)} – ${format(end)}`)

const periods = computed(() => {
    const t = times.value

    return [
        { name: 'Blue hour', detail: 'Morning', value: range(t.dawnBlue, t.dawnGolden), tone: 'bg-teal' },
        { name: 'Golden hour', detail: 'Morning', value: range(t.dawnGolden, t.morningGoldenEnd), tone: 'bg-mustard' },
        { name: 'Sunrise', detail: null, value: format(t.sunrise), tone: 'bg-ink' },
        { name: 'Sunset', detail: null, value: format(t.sunset), tone: 'bg-ink' },
        { name: 'Golden hour', detail: 'Evening', value: range(t.eveningGoldenStart, t.duskGolden), tone: 'bg-mustard' },
        { name: 'Blue hour', detail: 'Evening', value: range(t.duskGolden, t.duskBlue), tone: 'bg-teal' },
    ]
})

/** The next thing worth stepping outside for, with a countdown. */
const nextEvent = computed(() => {
    const t = times.value
    const upcoming = [
        ['Morning blue hour', t.dawnBlue],
        ['Morning golden hour', t.dawnGolden],
        ['Sunrise', t.sunrise],
        ['Evening golden hour', t.eveningGoldenStart],
        ['Sunset', t.sunset],
        ['Evening blue hour', t.duskGolden],
    ].find(([, at]) => at !== null && at > now.value)

    if (! upcoming) {
        return null
    }

    const minutes = Math.round((upcoming[1] - now.value) / 60000)
    const hours = Math.floor(minutes / 60)

    return {
        name: upcoming[0],
        countdown: hours > 0 ? `${hours} h ${minutes % 60} min` : `${minutes} min`,
    }
})

const coordinates = computed(() => {
    const { lat, lng } = place.value

    return `${Math.abs(lat).toFixed(2)}° ${lat >= 0 ? 'N' : 'S'}, ${Math.abs(lng).toFixed(2)}° ${lng >= 0 ? 'E' : 'W'}`
})
</script>

<template>
    <section id="light" class="light-band bg-ink py-12 text-paper sm:py-16" aria-labelledby="light-title">
        <div class="mx-auto w-full max-w-[60rem] px-5 sm:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="light-label flex items-center gap-2">
                        <span class="live-dot" aria-hidden="true"></span>
                        Live · {{ place.name }}
                    </p>
                    <h2 id="light-title" class="mt-2 text-[clamp(1.75rem,6vw,2.5rem)] font-medium">Today's light</h2>
                </div>

                <button
                    v-if="! visitor.position.value"
                    type="button"
                    class="btn btn--on-dark btn--small"
                    :disabled="visitor.status.value === 'locating'"
                    @click="visitor.locate"
                >
                    {{ visitor.status.value === 'locating' ? 'Finding you…' : 'Use my location' }}
                </button>
                <button v-else type="button" class="btn btn--on-dark btn--small" @click="visitor.forget">
                    Back to Orlando
                </button>
            </div>

            <p v-if="visitor.error.value" class="mt-3 text-sm text-mustard" role="alert">{{ visitor.error.value }}</p>

            <div class="mt-8 grid grid-cols-1 gap-8 sm:grid-cols-[auto_1fr] sm:gap-12">
                <div class="flex items-center gap-6">
                    <svg viewBox="-62 -62 124 124" class="h-36 w-36 shrink-0" role="img" :aria-label="`Sky dial: the sun is ${isUp ? 'up' : 'below the horizon'}`">
                        <circle r="50" class="fill-none stroke-paper" stroke-width="2" />
                        <circle r="33.3" class="fill-none stroke-paper/25" stroke-width="1" />
                        <circle r="16.7" class="fill-none stroke-paper/25" stroke-width="1" />
                        <text y="-53" text-anchor="middle" class="fill-paper text-[9px] font-semibold">N</text>
                        <text x="56" y="3" text-anchor="middle" class="fill-paper/70 text-[8px]">E</text>
                        <text y="61" text-anchor="middle" class="fill-paper/70 text-[8px]">S</text>
                        <text x="-56" y="3" text-anchor="middle" class="fill-paper/70 text-[8px]">W</text>
                        <polyline v-if="pathPoints" :points="pathPoints" class="fill-none stroke-mustard/70" stroke-width="2" stroke-dasharray="3 3" />
                        <circle
                            :cx="sunDot.x"
                            :cy="sunDot.y"
                            r="7"
                            class="stroke-paper"
                            :class="isUp ? 'fill-mustard' : 'fill-ink'"
                            stroke-width="2"
                        />
                    </svg>

                    <div>
                        <p class="readout text-4xl">{{ position.altitude.toFixed(0) }}°</p>
                        <p class="text-paper/80">
                            {{ isUp ? 'above' : 'below' }} the horizon,<br>
                            toward {{ compassPoint(position.azimuth) }}
                        </p>
                        <p v-if="nextEvent" class="mt-3 text-sm">
                            <span class="font-semibold text-mustard">{{ nextEvent.name }}</span>
                            in {{ nextEvent.countdown }}
                        </p>
                    </div>
                </div>

                <ul class="m-0 grid list-none grid-cols-1 gap-x-8 p-0 sm:grid-cols-2">
                    <li
                        v-for="period in periods"
                        :key="period.name + period.detail"
                        class="flex items-baseline justify-between gap-3 border-b border-paper/20 py-2"
                    >
                        <span class="flex flex-wrap items-center gap-x-2">
                            <span class="inline-block h-2.5 w-2.5 shrink-0 rounded-full border border-paper" :class="period.tone" aria-hidden="true"></span>
                            <span class="font-medium">{{ period.name }}</span>
                            <span v-if="period.detail" class="text-sm text-paper/70">{{ period.detail }}</span>
                        </span>
                        <span class="readout text-lg whitespace-nowrap">{{ period.value }}</span>
                    </li>
                </ul>
            </div>

            <details class="mt-8 text-sm text-paper/80">
                <summary class="cursor-pointer font-medium text-paper">How this is worked out</summary>
                <p class="mt-3 max-w-[46rem]">
                    From {{ coordinates }} and the current time, the sun's position is computed in your
                    browser: its ecliptic longitude gives its declination, the hour angle at this longitude
                    turns that into altitude and azimuth, and the times come from solving for the moments
                    the sun crosses 6°, −0.833°, −4° and −6°. The dial is a polar projection of the sky,
                    with the horizon at the rim and the zenith at the centre. No map service, no API,
                    and your location never leaves this device.
                </p>
            </details>
        </div>
    </section>
</template>
