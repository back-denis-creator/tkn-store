<template>
    <section class="mx-auto w-full px-4 md:max-w-[400px]">
        <div class="">
            <div class="border py-5 px-4 shadow-md">
                <p class="font-bold uppercase">{{ $t("Order") }}</p>

                <div v-for="product in $page.props.cart" class="flex justify-between border-b py-5">
                    <span>{{ product.name }}</span>
                    <span>{{ product.skus[0].price * product.quantity }}</span>
                </div>

                <div class="flex justify-between py-5">
                    <p>{{ $t("Total") }}</p>
                    <p>$1280</p>
                </div>

                <Button class="w-full px-5 py-2" @click="goToCheckout()" :disabled="!$page.props.cart.length">
                    {{ $t("Make an order") }}
                </Button>
            </div>
        </div>
    </section>
</template>
<script setup>
import { router } from '@inertiajs/vue3';

const goToCheckout = () => {
    router.visit(route('checkout'), { preserveScroll: true })
}
</script>
<style scope>

</style>