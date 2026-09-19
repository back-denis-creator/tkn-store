<script setup>
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { SwatchIcon, ChevronDownIcon } from '@heroicons/vue/24/outline';

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

// Collapsed by default — hundreds of fabrics rendered open at once made the
// page unusable. Multiple groups can stay open at the same time.
const openGroupIds = ref(new Set());

const isGroupOpen = (id) => openGroupIds.value.has(id);

const toggleGroup = (id) => {
    const next = new Set(openGroupIds.value);
    next.has(id) ? next.delete(id) : next.add(id);
    openGroupIds.value = next;
};
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

            <div v-for="group in groupedFabrics" :key="group.id" class="mb-6 last:mb-0 border-b border-gray-100 last:border-0 pb-6 last:pb-0">
                <button
                    type="button"
                    class="w-full flex items-center justify-between gap-3 text-left group/header"
                    :aria-expanded="isGroupOpen(group.id)"
                    @click="toggleGroup(group.id)"
                >
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <span class="w-8 h-1 bg-amber-400 rounded-full"></span>
                        {{ group.name }}
                        <span class="text-sm font-medium text-gray-400">({{ group.options.length }})</span>
                    </h2>
                    <ChevronDownIcon
                        class="h-6 w-6 shrink-0 text-gray-400 transition-transform duration-200 group-hover/header:text-gray-600"
                        :class="{ 'rotate-180': isGroupOpen(group.id) }"
                    />
                </button>

                <!-- v-if, not v-show — a collapsed group's images must not
                     load at all with hundreds of fabrics on the page. -->
                <div v-if="isGroupOpen(group.id)" class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4 mt-6">
                    <div
                        v-for="option in group.options"
                        :key="option.id"
                        class="group flex flex-col gap-3 rounded-2xl border border-gray-100 bg-gray-50 p-4 transition-shadow hover:shadow-md"
                    >
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

                            <!-- Descriptions repeat across many fabrics — showing
                                 them all at once is just noise. Reveal one only
                                 on hover, as an overlay, so it never pushes the
                                 grid around. -->
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
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
