<template>
    <GuestLayout>
        <Head title="Cart">
            <meta name="robots" content="noindex, nofollow" />
        </Head>

        <section
        class="container mx-auto flex-grow max-w-[1200px] border-b py-5 lg:flex lg:flex-row lg:py-10"
      >
        <!-- Cart table (responsive: DataView items stack on mobile via flex-col) -->
        <!-- The list used to be a 600px box whatever it held, so a cart with
             one item pushed the order summary an empty screen down. It keeps
             its own scrollbar on a wide screen, where a long list would
             otherwise run past the summary beside it, but on a phone it is
             simply as tall as its items. -->
        <section
          class="w-full max-w-[1200px] grid grid-cols-1 gap-3 px-5 pb-10 lg:max-h-[600px] lg:overflow-auto"
        >
          <DataView :value="cart">
              <template #list="slotProps">
                  <div>
                      <!-- A phone has no room for four columns: the name was left
                           squeezed into a sliver and broke over three lines. There
                           the photo and the name share the top line, the way they
                           read together, and the quantity and the bin drop to a
                           line of their own. From md up the row is the same
                           four-column grid as before, so the columns still line up
                           from one item to the next. -->
                      <div
                          v-for="(item, index) in slotProps.items"
                          :key="index"
                          class="flex flex-wrap items-start gap-x-4 gap-y-3 py-4 md:grid md:grid-cols-[auto_1fr_auto_auto] md:gap-x-8"
                          :class="index !== 0 ? 'border-t border-surface-200' : ''"
                      >
                          <Link :href="route('product', item.slug)" class="order-1 w-24 shrink-0 md:order-none md:w-40">
                              <img class="block mx-auto rounded max-h-24" :src="item.skus[0]?.media[0]?.original_url" :alt="item.name" />
                          </Link>

                          <div class="order-2 flex min-w-[8rem] flex-1 flex-col items-start gap-2 md:order-none md:min-w-0 md:flex-none">
                              <div>
                                  <span class="font-medium text-surface-500 text-sm">{{ item.category }}</span>
                                  <Link :href="route('product', item.slug)" class="block text-lg font-medium mt-2 hover:text-amber-600">{{ item.name }}</Link>
                              </div>
                              <div v-if="item.skus[0]?.attribute_options?.length || item.selected_fabric" class="bg-surface-100 p-1 text-xs" style="border-radius: 30px">
                                  <div v-for="option in item.skus[0]?.attribute_options">
                                    <span>{{ option.attribute.name }}: {{ option.value }}</span>
                                  </div>
                                  <div v-if="item.selected_fabric">
                                    <span>{{ item.selected_fabric.attribute.name }}: {{ item.selected_fabric.value }}</span>
                                  </div>
                              </div>
                          </div>

                          <InputGroup class="order-3 !w-auto md:order-none">
                              <InputGroupAddon>
                                  <Button icon="pi pi-minus" severity="secondary" @click="updateQuantity(index, '-')" class="minus" />
                              </InputGroupAddon>
                              <InputText v-model="item.quantity" class="max-w-16 text-center" />
                              <InputGroupAddon>
                                  <Button icon="pi pi-plus" severity="secondary" @click="updateQuantity(index, '+')" class="plus" />
                              </InputGroupAddon>
                          </InputGroup>

                          <div @click="deleteFromCart(item.skus[0]?.id, item.selected_fabric?.id)" class="order-4 ml-auto mt-3 h-5 w-5 shrink-0 cursor-pointer self-center md:order-none md:ml-0 md:mt-2 md:self-start">
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
                      </div>
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

const form = useForm({ skuId: null, quantity: null, fabricAttributeOptionId: null })

const deleteFromCart = (skuId, fabricAttributeOptionId = null) => {
    Object.assign(form, { skuId, fabricAttributeOptionId })
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
    Object.assign(form, {
        skuId: props.cart[index].skus[0].id,
        quantity: props.cart[index].quantity,
        fabricAttributeOptionId: props.cart[index].selected_fabric?.id ?? null,
    })
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