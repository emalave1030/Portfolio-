<script setup>
defineProps({
    name: { type: String, required: true },
    visible: { type: Boolean, default: false },
})

defineEmits(['print'])
</script>

<template>
    <div
        class="topbar fixed inset-x-0 top-0 z-50 border-b border-line bg-paper/95 backdrop-blur"
        :class="visible ? 'topbar--visible' : 'topbar--hidden'"
        :aria-hidden="visible ? 'false' : 'true'"
        :inert="! visible || null"
    >
        <div class="mx-auto flex w-full max-w-[60rem] items-center justify-between gap-3 px-5 py-3 sm:gap-4 sm:px-8">
            <a href="#top" class="whitespace-nowrap font-display text-xs font-semibold uppercase tracking-[0.1em] text-ink no-underline sm:text-sm sm:tracking-[0.18em]">
                {{ name }}
            </a>

            <nav class="flex items-center gap-2" aria-label="Quick actions">
                <button type="button" class="btn btn--small" @click="$emit('print')">
                    Print / PDF
                </button>
            </nav>
        </div>
    </div>
</template>
