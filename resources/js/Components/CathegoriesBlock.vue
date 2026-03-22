<template>
    <!-- Services -->
    <section
      class="mx-auto grid max-w-[1200px] grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-5 my-12"
    >
      <!-- 1: Tablecloths -->
      <div @click="openConsultation($t('Service4'))" class="relative group cursor-pointer overflow-hidden rounded-sm">
        <img
          class="aspect-[4/3] w-full object-cover brightness-[0.7] group-hover:brightness-100 group-hover:scale-110 transition-all duration-500"
          src="/images/service-tablecloth.png"
          alt="Tablecloths"
        />
        <div class="absolute inset-0 flex items-center justify-center p-4 bg-black/20 group-hover:bg-transparent transition-all duration-500">
          <p class="text-white text-xl font-bold uppercase tracking-widest text-center">
            {{ $t('Service4') }}
          </p>
        </div>
      </div>

      <!-- 2: Bedding -->
      <div @click="openConsultation($t('Service2'))" class="relative group cursor-pointer overflow-hidden rounded-sm">
        <img
          class="aspect-[4/3] w-full object-cover brightness-[0.7] group-hover:brightness-100 group-hover:scale-110 transition-all duration-500"
          src="/images/service-bedding.png"
          alt="Bedding sets"
        />
        <div class="absolute inset-0 flex items-center justify-center p-4 bg-black/20 group-hover:bg-transparent transition-all duration-500">
          <p class="text-white text-xl font-bold uppercase tracking-widest text-center">
            {{ $t('Service2') }}
          </p>
        </div>
      </div>

      <!-- 3: Pillows -->
      <div @click="openConsultation($t('Service3'))" class="relative group cursor-pointer overflow-hidden rounded-sm">
        <img
          class="aspect-[4/3] w-full object-cover brightness-[0.7] group-hover:brightness-100 group-hover:scale-110 transition-all duration-500"
          src="/images/service-pillows.png"
          alt="Decorative pillows"
        />
        <div class="absolute inset-0 flex items-center justify-center p-4 bg-black/20 group-hover:bg-transparent transition-all duration-500">
          <p class="text-white text-xl font-bold uppercase tracking-widest text-center">
            {{ $t('Service3') }}
          </p>
        </div>
      </div>

      <!-- 4: Curtains -->
      <div @click="openConsultation($t('Service1'))" class="relative group cursor-pointer overflow-hidden rounded-sm">
        <img
          class="aspect-[4/3] w-full object-cover brightness-[0.7] group-hover:brightness-100 group-hover:scale-110 transition-all duration-500"
          src="/images/service-curtains.png"
          alt="Curtain sewing"
        />
        <div class="absolute inset-0 flex items-center justify-center p-4 bg-black/20 group-hover:bg-transparent transition-all duration-500">
          <p class="text-white text-xl font-bold uppercase tracking-widest text-center">
            {{ $t('Service1') }}
          </p>
        </div>
      </div>

      <!-- 5: Repair -->
      <div @click="openConsultation($t('Service6'))" class="relative group cursor-pointer overflow-hidden rounded-sm">
        <img
          class="aspect-[4/3] w-full object-cover brightness-[0.7] group-hover:brightness-100 group-hover:scale-110 transition-all duration-500"
          src="/images/service-repair.jpg"
          alt="Clothing repair"
        />
        <div class="absolute inset-0 flex items-center justify-center p-4 bg-black/20 group-hover:bg-transparent transition-all duration-500">
          <p class="text-white text-xl font-bold uppercase tracking-widest text-center">
            {{ $t('Service6') }}
          </p>
        </div>
      </div>


      <!-- 6: HoReCa B2B -->
      <div @click="openConsultation('HoReCa / B2B')" class="relative group cursor-pointer overflow-hidden rounded-sm">
        <img
          class="aspect-[4/3] w-full object-cover brightness-[0.7] group-hover:brightness-100 group-hover:scale-110 transition-all duration-500"
          src="/images/service-covers.jpg"
          alt="Furniture covers"
        />
        <div class="absolute inset-0 flex items-center justify-center p-4 bg-black/20 group-hover:bg-transparent transition-all duration-500">
          <p class="text-white text-xl font-bold tracking-widest text-center">
            HoReCa<br>B2B
          </p>
        </div>
      </div>
    </section>
    <!-- /Services  -->

    <ConsultationModal ref="consultationModal" />
</template>

<script setup>
import { ref, defineAsyncComponent } from 'vue';

const ConsultationModal = defineAsyncComponent(() => import('./ConsultationModal.vue'));

const consultationModal = ref(null);

const openConsultation = (serviceName) => {
    if (consultationModal.value) {
        consultationModal.value.open(serviceName);
    }
};
</script>