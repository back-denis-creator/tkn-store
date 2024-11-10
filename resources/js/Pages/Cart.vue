<template>
    <GuestLayout>
        <Head title="Cart" />

        <section
        class="container mx-auto flex-grow max-w-[1200px] border-b py-5 lg:flex lg:flex-row lg:py-10"
      >
        <!-- Mobile cart table  -->
        <section
          class="container mx-auto my-3 flex w-full flex-col gap-3 px-4 md:hidden"
        >
          <!-- 1 -->

          <div class="flex w-full border px-4 py-4">
            <img
              class="self-start object-contain"
              width="90px"
              src="/images/bedroom.png"
              alt="bedroom image"
            />
            <div class="ml-3 flex w-full flex-col justify-center">
              <div class="flex items-center justify-between">
                <p class="text-xl font-bold">ITALIAN BED</p>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  class="h-5 w-5"
                >
                  <path
                    d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z"
                  />
                </svg>
              </div>
              <p class="text-sm text-gray-400">Size: XL</p>
              <p class="py-3 text-xl font-bold text-emerald-500">$320</p>
              <div class="mt-2 flex w-full items-center justify-between">
                <div class="flex items-center justify-center">
                  <button
                    class="flex h-8 w-8 cursor-pointer items-center justify-center border duration-100 hover:bg-neutral-100 focus:ring-2 focus:ring-gray-500 active:ring-2 active:ring-gray-500"
                  >
                    &minus;
                  </button>
                  <div
                    class="flex h-8 w-8 cursor-text items-center justify-center border-t border-b active:ring-gray-500"
                  >
                    1
                  </div>
                  <button
                    class="flex h-8 w-8 cursor-pointer items-center justify-center border duration-100 hover:bg-neutral-100 focus:ring-2 focus:ring-gray-500 active:ring-2 active:ring-gray-500"
                  >
                    &#43;
                  </button>
                </div>

                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  class="m-0 h-5 w-5 cursor-pointer"
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

          <!-- 2 -->

          <div class="flex w-full border px-4 py-4">
            <img
              class="self-start object-contain"
              width="90px"
              src="/images/product-chair.png"
              alt="Chair image"
            />
            <div class="ml-3 flex w-full flex-col justify-center">
              <div class="flex items-center justify-between">
                <p class="text-xl font-bold">GUYER CHAIR</p>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  class="h-5 w-5"
                >
                  <path
                    d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z"
                  />
                </svg>
              </div>
              <p class="text-sm text-gray-400">Size: XL</p>
              <p class="py-3 text-xl font-bold text-emerald-500">$320</p>
              <div class="mt-2 flex w-full items-center justify-between">
                <div class="flex items-center justify-center">
                  <button
                    class="flex h-8 w-8 cursor-pointer items-center justify-center border duration-100 hover:bg-neutral-100 focus:ring-2 focus:ring-gray-500 active:ring-2 active:ring-gray-500"
                  >
                    &minus;
                  </button>
                  <div
                    class="flex h-8 w-8 cursor-text items-center justify-center border-t border-b active:ring-gray-500"
                  >
                    1
                  </div>
                  <button
                    class="flex h-8 w-8 cursor-pointer items-center justify-center border duration-100 hover:bg-neutral-100 focus:ring-2 focus:ring-gray-500 active:ring-2 active:ring-gray-500"
                  >
                    &#43;
                  </button>
                </div>

                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  class="m-0 h-5 w-5 cursor-pointer"
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

          <!-- 3 -->

          <div class="flex w-full border px-4 py-4">
            <img
              class="self-start object-contain"
              width="90px"
              src="/images/outdoors.png"
              alt="Outdoor chair image"
            />
            <div class="ml-3 flex w-full flex-col justify-center">
              <div class="flex items-center justify-between">
                <p class="text-xl font-bold">OUTDOOR CHAIR</p>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  class="h-5 w-5"
                >
                  <path
                    d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z"
                  />
                </svg>
              </div>
              <p class="text-sm text-gray-400">Size: XL</p>
              <p class="py-3 text-xl font-bold text-emerald-500">$320</p>
              <div class="mt-2 flex w-full items-center justify-between">
                <div class="flex items-center justify-center">
                  <button
                    class="flex h-8 w-8 cursor-pointer items-center justify-center border duration-100 hover:bg-neutral-100 focus:ring-2 focus:ring-gray-500 active:ring-2 active:ring-gray-500"
                  >
                    &minus;
                  </button>
                  <div
                    class="flex h-8 w-8 cursor-text items-center justify-center border-t border-b active:ring-gray-500"
                  >
                    1
                  </div>
                  <button
                    class="flex h-8 w-8 cursor-pointer items-center justify-center border duration-100 hover:bg-neutral-100 focus:ring-2 focus:ring-gray-500 active:ring-2 active:ring-gray-500"
                  >
                    &#43;
                  </button>
                </div>

                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  class="m-0 h-5 w-5 cursor-pointer"
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

          <!-- 4 -->

          <div class="flex w-full border px-4 py-4">
            <img
              class="self-start object-contain"
              width="90px"
              src="/images/matrass.png"
              alt="Matrass image"
            />
            <div class="ml-3 flex w-full flex-col justify-center">
              <div class="flex items-center justify-between">
                <p class="text-xl font-bold">MATRASS COMFORT &plus;</p>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  class="h-5 w-5"
                >
                  <path
                    d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z"
                  />
                </svg>
              </div>
              <p class="text-sm text-gray-400">Size: XL</p>
              <p class="py-3 text-xl font-bold text-emerald-500">$320</p>
              <div class="mt-2 flex w-full items-center justify-between">
                <div class="flex items-center justify-center">
                  <button
                    class="flex h-8 w-8 cursor-pointer items-center justify-center border duration-100 hover:bg-neutral-100 focus:ring-2 focus:ring-gray-500 active:ring-2 active:ring-gray-500"
                  >
                    &minus;
                  </button>
                  <div
                    class="flex h-8 w-8 cursor-text items-center justify-center border-t border-b active:ring-gray-500"
                  >
                    1
                  </div>
                  <button
                    class="flex h-8 w-8 cursor-pointer items-center justify-center border duration-100 hover:bg-neutral-100 focus:ring-2 focus:ring-gray-500 active:ring-2 active:ring-gray-500"
                  >
                    &#43;
                  </button>
                </div>

                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  class="m-0 h-5 w-5 cursor-pointer"
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
        </section>
        <!-- /Mobile cart table  -->

        <!-- Desktop cart table  -->
        <section
          class="hidden h-[600px] w-full max-w-[1200px] grid-cols-1 gap-3 px-5 pb-10 md:grid overflow-auto"
        >
          <DataView :value="cart">
              <template #list="slotProps">
                  <div class="flex flex-col">
                      <div v-for="(item, index) in slotProps.items" :key="index">
                          <div class="flex flex-col sm:flex-row sm:items-center p-6 gap-4" :class="{ 'border-t border-surface-200 dark:border-surface-700': index !== 0 }">
                              <div class="md:w-40 relative">
                                  <img class="block xl:block mx-auto rounded w-full" :src="`https://primefaces.org/cdn/primevue/images/product/${item.image}`" :alt="item.name" />
                                  <div class="absolute bg-black/70 rounded-border" style="left: 4px; top: 4px">
                                      <!-- <Tag :value="item.inventoryStatus" :severity="getSeverity(item)"></Tag> -->
                                  </div>
                              </div>
                              <div class="flex flex-col md:flex-row justify-between md:items-center flex-1 gap-6">
                                  <div class="flex flex-row md:flex-col justify-between items-start gap-2">
                                      <div>
                                          <span class="font-medium text-surface-500 dark:text-surface-400 text-sm">{{ item.category }}</span>
                                          <div class="text-lg font-medium mt-2">{{ item.name }}</div>
                                      </div>
                                      <div class="bg-surface-100 p-1" style="border-radius: 30px">
                                          <div class="bg-surface-0 flex items-center gap-2 justify-center py-1 px-2" style="border-radius: 30px; box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.04), 0px 1px 2px 0px rgba(0, 0, 0, 0.06)">
                                              <span class="text-surface-900 font-medium text-sm">{{ item.rating }}</span>
                                              <i class="pi pi-star-fill text-yellow-500"></i>
                                          </div>
                                      </div>
                                  </div>
                                  <!-- md:items-end gap-8 -->
                                  <div class="flex flex-col">
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <Button icon="pi pi-minus" severity="secondary" @click="updateQuantity(index, '-')" class="minus" />
                                        </InputGroupAddon>
                                        <InputText v-model="item.quantity" class="max-w-16 text-center" />
                                        <InputGroupAddon>
                                            <Button icon="pi pi-plus" severity="secondary" @click="updateQuantity(index, '+')" class="plus" />
                                        </InputGroupAddon>
                                    </InputGroup>

                                      <!-- <span class="text-xl font-semibold">${{ item.price }}</span>
                                      <div class="flex flex-row-reverse md:flex-row gap-2">
                                          <Button icon="pi pi-heart" outlined></Button>
                                          <Button icon="pi pi-shopping-cart" label="Buy Now" :disabled="item.inventoryStatus === 'OUTOFSTOCK'" class="flex-auto md:flex-initial whitespace-nowrap"></Button>
                                      </div> -->
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </template>
          </DataView>

          <!-- <table class="table-fixed">
            <thead class="h-16 bg-neutral-100">
              <tr class="uppercase">
                <th>Товар</th>
                <th>Ціна</th>
                <th>Кількість</th>
                <th>Всього</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr class="h-[100px] border-b" v-for="(product, index) in cart">
                <td class="align-middle">
                  <div class="flex">
                    <img
                      class="w-[90px]"
                      src="/images/bedroom.png"
                      alt="bedroom image"
                    />
                    <div class="ml-3 flex flex-col justify-center">
                      <p class="text-xl font-bold">ITALIAN BED</p>
                      <p class="text-sm text-gray-400">Size: XL</p>
                    </div>
                  </div>
                </td>
                <td class="mx-auto text-center">{{ product.price }}</td>
                <td class="align-middle">
                  <div class="flex items-center justify-center">
                    <button
                        @click="updateQuantity(index, '-')"
                        class="flex h-8 w-8 cursor-pointer items-center justify-center border duration-100 hover:bg-neutral-100 focus:ring-2 focus:ring-gray-500 active:ring-2 active:ring-gray-500"
                    >
                      &minus;
                    </button>
                    <div
                      class="flex h-8 w-8 cursor-text items-center justify-center border-t border-b active:ring-gray-500"
                    >
                      {{ product.quantity }}
                    </div>
                    <button
                        @click="updateQuantity(index, '+')"
                      class="flex h-8 w-8 cursor-pointer items-center justify-center border duration-100 hover:bg-neutral-100 focus:ring-2 focus:ring-gray-500 active:ring-2 active:ring-gray-500"
                    >
                      &#43;
                    </button>
                  </div>
                </td>
                <td class="mx-auto text-center">{{ product.price * product.quantity }}</td>
                <td class="align-middle">
                    <div @click="deleteFromCart(product.product_id)" class="m-0 h-5 w-5 cursor-pointer">
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
                </td>
              </tr>
            </tbody>
          </table> -->
        </section>
        <!-- /Desktop cart table  -->

        <Summary />
      </section>

    </GuestLayout>
</template>
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import Summary from '@/Components/Summary.vue';
import DataView from 'primevue/dataview';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';

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

const form = useForm({ productId: null })

const deleteFromCart = (productId) => {
    Object.assign(form, { productId })
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