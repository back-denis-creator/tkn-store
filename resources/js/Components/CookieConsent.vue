<template>
    <div
        v-if="visible"
        class="fixed inset-x-0 bottom-0 z-50 border-t border-gray-200 bg-white px-5 py-4 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]"
    >
        <div class="mx-auto flex max-w-[1200px] flex-col items-center gap-3 sm:flex-row sm:justify-between">
            <p class="text-sm text-gray-600">
                {{ $t('Cookie_Consent_Text', 'Ми використовуємо файли cookie, необхідні для роботи сайту та покращення вашого досвіду користування.') }}
            </p>
            <div class="flex w-full shrink-0 gap-2 sm:w-auto">
                <button
                    type="button"
                    class="h-10 flex-1 whitespace-nowrap border border-gray-300 px-6 font-medium text-gray-700 hover:bg-gray-50 sm:flex-none"
                    @click="decline"
                >
                    {{ $t('Decline') }}
                </button>
                <button
                    type="button"
                    class="h-10 flex-1 whitespace-nowrap bg-amber-400 px-6 font-medium text-black hover:bg-yellow-300 sm:flex-none"
                    @click="accept"
                >
                    {{ $t('Accept') }}
                </button>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { loadGoogleAnalytics } from '@/analytics'

const STORAGE_KEY = 'casanel_cookie_consent'

const visible = ref(false)

onMounted(() => {
    try {
        const consent = localStorage.getItem(STORAGE_KEY)
        visible.value = consent === null
        // Returning visitor who already accepted on a previous visit — start
        // analytics on this page load too, not just right after the click.
        if (consent === 'accepted') {
            loadGoogleAnalytics()
        }
    } catch (e) {
        visible.value = false
    }
})

const setConsent = (value) => {
    visible.value = false
    try {
        localStorage.setItem(STORAGE_KEY, value)
    } catch (e) {
        // Private browsing / storage disabled — nothing to persist, the
        // banner will just show again next visit.
    }
}

const accept = () => {
    setConsent('accepted')
    loadGoogleAnalytics()
}
const decline = () => setConsent('declined')
</script>
