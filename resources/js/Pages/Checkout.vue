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
                                </div>
                              </div>
                              <div class="flex pt-6 justify-between">
                                  <Button label="Назад" severity="secondary" icon="pi pi-arrow-left" @click="backToCart()" />
                                  <Button label="Вперед" icon="pi pi-arrow-right" iconPos="right" @click="activateCallback('2')" />
                              </div>
                          </StepPanel>
                          <StepPanel v-slot="{ activateCallback }" value="2">
                              <div class="card flex flex-col h-48 justify-center gap-4">
                                  <div v-for="delivery in deliveries" :key="delivery.key" class="flex items-center gap-2">
                                      <RadioButton v-model="selectedDelivery" @update:modelValue="changeDelivery" :inputId="delivery.key" name="dynamic" :value="delivery.key" />
                                      <label :for="delivery.key">{{ delivery.name }}</label>
                                  </div>
                                  <Transition>
                                    <div v-if="selectedDelivery === '2'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                      <AutoComplete v-model="selectedCity" placeholder="Місто" optionLabel="Description" :suggestions="cities" @complete="getNPCities" inputClass="w-full" />
                                      <AutoComplete v-model="selectedWarehous" placeholder="№ Відділення" optionLabel="Description" :suggestions="warehouses" @complete="getNPWarehouses" inputClass="w-full" dropdown :disabled="!selectedCity?.Ref" />
                                    </div>
                                  </Transition>
                              </div>
                              <div class="flex pt-6 justify-between">
                                  <Button label="Назад" severity="secondary" icon="pi pi-arrow-left" @click="activateCallback('1')" />
                                  <Button label="Вперед" icon="pi pi-arrow-right" iconPos="right" @click="activateCallback('3')" />
                              </div>
                          </StepPanel>
                          <StepPanel v-slot="{ activateCallback }" value="3">
                              <div class="card flex flex-col h-48 justify-center gap-4">
                                  <div v-for="payment in payments" :key="payment.key" class="flex items-center gap-2">
                                      <RadioButton v-model="selectedPayment" :inputId="payment.key" name="dynamic" :value="payment.key" :disabled="!!payment.delivery.length && !payment.delivery.includes(selectedDelivery)" />
                                      <label :for="payment.key">{{ payment.name }}</label>
                                  </div>
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
            <Summary />
          </section>
          <ConsBages />
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
    }
})

const payments = ref([
    { name: 'Готівка', key: '1', delivery: ['1'] },
    { name: 'Грошовий переказ', key: '2', delivery: [] },
    { name: 'Післяплата (Нова Пошта)', key: '3', delivery: ['2'] },
])
const selectedPayment = ref('2')
const deliveries = ref([
    { name: 'Самовивіз (м. Дніпро)', key: '1' },
    { name: 'Нова Пошта', key: '2' },
])
const selectedDelivery = ref('2')

const changeDelivery = (newId) => {
  if(newId === '1' && selectedPayment.value === '3') {
    selectedPayment.value = '2'
  } else if(newId === '2' && selectedPayment.value === '1') {
    selectedPayment.value = '2'
  }
}

const backToCart = () => {
    router.visit(route('cart'), { preserveScroll: true })
}

const form = useForm({
    name: '',
    surname: '',
    phone: '',
    email: '',
    comment: ''
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