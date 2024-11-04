<script setup>
// import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavBar from '@/Components/NavBar.vue';
import Footer from '@/Components/Footer.vue';
// import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex flex-col h-screen">
        <NavBar />
        <!-- <div>
            <Link href="/">
                <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
            </Link>
        </div> -->
        <div v-if="$slots.header" class="w-full">
            <slot name="header" />
        </div>
        <!-- class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg" -->
        <!-- self-center -->
        <!-- mx-auto flex-grow w-full mt-10 mb-10 max-w-[1200px] px-5 -->
        <!-- flex-grow mx-auto max-w-[1200px] -->
            <div class="mx-auto flex-grow w-full mt-10 mb-10 max-w-[1200px] px-5">
                <slot />
            </div>
        <Footer />
    </div>
</template>
