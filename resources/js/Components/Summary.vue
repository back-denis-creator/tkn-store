<template>
    <section class="mx-auto w-full px-4 md:max-w-[400px]">
        <div class="">
            <div class="rounded-lg border border-gray-200 px-5 py-5 shadow-sm">
                <p class="font-bold uppercase">{{ $t("Order") }}</p>

                <div v-for="product in $page.props.cart" class="flex justify-between border-b py-5">
                    <span>{{ product.name }}</span>
                    <span>{{ product.skus[0].price * product.quantity }}</span>
                </div>

                <div class="flex justify-between py-5">
                    <p>{{ $t("Total") }}</p>
                    <p>{{ total }} грн.</p>
                </div>

                <Button class="w-full px-5 py-2" @click="goToCheckout()" :disabled="!$page.props.cart.length">
                    {{ $t("Make an order") }}
                </Button>
            </div>
        </div>
    </section>
</template>
<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const total = computed(() => {
    return (page.props.cart || []).reduce((sum, product) => {
        return sum + (product.skus?.[0]?.price || 0) * (product.quantity || 0)
    }, 0)
})

const goToCheckout = () => {
    router.visit(route('checkout'), { preserveScroll: true })
}
</script>
<style scope>

</style>