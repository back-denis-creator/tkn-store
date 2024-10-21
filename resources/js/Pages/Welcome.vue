<template>
    <Head title="Welcome" />
    <div class="bg-white">
        <NavBar />
        <WelcomeBlock />
        <ProductSlider />
    </div>
</template>
<script setup>
import NavBar from '@/Components/NavBar.vue';
import ProductSlider from '@/Components/ProductSlider.vue';
import WelcomeBlock from '@/Components/WelcomeBlock.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    productSlider: {
        type: Array,
        required: true
    }
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>