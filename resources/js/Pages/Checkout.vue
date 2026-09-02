<template>
    <GuestLayout>
        <Head title="Checkout">
            <meta name="robots" content="noindex, nofollow" />
        </Head>
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
                              <div class="flex flex-col justify-center">
                                <div class="card">
                                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                  <Textarea v-model="form.comment" rows="5" cols="30" class="w-full mt-4" placeholder="Коментар" />
                                  <p v-if="form.errors.name" class="text-sm text-red-600 mt-2">{{ form.errors.name }}</p>
                                  <p v-if="form.errors.phone" class="text-sm text-red-600 mt-2">{{ form.errors.phone }}</p>
                                </div>
                              </div>
                              <div class="flex pt-6 justify-between">
                                  <Button label="Назад" severity="secondary" icon="pi pi-arrow-left" @click="backToCart()" />
                                  <Button label="Вперед" icon="pi pi-arrow-right" iconPos="right" @click="activateCallback('2')" />
                              </div>
                          </StepPanel>
                          <StepPanel v-slot="{ activateCallback }" value="2">
                              <div class="card flex flex-col h-48 justify-center gap-4">
                                  <div v-for="delivery in deliveries" :key="delivery.id" class="flex items-center gap-2">
                                      <RadioButton v-model="form.delivery_method" @update:modelValue="changeDelivery" :inputId="`delivery_${delivery.id}`" name="delivery" :value="delivery.id" />
                                      <label :for="`delivery_${delivery.id}`">{{ delivery.name }}</label>
                                  </div>
                                  <Transition>
                                    <div v-if="form.delivery_method === DELIVERY_NOVA_POSHTA" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                      <AutoComplete v-model="selectedCity" placeholder="Місто" optionLabel="Description" :suggestions="cities" @complete="getNPCities" inputClass="w-full" />
                                      <AutoComplete v-model="selectedWarehous" placeholder="№ Відділення" optionLabel="Description" :suggestions="warehouses" @complete="getNPWarehouses" inputClass="w-full" dropdown :disabled="!selectedCity?.Ref" />
                                    </div>
                                  </Transition>
                                  <p v-if="form.errors.np_city_ref" class="text-sm text-red-600">{{ form.errors.np_city_ref }}</p>
                                  <p v-if="form.errors.np_warehouse_ref" class="text-sm text-red-600">{{ form.errors.np_warehouse_ref }}</p>
                              </div>
                              <div class="flex pt-6 justify-between">
                                  <Button label="Назад" severity="secondary" icon="pi pi-arrow-left" @click="activateCallback('1')" />
                                  <Button label="Вперед" icon="pi pi-arrow-right" iconPos="right" @click="activateCallback('3')" />
                              </div>
                          </StepPanel>
                          <StepPanel v-slot="{ activateCallback }" value="3">
                              <div class="card flex flex-col h-48 justify-center gap-4">
                                  <div v-for="(name, id) in payments" :key="id" class="flex items-center gap-2">
                                      <RadioButton v-model="form.payment_method" :inputId="`payment_${id}`" name="payment" :value="Number(id)" :disabled="isPaymentDisabled(id)" />
                                      <label :for="`payment_${id}`">{{ name }}</label>
                                  </div>
                              </div>
                              <p v-if="form.errors.cart" class="text-sm text-red-600">{{ form.errors.cart }}</p>
                              <div class="flex pt-6 justify-between">
                                  <Button label="Назад" severity="secondary" icon="pi pi-arrow-left" @click="activateCallback('2')" />
                                  <Button label="Підтвердити" :loading="form.processing" @click="submitOrder" />
                              </div>
                          </StepPanel>
                      </StepPanels>
                  </Stepper>
              </div>
            </section>
            <!-- /form  -->
            <Summary />
          </section>
          <ConsBages />
        </div>
    </GuestLayout>
</template>
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from "vue"
import Stepper from 'primevue/stepper';
import StepList from 'primevue/steplist';
import StepPanels from 'primevue/steppanels';
import Step from 'primevue/step';
import StepPanel from 'primevue/steppanel';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import InputMask from 'primevue/inputmask';
import Summary from '@/Components/Summary.vue';
import ConsBages from '@/Components/ConsBages.vue';

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
    },
    deliveries: {
        type: Array,
        default: () => []
    },
    payments: {
        type: Object,
        default: () => ({})
    }
})

// Mirrors App\Models\Delivery / App\Models\Order constants — must stay in sync.
const DELIVERY_NOVA_POSHTA = 1
const DELIVERY_SAMOVUVOZ = 2
const PAYMENT_CASH = 1
const PAYMENT_COD = 3

// Which delivery method each payment option is restricted to (empty = any).
const PAYMENT_DELIVERY_RESTRICTIONS = {
    [PAYMENT_CASH]: [DELIVERY_SAMOVUVOZ],
    [PAYMENT_COD]: [DELIVERY_NOVA_POSHTA],
}

const isPaymentDisabled = (paymentId) => {
    const restriction = PAYMENT_DELIVERY_RESTRICTIONS[paymentId]
    return !!restriction && !restriction.includes(form.delivery_method)
}

const changeDelivery = () => {
    if (isPaymentDisabled(form.payment_method)) {
        form.payment_method = null
    }
}

const backToCart = () => {
    router.visit(route('cart'), { preserveScroll: true })
}

// Prefill contact fields from the logged-in user (users table only has a
// single `name` column, unlike the order's separate customer_name/customer_surname).
const authUser = usePage().props.auth?.user
const [prefillName, ...prefillSurnameParts] = (authUser?.name || '').trim().split(' ')
const prefillSurname = prefillSurnameParts.join(' ')

const form = useForm({
    name: prefillName || '',
    surname: prefillSurname || '',
    phone: '',
    email: authUser?.email || '',
    comment: '',
    delivery_method: DELIVERY_NOVA_POSHTA,
    np_city_ref: null,
    np_city_name: null,
    np_warehouse_ref: null,
    np_warehouse_name: null,
    payment_method: null,
})

const cityModel = ref('');
const selectedCity = computed({
  get() {
    return cityModel.value
  },
  set(newValue) {
    if(warehousesTimer.value) clearTimeout(warehousesTimer.value)
    form.np_city_ref = newValue?.Ref || null
    form.np_city_name = newValue?.Description || null
    if(newValue?.Ref) {
        warehousesTimer.value = setTimeout(() => {
            getNPWarehouses()
        }, 800)
    }
    cityModel.value = newValue
  }
})
const warehouseModel = ref('');
const selectedWarehous = computed({
  get() {
    return warehouseModel.value
  },
  set(newValue) {
    form.np_warehouse_ref = newValue?.Ref || null
    form.np_warehouse_name = newValue?.Description || null
    warehouseModel.value = newValue
  }
})

const cityTimer = ref(null);
const warehousesTimer = ref(null);

const getNPCities = (event) => {
    if(cityTimer.value) clearTimeout(cityTimer.value)
    cityTimer.value = setTimeout(() => {
        if(event.query.trim().length) {
            router.post(route('np.cities'), {search: event.query.trim().toLowerCase()} ,{
                preserveScroll: true,
                only: ['cities'],
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
    })
}

const submitOrder = () => {
    form.post(route('order.store'))
}

</script>
<style scoped>
/* we will explain what these classes do next! */
.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
