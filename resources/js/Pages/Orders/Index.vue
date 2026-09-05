<template>
    <Head title="Замовлення" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Замовлення
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-4 px-4">
                            <select v-model="status" @change="applyFilter" class="border-gray-300 rounded-lg text-sm">
                                <option value="">Всі статуси</option>
                                <option v-for="(name, id) in statuses" :key="id" :value="id">{{ name }}</option>
                            </select>
                        </div>

                        <section class="container px-4 mx-auto">
                            <div class="flex flex-col mt-6">
                                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                    <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                                        <div class="overflow-hidden border border-gray-200 md:rounded-lg">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col" class="py-3.5 px-4 text-sm font-normal text-left text-gray-500">ID</th>
                                                        <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left text-gray-500">Клієнт</th>
                                                        <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left text-gray-500">Телефон</th>
                                                        <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left text-gray-500">Сума</th>
                                                        <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left text-gray-500">Статус</th>
                                                        <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left text-gray-500">Дата</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    <tr
                                                        v-for="order in orders.data"
                                                        :key="order.id"
                                                        class="cursor-pointer hover:bg-gray-50"
                                                        @click="router.visit(route('orders.show', order.uuid))"
                                                    >
                                                        <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">{{ order.id }}</td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap">{{ order.customer_name }} {{ order.customer_surname }}</td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap">{{ order.customer_phone }}</td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap">{{ order.total_amount / 100 }} грн</td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap">{{ statuses[order.status] }}</td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap">{{ new Date(order.created_at).toLocaleString('uk-UA') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div class="flex items-center justify-between mt-6">
                                <Link
                                    :class="{'pointer-events-none': !orders.prev_page_url}"
                                    :href="orders.prev_page_url || ''"
                                    class="flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-gray-100"
                                >
                                    Назад
                                </Link>
                                <div class="items-center hidden md:flex gap-x-3">
                                    <Link v-for="page in pages" :key="page.label" :href="page.url" class="px-2 py-1 text-sm" :class="!page.active ? 'text-gray-500 rounded-md hover:bg-gray-100' : 'text-amber-600 rounded-md bg-amber-100/60'">{{ page.label }}</Link>
                                </div>
                                <Link :class="{'pointer-events-none': !orders.next_page_url}" :href="orders.next_page_url || ''" class="flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-gray-100">
                                    Наступна
                                </Link>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, computed } from 'vue';

const props = defineProps({
    orders: {
        type: Object,
        default: () => ({}),
    },
    statusFilter: {
        type: [String, Number],
        default: '',
    },
    statuses: {
        type: Object,
        default: () => ({}),
    },
})

const status = ref(props.statusFilter || '')
const pages = computed(() => props.orders.links.filter(({label}) => isNumeric(label)))
const isNumeric = (value) => /^-?\d+$/.test(value)

const applyFilter = () => {
    router.get(route('orders.index'), { status: status.value }, { preserveState: true })
}
</script>
