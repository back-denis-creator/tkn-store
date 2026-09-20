<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { NewspaperIcon } from '@heroicons/vue/24/outline';
import BlogPostCard from '@/Components/BlogPostCard.vue';

defineProps({
    posts: {
        type: Array,
        default: () => ([]),
    },
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <GuestLayout>
        <Head>
            <title>{{ $t('Blog') }}</title>
            <meta name="description" :content="$t('Blog_Subtitle')">
            <link rel="canonical" :href="route('blog')">

            <meta property="og:title" :content="'Casanel — ' + $t('Blog')">
            <meta property="og:description" :content="$t('Blog_Subtitle')">
            <meta property="og:url" :content="route('blog')">
            <meta property="og:type" content="website">

            <meta name="twitter:title" :content="'Casanel — ' + $t('Blog')">
            <meta name="twitter:description" :content="$t('Blog_Subtitle')">
        </Head>

        <template #header>
            <div class="relative overflow-hidden bg-gray-900 text-white">
                <div class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full bg-amber-400/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-32 -left-16 h-80 w-80 rounded-full bg-amber-400/10 blur-3xl"></div>
                <NewspaperIcon
                    class="pointer-events-none absolute -right-10 bottom-0 hidden h-64 w-64 text-amber-400/10 lg:block xl:h-80 xl:w-80"
                    aria-hidden="true"
                />

                <div class="relative mx-auto max-w-[1200px] px-5 py-16 lg:py-24 text-center">
                    <h1 class="mb-4 text-3xl font-bold sm:text-5xl lg:text-6xl">{{ $t('Blog') }}</h1>
                    <p class="mx-auto max-w-2xl text-lg font-light leading-relaxed text-gray-300 sm:text-xl">
                        {{ $t('Blog_Subtitle') }}
                    </p>
                </div>
            </div>
        </template>

        <section class="py-16">
            <p v-if="!posts.length" class="text-center text-gray-400">
                {{ $t('Blog_Empty') }}
            </p>

            <div v-else class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <BlogPostCard v-for="post in posts" :key="post.id" :post="post" />
            </div>
        </section>
    </GuestLayout>
</template>
