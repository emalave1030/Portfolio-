import { ref, shallowReadonly } from 'vue'

/*
 * Module-level state: these refs are created once when the module is first
 * imported, so every component that calls usePortfolio() reads the same
 * instance and the request happens exactly once per page load.
 */
const data = ref(null)
const loading = ref(false)
const error = ref(null)

let request = null

async function load() {
    loading.value = true
    error.value = null

    try {
        const response = await fetch('/api/portfolio', {
            headers: { Accept: 'application/json' },
        })

        if (!response.ok) {
            throw new Error(`The portfolio could not be loaded (HTTP ${response.status}).`)
        }

        data.value = await response.json()
    } catch (caught) {
        error.value = caught instanceof Error
            ? caught.message
            : 'The portfolio could not be loaded.'
    } finally {
        loading.value = false
    }
}

export function usePortfolio() {
    request ??= load()

    function reload() {
        request = load()

        return request
    }

    return {
        // Read-only: the payload is server-shaped and components never edit it.
        data: shallowReadonly(data),
        loading: shallowReadonly(loading),
        error: shallowReadonly(error),
        reload,
    }
}
