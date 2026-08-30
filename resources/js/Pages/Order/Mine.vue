<template>
    <GuestLayout>
        <Head title="Мої замовлення">
            <meta name="robots" content="noindex, nofollow" />
        </Head>

        <section class="container mx-auto max-w-[1200px] px-5 py-5 lg:py-10">
            <h1 class="mb-6 text-2xl font-bold uppercase">Мої замовлення</h1>

            <p v-if="!orders.data.length" class="text-gray-500">
                У вас ще немає замовлень.
                <Link :href="route('catalog')" class="text-amber-600 underline">Перейти до каталогу</Link>
            </p>

            <div v-else class="flex flex-col gap-4">
                <div v-for="order in orders.data" :key="order.uuid" class="border rounded-lg p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b pb-3 mb-3">
                        <div>
                            <p class="font-medium">Замовлення №{{ order.uuid.slice(0, 8) }}</p>
                            <p class="text-sm text-gray-500">{{ new Date(order.created_at).toLocaleString('uk-UA') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-amber-600">{{ order.status_name }}</p>
                            <p class="text-sm text-gray-500">{{ order.payment_name }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1">
                        <div v-for="(item, index) in order.items" :key="index" class="flex justify-between text-sm">
                            <span>
                                {{ item.product_name }}
                                <span v-if="item.attributes_summary" class="text-gray-400">({{ item.attributes_summary }})</span>
                                × {{ item.quantity }}
                            </span>
                            <span>{{ item.price * item.quantity }} грн.</span>
                        </div>
                    </div>

                    <div class="flex justify-between font-medium pt-3 mt-3 border-t">
                        <span>Сума</span>
                        <span>{{ order.total_amount }} грн.</span>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="orders.data.length" class="flex items-center justify-between mt-6">
                <Link
                    :class="{ 'pointer-events-none opacity-40': !orders.prev_page_url }"
                    :href="orders.prev_page_url || ''"
                    class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50"
                >
                    Назад
                </Link>
                <Link
                    :class="{ 'pointer-events-none opacity-40': !orders.next_page_url }"
                    :href="orders.next_page_url || ''"
                    class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50"
                >
                    Наступна
                </Link>
            </div>
        </section>
    </GuestLayout>
</template>
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    orders: {
        type: Object,
        required: true,
    },
})
</script>
