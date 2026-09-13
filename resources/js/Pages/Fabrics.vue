<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { SwatchIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    fabricOptions: {
        type: Array,
        default: () => ([]),
    },
    colorGroups: {
        type: Array,
        default: () => ([]),
    },
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

// Options with no group assigned land in their own trailing section instead
// of being silently dropped — an admin who forgets to pick a group should
// still see the fabric show up somewhere on this page.
const OTHER_GROUP_ID = 'other';

const hasGroup = (option) => option.meta !== null && option.meta !== undefined && option.meta !== '';

const groupedFabrics = computed(() => {
    // Number(null) === 0 and 'Однотон' is group id 0 — an explicit hasGroup()
    // check keeps ungrouped options from silently landing in that group too.
    const groups = props.colorGroups.map((group) => ({
        id: group.id,
        name: group.name,
        options: props.fabricOptions.filter((option) => hasGroup(option) && Number(option.meta) === group.id),
    }));

    const ungrouped = props.fabricOptions.filter((option) => !hasGroup(option));
    if (ungrouped.length) {
        groups.push({ id: OTHER_GROUP_ID, name: 'Інше', options: ungrouped });
    }

    return groups.filter((group) => group.options.length);
});
</script>

<template>
    <GuestLayout>
        <Head>
            <title>{{ $t('Fabrics') }}</title>
            <meta name="description" content="Каталог тканин Casanel: усі кольори та принти, згруповані за колекціями — однотон, мармур, геометрія та інші.">
            <link rel="canonical" :href="route('fabrics')">

            <meta property="og:title" :content="$t('Fabrics')">
            <meta property="og:description" content="Каталог тканин Casanel: усі кольори та принти, згруповані за колекціями.">
            <meta property="og:url" :content="route('fabrics')">

            <meta name="twitter:title" :content="$t('Fabrics')">
            <meta name="twitter:description" content="Каталог тканин Casanel: усі кольори та принти, згруповані за колекціями.">
        </Head>

        <template #header>
            <div class="relative overflow-hidden bg-gray-900 text-white">
                <div class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full bg-amber-400/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-32 -left-16 h-80 w-80 rounded-full bg-amber-400/10 blur-3xl"></div>
                <SwatchIcon
                    class="pointer-events-none absolute -right-10 bottom-0 hidden h-64 w-64 text-amber-400/10 lg:block xl:h-80 xl:w-80"
                    aria-hidden="true"
                />

                <div class="relative mx-auto max-w-[1200px] px-5 py-16 lg:py-24 text-center">
                    <h1 class="text-3xl font-bold sm:text-5xl lg:text-6xl mb-4">{{ $t('Fabrics') }}</h1>
                    <p class="text-lg sm:text-xl max-w-2xl mx-auto font-light leading-relaxed text-gray-300">
                        {{ $t('Fabrics_Subtitle') }}
                    </p>
                </div>
            </div>
        </template>

        <section class="mx-auto max-w-[1200px] px-5 py-20">
            <p v-if="!groupedFabrics.length" class="text-center text-gray-400">
                {{ $t('Fabrics_Empty') }}
            </p>

            <div v-for="group in groupedFabrics" :key="group.id" class="mb-16 last:mb-0">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                    <span class="w-8 h-1 bg-amber-400 rounded-full"></span>
                    {{ group.name }}
                </h2>

                <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
                    <div
                        v-for="option in group.options"
                        :key="option.id"
                        class="flex flex-col gap-3 rounded-2xl border border-gray-100 bg-gray-50 p-4 transition-shadow hover:shadow-md"
                    >
                        <div class="aspect-square w-full overflow-hidden rounded-xl border border-gray-200 bg-white">
                            <img
                                v-if="option.media?.[0]?.original_url"
                                :src="option.media[0].original_url"
                                :alt="option.value"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center text-gray-300">
                                <SwatchIcon class="h-10 w-10" />
                            </div>
                        </div>

                        <div>
                            <p class="font-semibold text-gray-900">{{ option.value }}</p>
                            <p v-if="option.description" class="mt-1 text-sm text-gray-500 leading-snug">{{ option.description }}</p>
                            <p v-if="option.article" class="mt-2 text-xs uppercase tracking-wide text-gray-400">{{ $t('Article') }}: {{ option.article }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
