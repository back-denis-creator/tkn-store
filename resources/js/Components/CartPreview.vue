<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    cartCount: {
        type: Number,
        default: 0,
    },
});

const items = ref([]);
const loading = ref(false);
const loaded = ref(false);

// Fetched on demand (not shared globally like cartCount) — most pages have
// no other reason to hydrate cart products from the DB on every request.
// Re-fetched on every hover so a change made elsewhere (Cart page, "add to
// cart") is always reflected, without needing a shared reactive cart store.
const fetchPreview = () => {
    if (loading.value) return;
    loading.value = true;
    window.axios.get(route('cart.preview'))
        .then(({ data }) => {
            items.value = data;
            loaded.value = true;
        })
        .finally(() => {
            loading.value = false;
        });
};

const total = computed(() => items.value.reduce((sum, item) => sum + item.price * item.quantity, 0));
</script>

<template>
    <div class="relative group" @mouseenter="fetchPreview">
        <Link
            :href="route('cart')"
            class="relative inline-flex items-center justify-center p-2 text-gray-700 hover:bg-gray-50 transition-all"
        >
            <i class="pi pi-shopping-bag text-lg"></i>
            <span
                class="absolute -top-1 -right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-amber-400 px-1 text-[10px] font-bold text-black"
            >{{ cartCount }}</span>
        </Link>

        <!-- Cart dropdown on hover -->
        <div
            class="absolute right-0 mt-0 w-80 pt-2 invisible opacity-0 translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 ease-out z-[60]"
        >
            <div class="bg-white rounded-md shadow-xl ring-1 ring-black ring-opacity-5 overflow-hidden border border-gray-100">
                <div v-if="loading && !loaded" class="flex items-center justify-center py-10">
                    <i class="pi pi-spin pi-spinner text-xl text-gray-300"></i>
                </div>

                <div v-else-if="loaded && !items.length" class="px-4 py-8 text-center text-sm text-gray-400">
                    Кошик порожній
                </div>

                <template v-else-if="items.length">
                    <div class="max-h-80 overflow-y-auto">
                        <div
                            v-for="item in items"
                            :key="item.sku_id"
                            class="flex gap-3 border-b border-gray-50 p-3 last:border-none"
                        >
                            <Link :href="route('product', item.slug)" class="h-14 w-14 shrink-0 overflow-hidden rounded-md border border-gray-100">
                                <img v-if="item.image" :src="item.image" :alt="item.name" class="h-full w-full object-cover" />
                            </Link>

                            <div class="min-w-0 flex-1">
                                <Link
                                    :href="route('product', item.slug)"
                                    class="block truncate text-sm font-medium text-gray-800 hover:text-amber-600"
                                >{{ item.name }}</Link>
                                <p v-if="item.attributes.length" class="mt-0.5 truncate text-xs text-gray-400">
                                    <template v-for="(attr, index) in item.attributes" :key="attr.name">{{ attr.value }}<span v-if="index < item.attributes.length - 1">, </span></template>
                                </p>
                                <p class="mt-1 text-xs text-gray-500">{{ item.quantity }} × {{ item.price }} грн</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-4 py-3">
                        <span class="text-sm font-medium text-gray-600">Разом</span>
                        <span class="text-sm font-bold text-gray-900">{{ total }} грн</span>
                    </div>

                    <Link
                        :href="route('cart')"
                        class="block bg-amber-400 px-4 py-2.5 text-center text-sm font-semibold text-black transition-colors hover:bg-amber-500"
                    >
                        Перейти в кошик
                    </Link>
                </template>
            </div>
        </div>
    </div>
</template>
