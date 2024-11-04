<template>
    <Head title="Welcome" />
    <div class="bg-white">
        <NavBar />
        <WelcomeBlock />
        <ConsBages />
        <CathegoriesBlock />
        <ProductSlider />
        <SpecialOfferCard />
        <Recommendations />
        <Footer />
    </div>
</template>
<script setup>
import NavBar from '@/Components/NavBar.vue';
import Footer from '@/Components/Footer.vue';
import ProductSlider from '@/Components/ProductSlider.vue';
import SpecialOfferCard from '@/Components/SpecialOfferCard.vue';
import Recommendations from '@/Components/Recommendations.vue';
import WelcomeBlock from '@/Components/WelcomeBlock.vue';
import ConsBages from '@/Components/ConsBages.vue';
import CathegoriesBlock from '@/Components/CathegoriesBlock.vue';
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