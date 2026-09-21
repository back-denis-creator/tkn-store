<template>
    <section class="mx-auto w-full px-4 md:max-w-[400px]">
        <div class="">
            <div class="rounded-lg border border-gray-200 px-5 py-5 shadow-sm">
                <p class="font-bold uppercase">{{ $t("Order") }}</p>

                <!-- One rule, above the total: a line under every item made a
                     two-item order read as a table of totals. -->
                <div class="mt-5 space-y-3">
                    <div v-for="product in $page.props.cart" :key="product.id" class="flex justify-between gap-4">
                        <span class="min-w-0">
                            {{ product.name }}
                            <span class="whitespace-nowrap text-surface-500">× {{ product.quantity }}</span>
                        </span>
                        <span class="whitespace-nowrap">{{ product.skus[0].price * product.quantity }} {{ $t('Currency') }}</span>
                    </div>
                </div>

                <div class="mt-5 flex justify-between border-t border-gray-200 pt-5 font-semibold">
                    <p>{{ $t("Total") }}</p>
                    <p class="whitespace-nowrap">{{ total }} {{ $t('Currency') }}</p>
                </div>


                <Button
                    class="mt-5 w-full px-5 py-2"
                    :loading="processing"
                    :disabled="!$page.props.cart.length"
                    @click="onClick"
                >
                    {{ $t("Make an order") }}
                </Button>
            </div>
        </div>
    </section>
</template>
<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    // On the checkout page this button has to place the order: leading to
    // checkout from checkout did nothing at all, and it is the button most
    // buyers press there, being the largest one on the page.
    submitsOrder: {
        type: Boolean,
        default: false,
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['submit']);

const page = usePage();

const total = computed(() => {
    return (page.props.cart || []).reduce((sum, product) => {
        return sum + (product.skus?.[0]?.price || 0) * (product.quantity || 0)
    }, 0)
})

const goToCheckout = () => {
    router.visit(route('checkout'), { preserveScroll: true })
}

const onClick = () => {
    props.submitsOrder ? emit('submit') : goToCheckout()
}
</script>
<style scope>

</style>