<template>
    <Head title="Атрибути" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Атрибути
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-2 px-4">
                            <Link :href="route('attributes.create')">
                                <PrimaryButton>Створити атрибут</PrimaryButton>
                            </Link>
                        </div>

                        <section class="container px-4 mx-auto">
                            <div class="flex flex-col mt-6">
                                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                    <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                                        <div class="overflow-hidden border border-gray-200 dark:border-gray-700 md:rounded-lg">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-800">
                                                    <tr>
                                                        <th scope="col" class="py-3.5 px-4 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                                            <button class="flex items-center gap-x-3 focus:outline-none">
                                                                <span>ID</span>
                                                            </button>
                                                        </th>
                                                        <th scope="col" class="px-12 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                                            Імʼя
                                                        </th>
                                                        <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                                            Опис
                                                        </th>
                                                        <th scope="col" class="relative py-3.5 px-4">
                                                            <span class="sr-only">Редагувати</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-900">
                                                    <tr v-for="attribute in attributes" :key="attribute.id">
                                                        <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                                                            <div>
                                                                <h2 class="font-medium text-gray-800 dark:text-white ">{{ attribute.id }}</h2>
                                                            </div>
                                                        </td>
                                                        <td class="px-12 py-4 text-sm font-medium whitespace-nowrap">
                                                            <h2 class="font-medium text-gray-800 dark:text-white ">{{ attribute.name }}</h2>
                                                        </td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap">
                                                            <div>
                                                                <p class="text-gray-500 dark:text-gray-400">{{ attribute.description }}</p>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-4 text-sm whitespace-nowrap">
                                                            <div class="flex justify-end">
                                                                <Link
                                                                    :href="route('attributes.edit', attribute.id)"
                                                                    class="mx-1 px-6 py-2 font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-blue-600 rounded-lg hover:bg-blue-500 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-80"
                                                                >
                                                                    Редагувати
                                                                </Link>
                                                                <button @click="destroy(attribute.id)" class="mx-1 px-6 py-2 font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-red-600 rounded-lg hover:bg-red-500 focus:outline-none focus:ring focus:ring-red-300 focus:ring-opacity-80">
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
                            <!-- <div class="flex items-center justify-between mt-6">
                                <Link
                                    :class="{'pointer-events-none': !categories.prev_page_url}"
                                    :href="categories.prev_page_url || ''"
                                    class="flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-gray-100 dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-800"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 rtl:-scale-x-100">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18" />
                                    </svg>
                                    <span>
                                        Назад
                                    </span>
                                </Link>
                                <div class="items-center hidden md:flex gap-x-3">
                                    <Link v-for="page in pages" :key="page.label" :href="page.url" class="px-2 py-1 text-sm" :class="!page.active ? 'text-gray-500 rounded-md dark:hover:bg-gray-800 dark:text-gray-300 hover:bg-gray-100' : 'text-blue-500 rounded-md dark:bg-gray-800 bg-blue-100/60'">{{ page.label }}</Link>
                                </div>
                                <Link :class="{'pointer-events-none': !categories.next_page_url}" :href="categories.next_page_url || ''" class="flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-gray-100 dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-800">
                                    <span>
                                        Наступна
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 rtl:-scale-x-100">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
                                    </svg>
                                </Link>
                            </div> -->
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
// import { computed } from 'vue';
const props = defineProps({
    attributes: {
        type: Object,
        default: () => ({}),
    },
})
const form = useForm({})
// const pages = computed(() => props.attributes.links.filter(({label}) => isNumeric(label)))
// const isNumeric = (value) => /^-?\d+$/.test(value)
function destroy(id) {
    if (confirm("Are you sure you want to Delete")) {
        form.delete(route("categories.destroy", id));
    }
}
</script>
