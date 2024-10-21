<script setup>
import { Head, Link } from '@inertiajs/vue3';
import NavBar from '@/Components/NavBar.vue';
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
});
</script>
<template>
    <Head title="Contacts" />
    <div class="bg-white">
        <NavBar />
    </div>
</template>