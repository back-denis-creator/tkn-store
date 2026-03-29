<template>
    <Head>
        <title>Текстиль для вашого простору</title>
        <meta name="description" content="Майстерня індивідуального пошиття Casanel. Шиємо столовий, кухонний і вуличний текстиль за вашими розмірами: скатертини, доріжки, серветки та подушки." />
        <meta property="og:title" content="Casanel — Текстиль для вашого простору" />
        <meta property="og:description" content="Майстерня індивідуального пошиття Casanel. Шиємо столовий, кухонний і вуличний текстиль за вашими розмірами: скатертини, доріжки, серветки та подушки." />
        <meta property="og:image" content="/images/casanel-logo.png" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Casanel — Текстиль для вашого простору" />
        <meta name="twitter:description" content="Майстерня індивідуального пошиття Casanel. Шиємо столовий, кухонний і вуличний текстиль за вашими розмірами: скатертини, доріжки, серветки та подушки." />
        <meta name="twitter:image" content="/images/casanel-logo.png" />
    </Head>
    <div>
        <NavBar />
        <WelcomeBlock />
        <ConsBages />
        <CathegoriesBlock />
        <ProductSlider />
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