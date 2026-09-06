<template>
    <div v-if="announcement.enabled" class="overflow-hidden bg-amber-50 py-2">
        <div class="marquee flex w-max">
            <span
                v-for="group in 2"
                :key="group"
                class="flex shrink-0 items-center"
                :class="{ 'marquee__duplicate': group === 2 }"
                :aria-hidden="group === 2 ? 'true' : undefined"
            >
                <span
                    v-for="n in 6"
                    :key="n"
                    class="flex shrink-0 items-center gap-2 px-6 text-sm font-medium whitespace-nowrap text-amber-900"
                >
                    <GiftIcon class="h-4 w-4 text-amber-500" />
                    {{ bannerText }}
                </span>
            </span>
        </div>
    </div>
</template>
<script setup>
import { usePage } from '@inertiajs/vue3'
import { trans } from 'laravel-vue-i18n'
import { computed } from 'vue'
import { GiftIcon } from '@heroicons/vue/24/outline'

const page = usePage()
const announcement = computed(() => page.props.announcement)
// trans() (not the template-only $t global) since this runs in <script setup>
// — same reason Product.vue has to inject('route') instead of using route().
const bannerText = computed(() => announcement.value.mode === 'custom'
    ? announcement.value.custom_text
    : trans('Free_Shipping_Banner', { amount: announcement.value.threshold }))
</script>
<style scoped>
.marquee {
    animation: marquee 24s linear infinite;
}

@media (prefers-reduced-motion: reduce) {
    .marquee {
        animation: none;
    }

    .marquee__duplicate {
        display: none;
    }
}

@keyframes marquee {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(-50%);
    }
}
</style>
