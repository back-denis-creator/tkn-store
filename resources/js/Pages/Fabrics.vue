<script setup>
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { SwatchIcon, ChevronDownIcon } from '@heroicons/vue/24/outline';
import FabricOptionCard from '@/Components/FabricOptionCard.vue';

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
const hasSubGroup = (option) => option.sub_meta !== null && option.sub_meta !== undefined && option.sub_meta !== '';
const OTHER_SUBGROUP_ID = 'other';

const groupedFabrics = computed(() => {
    // Number(null) === 0 and 'Однотон' is group id 0 — an explicit hasGroup()
    // check keeps ungrouped options from silently landing in that group too.
    const groups = props.colorGroups.map((group) => {
        const options = props.fabricOptions.filter((option) => hasGroup(option) && Number(option.meta) === group.id);

        // Only some categories have subcategories (e.g. "Геометричні" does,
        // "Однотонні" doesn't) — those without one show a flat grid exactly
        // like before; those with one get a labeled sub-section per
        // subcategory, so hundreds of fabrics under one category (e.g.
        // "Ботаніка") aren't dumped into a single undifferentiated grid.
        let subGroups = null;
        if (group.subcategories?.length) {
            subGroups = group.subcategories
                .map((sub) => ({
                    id: `${group.id}-${sub.id}`,
                    name: sub.name,
                    options: options.filter((option) => hasSubGroup(option) && Number(option.sub_meta) === sub.id),
                }))
                .filter((sub) => sub.options.length);

            const withoutSubcategory = options.filter((option) => !hasSubGroup(option));
            if (withoutSubcategory.length) {
                subGroups.push({ id: `${group.id}-${OTHER_SUBGROUP_ID}`, name: 'Інше', options: withoutSubcategory });
            }
        }

        return { id: group.id, name: group.name, options, subGroups };
    });

    const ungrouped = props.fabricOptions.filter((option) => !hasGroup(option));
    if (ungrouped.length) {
        groups.push({ id: OTHER_GROUP_ID, name: 'Інше', options: ungrouped, subGroups: null });
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
                <div v-if="isGroupOpen(group.id)" class="mt-6">
                    <!-- Category has subcategories (e.g. "Геометричні") —
                         a labeled sub-section per subcategory. -->
                    <template v-if="group.subGroups">
                        <div v-for="subGroup in group.subGroups" :key="subGroup.id" class="mb-8 last:mb-0">
                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">
                                {{ subGroup.name }} <span class="font-normal text-gray-400">({{ subGroup.options.length }})</span>
                            </h3>
                            <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
                                <FabricOptionCard v-for="option in subGroup.options" :key="option.id" :option="option" />
                            </div>
                        </div>
                    </template>

                    <!-- Category has no subcategories (e.g. "Однотонні") —
                         the same flat grid as before. -->
                    <div v-else class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
                        <FabricOptionCard v-for="option in group.options" :key="option.id" :option="option" />
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
