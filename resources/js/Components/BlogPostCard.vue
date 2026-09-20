<script setup>
import { Link } from '@inertiajs/vue3';
import { NewspaperIcon } from '@heroicons/vue/24/outline';
import { formatPostDate } from '@/blogDate';

defineProps({
    post: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="route('blog.post', post.slug)"
        class="group flex h-full flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white transition-shadow hover:shadow-lg"
    >
        <div class="relative aspect-[16/10] w-full overflow-hidden bg-gray-100">
            <img
                v-if="post.cover_url"
                :src="post.cover_url"
                :alt="post.title"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
            <!-- A post with no cover still needs a card of the same height,
                 otherwise one missing photo breaks the whole row. -->
            <div v-else class="flex h-full w-full items-center justify-center text-gray-300">
                <NewspaperIcon class="h-12 w-12" />
            </div>
        </div>

        <div class="flex flex-1 flex-col p-5">
            <p v-if="post.published_at" class="text-xs uppercase tracking-wide text-gray-400">
                {{ formatPostDate(post.published_at) }}
            </p>
            <h2 class="mt-2 text-lg font-bold leading-snug text-gray-900 transition-colors group-hover:text-amber-600">
                {{ post.title }}
            </h2>
            <p v-if="post.excerpt" class="mt-2 line-clamp-3 text-sm leading-relaxed text-gray-500">
                {{ post.excerpt }}
            </p>
            <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-amber-600">
                {{ $t('Blog_Read') }}
                <span aria-hidden="true" class="transition-transform group-hover:translate-x-1">→</span>
            </span>
        </div>
    </Link>
</template>
