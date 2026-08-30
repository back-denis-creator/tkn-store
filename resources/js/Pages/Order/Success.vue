<template>
    <GuestLayout>
        <Head title="Дякуємо за замовлення">
            <meta name="robots" content="noindex, nofollow" />
        </Head>

        <section class="container mx-auto max-w-[800px] px-5 py-16 text-center">
            <h1 class="text-3xl font-bold mb-4">Дякуємо за замовлення!</h1>
            <p class="text-gray-500 mb-8">
                Номер замовлення: <span class="font-medium text-gray-800">{{ order.uuid.slice(0, 8) }}</span>
            </p>

            <div class="border rounded-lg p-6 text-left mx-auto max-w-[400px]">
                <div class="flex justify-between py-2 border-b">
                    <span>Статус</span>
                    <span class="font-medium">{{ order.status_name }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span>Оплата</span>
                    <span class="font-medium">{{ order.payment_name }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span>Сума</span>
                    <span class="font-medium">{{ order.total_amount }} грн.</span>
                </div>
            </div>

            <p v-if="isAwaitingPayment" class="mt-6 text-sm text-gray-500">
                Якщо оплата не підтвердилась автоматично протягом кількох хвилин, зверніться до нас — ми перевіримо вручну.
            </p>

            <Link :href="route('home')" class="inline-block mt-8 text-amber-600 underline">
                На головну
            </Link>
        </section>
    </GuestLayout>
</template>
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
})

const isAwaitingPayment = computed(() => !props.order.paid_at && props.order.payment_method === 4)
</script>
