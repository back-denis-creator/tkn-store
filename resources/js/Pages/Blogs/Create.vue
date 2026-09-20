<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BlogForm from '@/Pages/Blogs/Form.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    cover: null,
    remove_cover: false,
});

const submit = () => {
    form.post(route('blogs.store'), { forceFormData: true });
};
</script>

<template>
    <Head title="Новий допис" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Новий допис</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <BlogForm :form="form" submit-label="Опублікувати" @submit="submit" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
