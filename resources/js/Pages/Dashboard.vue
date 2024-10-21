<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from "vue"
const value = ref(null)
const items = ref([])
const search = (e) => {
    console.log('e: ', e)
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <Card class="max-w-7xl mx-auto">
                <template #title>Simple Card</template>
                <template #content>
                    <AutoComplete v-model="value" :suggestions="items" @complete="search" />
                </template>
            </Card>
            <!-- <div class="max-w-7xl mx-auto">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">You're logged in!</div>
                    <AutoComplete v-model="value" :suggestions="items" @complete="search" />
                </div>
            </div> -->
        </div>
    </AuthenticatedLayout>
</template>
