<script setup>
import { SwatchIcon } from '@heroicons/vue/24/outline';

defineProps({
    option: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="group flex flex-col gap-3 rounded-2xl border border-gray-100 bg-gray-50 p-4 transition-shadow hover:shadow-md">
        <div class="relative aspect-square w-full overflow-hidden rounded-xl border border-gray-200 bg-white">
            <img
                v-if="option.media?.[0]?.original_url"
                :src="option.media[0].original_url"
                :alt="option.value"
                loading="lazy"
                class="h-full w-full object-cover"
            />
            <div v-else class="flex h-full w-full items-center justify-center text-gray-300">
                <SwatchIcon class="h-10 w-10" />
            </div>

            <!-- Descriptions repeat across many fabrics — showing them all at
                 once is just noise. Reveal one only on hover, as an overlay,
                 so it never pushes the grid around. -->
            <div
                v-if="option.description"
                class="absolute inset-0 flex items-end bg-gradient-to-t from-black/85 via-black/40 to-transparent p-3 opacity-0 translate-y-1 transition-all duration-300 ease-out group-hover:opacity-100 group-hover:translate-y-0"
            >
                <p class="text-sm text-white leading-snug">{{ option.description }}</p>
            </div>
        </div>

        <div>
            <p class="font-semibold text-gray-900">{{ option.value }}</p>
            <p v-if="option.article" class="mt-2 text-xs uppercase tracking-wide text-gray-400">{{ $t('Article') }}: {{ option.article }}</p>
        </div>
    </div>
</template>
