<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BlogForm from '@/Pages/Blogs/Form.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    blog: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    title: props.blog.title,
    slug: props.blog.slug,
    excerpt: props.blog.excerpt ?? '',
    content: props.blog.content ?? '',
    cover: null,
    remove_cover: false,
});

const submit = () => {
    // POST with _method, not patch(): a PATCH cannot carry a file upload.
    form.transform((data) => ({ ...data, _method: 'patch' }))
        .post(route('blogs.update', props.blog.id), { forceFormData: true });
};
</script>

<template>
    <Head title="Редагування допису" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Редагування допису</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <BlogForm
                    :form="form"
                    :current-cover-url="blog.cover_url"
                    submit-label="Зберегти"
                    @submit="submit"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
