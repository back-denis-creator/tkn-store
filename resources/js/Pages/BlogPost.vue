<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';
import BlogPostCard from '@/Components/BlogPostCard.vue';
import { formatPostDate } from '@/blogDate';

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    morePosts: {
        type: Array,
        default: () => ([]),
    },
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

// Falls back to the opening of the article when the author left the excerpt
// empty — a post with no meta description at all is worse than a trimmed one.
const metaDescription = computed(() => {
    if (props.post.excerpt) return props.post.excerpt;

    const text = (props.post.content || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();

    return text.length > 160 ? `${text.slice(0, 157)}…` : text;
});
</script>

<template>
    <GuestLayout>
        <Head>
            <title>{{ post.title }}</title>
            <meta name="description" :content="metaDescription">
            <link rel="canonical" :href="route('blog.post', post.slug)">

            <meta property="og:title" :content="post.title">
            <meta property="og:description" :content="metaDescription">
            <meta property="og:url" :content="route('blog.post', post.slug)">
            <meta property="og:type" content="article">
            <meta v-if="post.cover_url" property="og:image" :content="post.cover_url">

            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" :content="post.title">
            <meta name="twitter:description" :content="metaDescription">
            <meta v-if="post.cover_url" name="twitter:image" :content="post.cover_url">
        </Head>

        <template #header>
            <!-- With a cover, the photo is the header and the title sits on it;
                 without one, the same dark ground as every other page header. -->
            <div class="relative overflow-hidden bg-gray-900 text-white">
                <img
                    v-if="post.cover_url"
                    :src="post.cover_url"
                    :alt="post.title"
                    class="absolute inset-0 h-full w-full object-cover object-center opacity-40"
                />
                <div v-else class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full bg-amber-400/20 blur-3xl"></div>

                <div class="relative mx-auto max-w-[800px] px-5 py-20 text-center lg:py-28">
                    <p v-if="post.published_at" class="text-xs uppercase tracking-[0.2em] text-amber-300">
                        {{ formatPostDate(post.published_at) }}
                    </p>
                    <h1 class="mt-4 text-3xl font-bold leading-tight drop-shadow sm:text-5xl">
                        {{ post.title }}
                    </h1>
                    <p v-if="post.excerpt" class="mx-auto mt-5 max-w-2xl text-lg font-light leading-relaxed text-gray-200">
                        {{ post.excerpt }}
                    </p>
                </div>
            </div>
        </template>

        <article class="py-16">
            <div class="rich-text mx-auto max-w-[720px] text-gray-800" v-html="post.content"></div>

            <div class="mx-auto mt-12 max-w-[720px]">
                <Link :href="route('blog')" class="inline-flex items-center gap-2 text-sm font-semibold text-amber-600 hover:text-amber-700">
                    <ArrowLeftIcon class="h-4 w-4" />
                    {{ $t('Blog_Back') }}
                </Link>
            </div>
        </article>

        <section v-if="morePosts.length" class="border-t border-gray-100 py-16">
            <h2 class="mb-8 text-2xl font-bold text-gray-900">{{ $t('Blog_More') }}</h2>
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <BlogPostCard v-for="more in morePosts" :key="more.id" :post="more" />
            </div>
        </section>
    </GuestLayout>
</template>
