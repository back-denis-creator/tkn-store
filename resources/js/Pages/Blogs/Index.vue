<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    blogs: {
        type: Array,
        default: () => ([]),
    },
});

const form = useForm({});

const destroy = (id) => {
    if (confirm('Видалити цей допис?')) {
        form.delete(route('blogs.destroy', id));
    }
};
</script>

<template>
    <Head title="Блог" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Блог</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-white p-6">
                        <Link :href="route('blogs.create')">
                            <PrimaryButton>Створити допис</PrimaryButton>
                        </Link>

                        <p v-if="!blogs.length" class="mt-6 text-sm text-gray-400">
                            Дописів ще немає.
                        </p>

                        <div v-else class="mt-6 overflow-x-auto rounded-lg border border-gray-200">
                            <table class="w-full text-left text-sm text-gray-500">
                                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">Фото</th>
                                        <th scope="col" class="px-4 py-3">Заголовок</th>
                                        <th scope="col" class="px-4 py-3">Дата</th>
                                        <th scope="col" class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="blog in blogs" :key="blog.id" class="border-t border-gray-100 bg-white">
                                        <td class="px-4 py-4">
                                            <img
                                                v-if="blog.cover_url"
                                                :src="blog.cover_url"
                                                :alt="blog.title"
                                                class="h-12 w-20 rounded object-cover"
                                            />
                                            <span v-else class="text-gray-300">—</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="font-medium text-gray-800">{{ blog.title }}</p>
                                            <p v-if="blog.excerpt" class="mt-1 line-clamp-1 text-gray-500">{{ blog.excerpt }}</p>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-gray-500">{{ blog.created_at }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-right">
                                            <a
                                                :href="route('blog.post', blog.slug)"
                                                target="_blank"
                                                class="mr-3 text-sm text-gray-500 hover:text-gray-700"
                                            >
                                                Переглянути
                                            </a>
                                            <Link
                                                :href="route('blogs.edit', blog.id)"
                                                class="mr-2 rounded-lg bg-blue-600 px-4 py-2 text-white"
                                            >
                                                Редагувати
                                            </Link>
                                            <PrimaryButton class="bg-red-700" @click="destroy(blog.id)">
                                                Видалити
                                            </PrimaryButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
