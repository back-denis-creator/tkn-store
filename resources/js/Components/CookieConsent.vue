<template>
    <div
        v-if="visible"
        class="fixed inset-x-0 bottom-0 z-50 border-t border-gray-200 bg-white px-5 py-4 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]"
    >
        <div class="mx-auto flex max-w-[1200px] flex-col items-center gap-3 sm:flex-row sm:justify-between">
            <p class="text-sm text-gray-600">
                {{ $t('Cookie_Consent_Text', 'Ми використовуємо файли cookie, необхідні для роботи сайту та покращення вашого досвіду користування.') }}
            </p>
            <button
                type="button"
                class="h-10 w-full shrink-0 whitespace-nowrap bg-amber-400 px-6 font-medium text-black hover:bg-yellow-300 sm:w-auto"
                @click="accept"
            >
                {{ $t('Accept') }}
            </button>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'

// Only "necessary" cookies (session, CSRF) are in use today, so a single
// accept button is enough — no per-category toggles. Once analytics/marketing
// scripts are added, gate their injection on this same 'accepted' flag rather
// than adding a second consent mechanism.
const STORAGE_KEY = 'casanel_cookie_consent'

const visible = ref(false)

onMounted(() => {
    try {
        visible.value = localStorage.getItem(STORAGE_KEY) !== 'accepted'
    } catch (e) {
        visible.value = false
    }
})

const accept = () => {
    visible.value = false
    try {
        localStorage.setItem(STORAGE_KEY, 'accepted')
    } catch (e) {
        // Private browsing / storage disabled — nothing to persist, the
        // banner will just show again next visit.
    }
}
</script>
