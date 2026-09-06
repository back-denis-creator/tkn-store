<template>
    <Head title="Відгуки" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Відгуки
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
                                                        <th scope="col" class="py-3.5 px-4 text-sm font-normal text-left text-gray-500">Товар</th>
                                                        <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left text-gray-500">Автор</th>
                                                        <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left text-gray-500">Рейтинг</th>
                                                        <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left text-gray-500">Коментар</th>
                                                        <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left text-gray-500">Статус</th>
                                                        <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left text-gray-500">Дата</th>
                                                        <th scope="col" class="relative py-3.5 px-4">
                                                            <span class="sr-only">Дії</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    <tr v-for="review in reviews.data" :key="review.id">
                                                        <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">{{ review.product?.name }}</td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap">{{ review.author_name }}</td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap">{{ review.rating }} / 5</td>
                                                        <td class="px-4 py-4 text-sm max-w-xs truncate">{{ review.comment }}</td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap">{{ statuses[review.status] }}</td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap">{{ new Date(review.created_at).toLocaleString('uk-UA') }}</td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap">
                                                            <div class="flex justify-end gap-1">
                                                                <button
                                                                    v-if="review.status === 0"
                                                                    @click="approve(review.id)"
                                                                    class="mx-1 px-4 py-2 font-medium tracking-wide text-white transition-colors duration-300 transform bg-green-600 rounded-lg hover:bg-green-500 focus:outline-none focus:ring focus:ring-green-300 focus:ring-opacity-80"
                                                                >
                                                                    Опублікувати
                                                                </button>
                                                                <button
                                                                    v-if="review.status === 0"
                                                                    @click="reject(review.id)"
                                                                    class="mx-1 px-4 py-2 font-medium tracking-wide text-white transition-colors duration-300 transform bg-gray-500 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring focus:ring-gray-300 focus:ring-opacity-80"
                                                                >
                                                                    Відхилити
                                                                </button>
                                                                <button
                                                                    @click="destroy(review.id)"
                                                                    class="mx-1 px-4 py-2 font-medium tracking-wide text-white transition-colors duration-300 transform bg-red-600 rounded-lg hover:bg-red-500 focus:outline-none focus:ring focus:ring-red-300 focus:ring-opacity-80"
                                                                >
                                                                    Видалити
                                                                </button>
                                                            </div>
                                                        </td>
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
                                    :class="{'pointer-events-none': !reviews.prev_page_url}"
                                    :href="reviews.prev_page_url || ''"
                                    class="flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-gray-100"
                                >
                                    Назад
                                </Link>
                                <div class="items-center hidden md:flex gap-x-3">
                                    <Link v-for="page in pages" :key="page.label" :href="page.url" class="px-2 py-1 text-sm" :class="!page.active ? 'text-gray-500 rounded-md hover:bg-gray-100' : 'text-amber-600 rounded-md bg-amber-100/60'">{{ page.label }}</Link>
                                </div>
                                <Link :class="{'pointer-events-none': !reviews.next_page_url}" :href="reviews.next_page_url || ''" class="flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-gray-100">
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
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref, computed } from 'vue';

const props = defineProps({
    reviews: {
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
const pages = computed(() => props.reviews.links.filter(({label}) => isNumeric(label)))
const isNumeric = (value) => /^-?\d+$/.test(value)

const applyFilter = () => {
    router.get(route('reviews.index'), { status: status.value }, { preserveState: true })
}

const approve = (id) => {
    useForm({ status: 1 }).patch(route('reviews.update', id))
}
const reject = (id) => {
    useForm({ status: 2 }).patch(route('reviews.update', id))
}
function destroy(id) {
    if (confirm("Ви впевнені, що хочете видалити цей відгук?")) {
        useForm({}).delete(route('reviews.destroy', id))
    }
}
</script>
