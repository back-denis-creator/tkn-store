<template>
    <!-- Offer image  -->

    <div class="relative overflow-hidden">
      <img
        class="w-full object-cover object-center brightness-[0.4] filter lg:h-[650px] transition-all duration-700 hover:scale-105"
        src="/images/hero-bg.png"
        alt="Custom sewing workshop"
      />

      <div
        class="absolute top-1/2 left-1/2 mx-auto flex w-11/12 max-w-[1200px] -translate-x-1/2 -translate-y-1/2 flex-col text-center text-white lg:ml-5 lg:items-start lg:text-left"
      >
        <h1 class="text-4xl font-bold sm:text-6xl leading-tight">
          {{ $t('HeroHeadline') }}
        </h1>
        <p class="pt-5 text-sm sm:text-xl lg:w-3/5 font-light leading-relaxed">
          {{ $t('HeroSubline') }}
        </p>
        <p class="text-sm sm:text-xl lg:w-4/5 font-light leading-relaxed">
          {{ $t('HeroSubline2') }}
        </p>
        <button
          @click="openConsultation"
          class="mx-auto mt-8 bg-amber-400 px-8 py-3 text-black font-semibold rounded-sm duration-200 hover:bg-yellow-300 transform hover:-translate-y-1 transition-all lg:mx-0 shadow-lg"
        >
          {{ $t('HeroCTA') }}
        </button>
      </div>
    </div>
    
    <ConsultationModal ref="consultationModal" />

    <!-- /Offer image  -->
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import ConsultationModal from './ConsultationModal.vue';

  const consultationModal = ref(null);

  const openConsultation = () => {
      consultationModal.value?.open();
  };
  </script>
  