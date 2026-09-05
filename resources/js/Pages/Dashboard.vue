<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        required: true,
    },
    recentOrders: {
        type: Array,
        default: () => [],
    },
});

const cards = (stats) => [
    { label: 'Товари', value: stats.products, route: 'products.index', icon: 'pi-box' },
    { label: 'Категорії', value: stats.categories, route: 'categories.index', icon: 'pi-tags' },
    { label: 'Атрибути', value: stats.attributes, route: 'attributes.index', icon: 'pi-sliders-h' },
    { label: 'Нові замовлення', value: stats.newOrders, route: 'orders.index', icon: 'pi-shopping-bag', highlight: stats.newOrders > 0 },
];

// Order::STATUS_* constants (app/Models/Order.php) — keep in sync.
const STATUS_BADGE_CLASS = {
    1: 'bg-blue-50 text-blue-700',      // New
    2: 'bg-amber-50 text-amber-700',    // Processing
    3: 'bg-indigo-50 text-indigo-700',  // Shipped
    4: 'bg-green-50 text-green-700',    // Completed
    5: 'bg-red-50 text-red-700',        // Cancelled
};
const statusBadgeClass = (status) => STATUS_BADGE_CLASS[status] || 'bg-gray-100 text-gray-600';
</script>

<template>
    <Head title="Кабінет">
        <meta name="robots" content="noindex, nofollow" />
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Кабінет</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Link
                        v-for="card in cards(stats)"
                        :key="card.label"
                        :href="route(card.route)"
                        class="flex items-center gap-4 rounded-sm border bg-white p-5 shadow-sm transition-colors hover:border-amber-300"
                        :class="card.highlight ? 'border-amber-300 bg-amber-50' : 'border-gray-100'"
                    >
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full"
                            :class="card.highlight ? 'bg-amber-400 text-black' : 'bg-gray-100 text-gray-500'"
                        >
                            <i :class="`pi ${card.icon} text-lg`"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold leading-none">{{ card.value }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ card.label }}</p>
                        </div>
                    </Link>
                </div>

                <div class="rounded-sm border border-gray-100 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b px-5 py-4">
                        <h3 class="font-medium text-gray-800">Останні замовлення</h3>
                        <Link :href="route('orders.index')" class="text-sm text-amber-600 hover:underline">
                            Усі замовлення
                        </Link>
                    </div>

                    <p v-if="!recentOrders.length" class="px-5 py-8 text-center text-sm text-gray-500">
                        Замовлень ще немає.
                    </p>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-wide text-gray-400">
                                    <th class="px-5 py-3 font-medium">№</th>
                                    <th class="px-5 py-3 font-medium">Клієнт</th>
                                    <th class="px-5 py-3 font-medium">Сума</th>
                                    <th class="px-5 py-3 font-medium">Статус</th>
                                    <th class="px-5 py-3 font-medium">Дата</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr
                                    v-for="order in recentOrders"
                                    :key="order.uuid"
                                    class="cursor-pointer hover:bg-gray-50"
                                    @click="router.visit(route('orders.show', order.uuid))"
                                >
                                    <td class="px-5 py-3 text-sm font-medium">{{ order.uuid.slice(0, 8) }}</td>
                                    <td class="px-5 py-3 text-sm">{{ order.customer_name }}</td>
                                    <td class="px-5 py-3 text-sm">{{ order.total_amount }} грн</td>
                                    <td class="px-5 py-3 text-sm">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap" :class="statusBadgeClass(order.status)">
                                            {{ order.status_name }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-500">
                                        {{ new Date(order.created_at).toLocaleDateString('uk-UA') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
