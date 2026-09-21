<template>
    <GuestLayout>
        <Head title="Checkout">
            <meta name="robots" content="noindex, nofollow" />
        </Head>
        <div class="flex-grow">
          <section
            class="container mx-auto flex max-w-[1200px] flex-col py-5 lg:flex-row lg:py-10"
          >
            <!-- form  -->
            <section
              class="grid w-full min-w-0 max-w-[1200px] grid-cols-1 gap-3 px-5 pb-10"
            >
              <div class="card flex justify-center">
                  <Stepper v-model:value="activeStep" class="w-full min-w-0 lg:basis-[50rem]">
                      <StepList>
                          <Step v-for="step in steps" :key="step.value" :value="step.value">
                              <span v-if="activeStep === step.value" class="text-xs sm:hidden">{{ step.short }}</span>
                              <span class="hidden sm:inline">{{ step.label }}</span>
                          </Step>
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
                                  <Textarea v-model="form.comment" rows="4" class="mt-4 w-full" placeholder="Коментар" />
                                  <p v-if="form.errors.name" class="text-sm text-red-600 mt-2">{{ form.errors.name }}</p>
                                  <p v-if="form.errors.surname" class="text-sm text-red-600 mt-2">{{ form.errors.surname }}</p>
                                  <p v-if="form.errors.phone" class="text-sm text-red-600 mt-2">{{ form.errors.phone }}</p>
                                  <p v-if="form.errors.email" class="text-sm text-red-600 mt-2">{{ form.errors.email }}</p>
                                </div>
                              </div>
                              <div class="flex pt-6 justify-between">
                                  <Button label="Назад" severity="secondary" icon="pi pi-arrow-left" @click="backToCart()" />
                                  <Button label="Вперед" icon="pi pi-arrow-right" iconPos="right" @click="activateCallback('2')" />
                              </div>
                          </StepPanel>
                          <StepPanel v-slot="{ activateCallback }" value="2">
                              <div class="card flex flex-col justify-center gap-4 py-4">
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
                                  <p v-if="form.errors.delivery_method" class="text-sm text-red-600">{{ form.errors.delivery_method }}</p>
                                  <p v-if="form.errors.np_city_ref" class="text-sm text-red-600">{{ form.errors.np_city_ref }}</p>
                                  <p v-if="form.errors.np_warehouse_ref" class="text-sm text-red-600">{{ form.errors.np_warehouse_ref }}</p>
                              </div>
                              <div class="flex pt-6 justify-between">
                                  <Button label="Назад" severity="secondary" icon="pi pi-arrow-left" @click="activateCallback('1')" />
                                  <Button label="Вперед" icon="pi pi-arrow-right" iconPos="right" @click="activateCallback('3')" />
                              </div>
                          </StepPanel>
                          <StepPanel v-slot="{ activateCallback }" value="3">
                              <div class="card flex flex-col justify-center gap-4 py-4">
                                  <div v-for="(name, id) in payments" :key="id" class="flex items-center gap-2">
                                      <RadioButton v-model="form.payment_method" @update:modelValue="changePayment" :inputId="`payment_${id}`" name="payment" :value="Number(id)" :disabled="isPaymentDisabled(id)" />
                                      <label :for="`payment_${id}`">{{ name }}</label>
                                  </div>
                              </div>
                              <p v-if="form.errors.payment_method" class="text-sm text-red-600">{{ form.errors.payment_method }}</p>
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
            <Summary
              submits-order
              :processing="form.processing"
              @submit="submitOrder"
            />
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
const PAYMENT_TRANSFER = 2
const PAYMENT_COD = 3

// Which delivery method each payment option is restricted to (empty = any).
const PAYMENT_DELIVERY_RESTRICTIONS = {
    [PAYMENT_CASH]: [DELIVERY_SAMOVUVOZ],
    [PAYMENT_COD]: [DELIVERY_NOVA_POSHTA],
}

// The payment each delivery usually goes with, offered ready-made so the last
// step needs no thought.
const DEFAULT_PAYMENT_OF_DELIVERY = {
    [DELIVERY_NOVA_POSHTA]: PAYMENT_COD,
    [DELIVERY_SAMOVUVOZ]: PAYMENT_TRANSFER,
}

const isPaymentDisabled = (paymentId) => {
    const restriction = PAYMENT_DELIVERY_RESTRICTIONS[paymentId]
    return !!restriction && !restriction.includes(form.delivery_method)
}

// Tells a payment the buyer picked from one this page filled in for them.
// Without it, a default that happens to fit both deliveries (a money
// transfer) would survive a change of delivery and quietly stand in for the
// default the new delivery actually calls for.
const paymentChosenByBuyer = ref(false)

const changePayment = () => {
    paymentChosenByBuyer.value = true
}

const changeDelivery = () => {
    // The buyer's own choice stays, as long as the new delivery still allows
    // it. Anything else — an untouched default, or a choice this delivery
    // cannot take — gives way to the default of the delivery now selected.
    if (paymentChosenByBuyer.value && !isPaymentDisabled(form.payment_method)) return

    paymentChosenByBuyer.value = false
    form.payment_method = DEFAULT_PAYMENT_OF_DELIVERY[form.delivery_method] ?? null
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
    payment_method: DEFAULT_PAYMENT_OF_DELIVERY[DELIVERY_NOVA_POSHTA],
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

// Three labels never fit side by side on a phone. There only the step being
// filled in names itself, and the first one does so in a word that fits.
const steps = [
    { value: '1', label: 'Контактна інформація', short: 'Контакти' },
    { value: '2', label: 'Доставка', short: 'Доставка' },
    { value: '3', label: 'Оплата', short: 'Оплата' },
]

const activeStep = ref('1')

// Which step each field belongs to, so a rejected order can open the step
// that actually needs fixing. Without this a missing name is reported while
// the buyer looks at the payment step, and the button just does nothing.
const STEP_OF_FIELD = {
    name: '1', surname: '1', phone: '1', email: '1', comment: '1',
    delivery_method: '2', np_city_ref: '2', np_warehouse_ref: '2',
    payment_method: '3', cart: '3',
}

const submitOrder = () => {
    form.post(route('order.store'), {
        onError: (errors) => {
            const steps = Object.keys(errors)
                .map((field) => STEP_OF_FIELD[field])
                .filter(Boolean)
                .sort()

            if (steps.length) activeStep.value = steps[0]
        },
    })
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
