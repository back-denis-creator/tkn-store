<template>
    <Head :title="`Замовлення №${order.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Замовлення №{{ order.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="font-bold mb-3">Клієнт</h3>
                            <p>{{ order.customer_name }} {{ order.customer_surname }}</p>
                            <p>{{ order.customer_phone }}</p>
                            <p v-if="order.customer_email">{{ order.customer_email }}</p>
                            <p v-if="order.comment" class="mt-2 text-gray-500">Коментар: {{ order.comment }}</p>

                            <h3 class="font-bold mt-6 mb-3">Доставка</h3>
                            <p v-if="order.np_city_name">
                                Нова Пошта, {{ order.np_city_name }}, відділення: {{ order.np_warehouse_name }}
                            </p>
                            <p v-else>Самовивіз</p>
                        </div>

                        <div>
                            <h3 class="font-bold mb-3">Статус</h3>
                            <form @submit.prevent="updateStatus" class="flex items-center gap-3">
                                <select v-model="form.status" class="border-gray-300 rounded-lg text-sm">
                                    <option v-for="(name, id) in statuses" :key="id" :value="Number(id)">{{ name }}</option>
                                </select>
                                <PrimaryButton type="submit" :disabled="form.processing">Зберегти</PrimaryButton>
                            </form>

                            <h3 class="font-bold mt-6 mb-3">Оплата</h3>
                            <p>Сума: {{ order.total_amount / 100 }} грн</p>
                            <p v-if="order.paid_at">Оплачено: {{ new Date(order.paid_at).toLocaleString('uk-UA') }}</p>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200">
                        <h3 class="font-bold mb-3">Товари</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm text-gray-500">Товар</th>
                                    <th class="px-4 py-2 text-left text-sm text-gray-500">Варіант</th>
                                    <th class="px-4 py-2 text-left text-sm text-gray-500">Ціна</th>
                                    <th class="px-4 py-2 text-left text-sm text-gray-500">Кількість</th>
                                    <th class="px-4 py-2 text-left text-sm text-gray-500">Всього</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="item in order.order_items" :key="item.id">
                                    <td class="px-4 py-2 text-sm">{{ item.product_name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ item.attributes_summary || '—' }}</td>
                                    <td class="px-4 py-2 text-sm">{{ item.price / 100 }} грн</td>
                                    <td class="px-4 py-2 text-sm">{{ item.quantity }}</td>
                                    <td class="px-4 py-2 text-sm">{{ (item.price * item.quantity) / 100 }} грн</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
    statuses: {
        type: Object,
        default: () => ({}),
    },
})

const form = useForm({
    status: props.order.status,
})

const updateStatus = () => {
    form.put(route('orders.update', props.order.uuid))
}
</script>
