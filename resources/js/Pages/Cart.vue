<template>
    <GuestLayout>
        <Head title="Cart">
            <meta name="robots" content="noindex, nofollow" />
        </Head>

        <section
        class="container mx-auto flex-grow max-w-[1200px] border-b py-5 lg:flex lg:flex-row lg:py-10"
      >
        <!-- Cart table (responsive: DataView items stack on mobile via flex-col) -->
        <section
          class="h-[600px] w-full max-w-[1200px] grid grid-cols-1 gap-3 px-5 pb-10 overflow-auto"
        >
          <DataView :value="cart">
              <template #list="slotProps">
                  <!-- Single grid for the whole list (not one grid per row) so the image/name/qty/delete
                       columns share the exact same width across every row — a fixed-width name column
                       would either clip long titles or waste space on short ones. -->
                  <div class="grid grid-cols-[auto_1fr_auto_auto] items-start gap-x-4 gap-y-4 sm:gap-x-6 md:gap-x-8">
                      <template v-for="(item, index) in slotProps.items" :key="index">
                          <div v-if="index !== 0" class="col-span-full border-t border-surface-200 dark:border-surface-700"></div>

                          <Link :href="route('product', item.slug)" class="w-24 md:w-40 shrink-0">
                              <img class="block mx-auto rounded max-h-24" :src="item.skus[0]?.media[0]?.original_url" :alt="item.name" />
                          </Link>

                          <div class="flex flex-col items-start gap-2">
                              <div>
                                  <span class="font-medium text-surface-500 dark:text-surface-400 text-sm">{{ item.category }}</span>
                                  <Link :href="route('product', item.slug)" class="block text-lg font-medium mt-2 hover:text-amber-600">{{ item.name }}</Link>
                              </div>
                              <div v-if="item.skus[0]?.attribute_options?.length" class="bg-surface-100 p-1 text-xs" style="border-radius: 30px">
                                  <div v-for="option in item.skus[0]?.attribute_options">
                                    <span>{{ option.attribute.name }}: {{ option.value }}</span>
                                  </div>
                              </div>
                          </div>

                          <InputGroup>
                              <InputGroupAddon>
                                  <Button icon="pi pi-minus" severity="secondary" @click="updateQuantity(index, '-')" class="minus" />
                              </InputGroupAddon>
                              <InputText v-model="item.quantity" class="max-w-16 text-center" />
                              <InputGroupAddon>
                                  <Button icon="pi pi-plus" severity="secondary" @click="updateQuantity(index, '+')" class="plus" />
                              </InputGroupAddon>
                          </InputGroup>

                          <div @click="deleteFromCart(item.skus[0]?.id)" class="m-0 mt-2 h-5 w-5 cursor-pointer">
                              <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  viewBox="0 0 20 20"
                                  fill="currentColor"
                              >
                                  <path
                                      fill-rule="evenodd"
                                      d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z"
                                      clip-rule="evenodd"
                                  />
                              </svg>
                          </div>
                      </template>
                  </div>
              </template>
          </DataView>

        </section>
        <!-- /Desktop cart table  -->

        <Summary />
      </section>

    </GuestLayout>
</template>
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Summary from '@/Components/Summary.vue';
import DataView from 'primevue/dataview';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import { useToast } from "primevue/usetoast"

const toast = useToast()
const props = defineProps({
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
    cart: {
        type: Array,
        required: true
    },
})

const goToCheckout = () => {
    router.visit(route('checkout'), { preserveScroll: true })
}

const form = useForm({ skuId: null, quantity: null })

const deleteFromCart = (skuId) => {
    Object.assign(form, { skuId })
    form.delete(route('cart.delete'), {
        preserveScroll: true,
        onSuccess: () => {

        },
        onError: () => {

        },
    });
}

const updateQuantity = (index, action) => {
    if (action === '+') {
        props.cart[index].quantity ++
    } else if (props.cart[index].quantity > 1) {
        props.cart[index].quantity --
    }
    console.log('props.cart[index]: ', props.cart[index])
    Object.assign(form, { skuId: props.cart[index].skus[0].id, quantity: props.cart[index].quantity })
    console.log('form: ', form)
    form.post(route('cart.update'), {
        preserveScroll: true,
        only: ['cart'],
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Оновленно', life: 3000 })
        },
        onError: () => {

        },
    })
}

</script>
<style scoped>
.p-inputgroupaddon {
  padding: 0 !important;
}
.minus {
  border-bottom-right-radius: 0;
  border-top-right-radius: 0;
}
.plus {
  border-bottom-left-radius: 0;
  border-top-left-radius: 0;
}
</style>