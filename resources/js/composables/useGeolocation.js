import { ref, shallowReadonly } from 'vue'

/*
 * Where the visitor is, to the nearest kilometre or so. Sun times barely
 * change across a city, so a coarse, fast fix beats a precise, slow one,
 * and two decimals is all that gets remembered.
 *
 * The rounded position is kept in this browser's storage so a return visit
 * shows the visitor's own light at once, with no prompt. It is never sent
 * anywhere.
 */
const STORAGE_KEY = 'todays-light:place'

function round(value) {
    return Math.round(value * 100) / 100
}

function remembered() {
    try {
        const stored = JSON.parse(localStorage.getItem(STORAGE_KEY))

        return Number.isFinite(stored?.lat) && Number.isFinite(stored?.lng) ? stored : null
    } catch {
        return null
    }
}

export function useGeolocation() {
    const position = ref(remembered())
    const status = ref('idle')
    const error = ref(null)

    const MESSAGES = {
        1: 'Location was declined, so this shows Orlando.',
        2: 'Your device could not work out where it is. Try again in a moment.',
        3: 'That took too long. Try again.',
    }

    function locate() {
        if (! ('geolocation' in navigator)) {
            status.value = 'error'
            error.value = 'This browser cannot share its location.'

            return
        }

        status.value = 'locating'
        error.value = null

        navigator.geolocation.getCurrentPosition(
            ({ coords }) => {
                position.value = { lat: round(coords.latitude), lng: round(coords.longitude) }
                status.value = 'idle'

                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(position.value))
                } catch {
                    // Not remembered this time; the section still works.
                }
            },
            (failure) => {
                status.value = 'error'
                error.value = MESSAGES[failure.code] ?? 'Your location could not be read.'
            },
            { enableHighAccuracy: false, maximumAge: 600000, timeout: 15000 },
        )
    }

    function forget() {
        position.value = null
        error.value = null

        try {
            localStorage.removeItem(STORAGE_KEY)
        } catch {
            // Nothing stored to remove.
        }
    }

    return {
        position: shallowReadonly(position),
        status: shallowReadonly(status),
        error: shallowReadonly(error),
        locate,
        forget,
    }
}
