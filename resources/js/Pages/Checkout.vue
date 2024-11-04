<template>
    <GuestLayout>
        <Head title="Checkout" />

        <div class="flex-grow">
        <section
          class="container mx-auto max-w-[1200px] py-5 lg:flex lg:flex-row lg:py-10"
        >
          <h2 class="mx-auto px-5 text-2xl font-bold md:hidden">
            Complete Address
          </h2>
          <!-- form  -->
          <section
            class="grid w-full max-w-[1200px] grid-cols-1 gap-3 px-5 pb-10"
          >

            <div class="card flex justify-center">
                <Stepper value="1" class="basis-[50rem]">
                    <StepList>
                        <Step value="1">Контактна інформація</Step>
                        <Step value="2">Доставка</Step>
                        <Step value="3">Оплата</Step>
                    </StepList>
                    <StepPanels>
                        <StepPanel v-slot="{ activateCallback }" value="1">
                            <div class="flex flex-col h-48 justify-center">
                                <div class="card grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <i class="pi pi-user"></i>
                                        </InputGroupAddon>
                                        <InputText v-model="form.name" placeholder="Ім'я" />
                                    </InputGroup>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <i class="pi pi-user"></i>
                                        </InputGroupAddon>
                                        <InputText v-model="form.surname" placeholder="Фамілія" />
                                    </InputGroup>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <i class="pi pi-phone"></i>
                                        </InputGroupAddon>
                                        <InputMask id="phone" v-model="form.phone" mask="+38 (999) 999-9999" placeholder="Телефон" fluid />
                                    </InputGroup>
                                    <InputGroup>
                                        <InputGroupAddon>
                                            <i class="pi pi-envelope"></i>
                                        </InputGroupAddon>
                                        <InputText v-model="form.email" placeholder="Пошта" />
                                    </InputGroup>
                                </div>
                            </div>
                            <div class="flex pt-6 justify-between">
                                <Button label="Назад" severity="secondary" icon="pi pi-arrow-left" @click="backToCart()" />
                                <Button label="Вперед" icon="pi pi-arrow-right" iconPos="right" @click="activateCallback('2')" />
                            </div>
                        </StepPanel>
                        <StepPanel v-slot="{ activateCallback }" value="2">
                            <div class="flex flex-col h-48 justify-center">
                                <div class="card grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <AutoComplete v-model="selectedCity" optionLabel="Description" :suggestions="cities" @complete="getNPCities" inputClass="w-full" />
                                    <AutoComplete v-model="selectedWarehous" optionLabel="Description" :suggestions="warehouses" @complete="getNPWarehouses" inputClass="w-full" dropdown :disabled="!selectedCity?.Ref" />
                                </div>
                            </div>
                            <div class="flex pt-6 justify-between">
                                <Button label="Назад" severity="secondary" icon="pi pi-arrow-left" @click="activateCallback('1')" />
                                <Button label="Вперед" icon="pi pi-arrow-right" iconPos="right" @click="activateCallback('3')" />
                            </div>
                        </StepPanel>
                        <StepPanel v-slot="{ activateCallback }" value="3">
                            <div class="flex flex-col h-48">
                                <div class="border-2 border-dashed border-surface-200 dark:border-surface-700 rounded bg-surface-50 dark:bg-surface-950 flex-auto flex justify-center items-center font-medium">Content III</div>
                            </div>
                            <div class="flex pt-6 justify-between">
                                <Button label="Назад" severity="secondary" icon="pi pi-arrow-left" @click="activateCallback('2')" />
                                <Button label="Підтвердити" />
                            </div>
                        </StepPanel>
                    </StepPanels>
                </Stepper>
            </div>

          </section>
          <!-- /form  -->

          <!-- Summary  -->

          <section class="mx-auto w-full px-4 md:max-w-[400px]">
            <div class="">
              <div class="border py-5 px-4 shadow-md">
                <p class="font-bold uppercase">Сума Замовлення</p>

                <div class="flex justify-between border-b py-5">
                  <p>Subtotal</p>
                  <p>$1280</p>
                </div>

                <div class="flex justify-between border-b py-5">
                  <p>Shipping</p>
                  <p>Free</p>
                </div>

                <div class="flex justify-between py-5">
                  <p>Total</p>
                  <p>$1280</p>
                </div>
              </div>
            </div>
          </section>
        </section>

        <!-- /Summary -->

        <!-- Cons bages -->

        <section
          class="container mx-auto my-8 flex flex-col justify-center gap-3 lg:flex-row"
        >
          <!-- 1 -->

          <div
            class="mx-5 flex flex-row items-center justify-center border-2 border-yellow-400 py-4 px-5"
          >
            <div class="">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="h-6 w-6 text-violet-900 lg:mr-2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"
                />
              </svg>
            </div>

            <div class="ml-6 flex flex-col justify-center">
              <h3 class="text-left text-xs font-bold lg:text-sm">
                Free Delivery
              </h3>
              <p class="text-light text-center text-xs lg:text-left lg:text-sm">
                Orders from $200
              </p>
            </div>
          </div>

          <!-- 2 -->

          <div
            class="mx-5 flex flex-row items-center justify-center border-2 border-yellow-400 py-4 px-5"
          >
            <div class="">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="h-6 w-6 text-violet-900 lg:mr-2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"
                />
              </svg>
            </div>

            <div class="ml-6 flex flex-col justify-center">
              <h3 class="text-left text-xs font-bold lg:text-sm">
                Money returns
              </h3>
              <p class="text-light text-left text-xs lg:text-sm">
                30 Days guarantee
              </p>
            </div>
          </div>

          <!-- 3 -->

          <div
            class="mx-5 flex flex-row items-center justify-center border-2 border-yellow-400 py-4 px-5"
          >
            <div class="">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="h-6 w-6 text-violet-900 lg:mr-2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"
                />
              </svg>
            </div>

            <div class="ml-6 flex flex-col justify-center">
              <h3 class="text-left text-xs font-bold lg:text-sm">
                24/7 Supports
              </h3>
              <p class="text-light text-left text-xs lg:text-sm">
                Consumer support
              </p>
            </div>
          </div>
        </section>

        <!-- /Cons bages  -->
      </div>

    </GuestLayout>
</template>
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from "vue"
import Stepper from 'primevue/stepper';
import StepList from 'primevue/steplist';
import StepPanels from 'primevue/steppanels';
// import StepItem from 'primevue/stepitem';
import Step from 'primevue/step';
import StepPanel from 'primevue/steppanel';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import InputMask from 'primevue/inputmask';

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
    cities: {
        type: Array,
        required: false
    },
    warehouses: {
        type: Array,
        required: false
    }
})

const backToCart = () => {
    router.visit(route('cart'), { preserveScroll: true })
}

const form = useForm({
    name: '',
    surname: '',
    phone: '',
    email: ''
})

const cityModel = ref('');
const selectedCity = computed({
  get() {
    return cityModel.value
  },
  set(newValue) {
    if(warehousesTimer.value) clearTimeout(warehousesTimer.value)
    if(newValue?.Ref) {
        warehousesTimer.value = setTimeout(() => {
            getNPWarehouses()
        }, 800)
    }
    cityModel.value = newValue
  }
})
const selectedWarehous = ref('');

const cityTimer = ref(null);
const warehousesTimer = ref(null);

const getNPCities = (event) => {
    if(cityTimer.value) clearTimeout(cityTimer.value)
    cityTimer.value = setTimeout(() => {
        if(event.query.trim().length) {
            router.post(route('np.cities'), {search: event.query.trim().toLowerCase()} ,{
                preserveScroll: true,
                only: ['cities'],
                onSuccess: (page) => {
                    console.log('page: ', page)
                }
            })
        }
    }, 800)
}

const getNPWarehouses = (event = false) => {
    let data = { city_ref: selectedCity.value.Ref }
    if(event) Object.assign(data, { search: event.query.trim().toLowerCase() })
    router.post(route('np.warehouses'), data, {
        preserveScroll: true,
        only: ['warehouses'],
        onSuccess: (page) => {
            console.log('page: ', page)
        }
    })
}

</script>