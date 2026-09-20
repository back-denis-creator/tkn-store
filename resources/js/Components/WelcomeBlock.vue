<template>
    <!-- Offer image  -->

    <div class="relative overflow-hidden" @mouseenter="isHovering = true" @mouseleave="isHovering = false">
      <div class="relative h-[600px] w-full lg:h-[650px]">
        <img
          v-for="(slide, index) in displaySlides"
          :key="slide.id ?? 'default'"
          :src="slide.url"
          alt="Custom sewing workshop"
          class="absolute inset-0 h-full w-full object-cover object-center brightness-[0.35] filter transition-opacity duration-1000 ease-in-out"
          :class="index === activeIndex ? 'opacity-100' : 'opacity-0'"
        />
      </div>

      <div
        class="absolute top-1/2 left-1/2 mx-auto flex w-11/12 max-w-[1200px] -translate-x-1/2 -translate-y-1/2 flex-col text-center text-white lg:items-start lg:text-left"
      >
        <h1 class="text-3xl font-bold sm:text-6xl leading-tight drop-shadow-lg">
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

      <div v-if="displaySlides.length > 1" class="absolute bottom-6 left-1/2 flex -translate-x-1/2 gap-2">
        <button
          v-for="(slide, index) in displaySlides"
          :key="slide.id ?? 'default'"
          type="button"
          @click="activeIndex = index"
          class="h-2 rounded-full transition-all duration-300"
          :class="index === activeIndex ? 'w-6 bg-amber-400' : 'w-2 bg-white/50 hover:bg-white/80'"
          :aria-label="`Слайд ${index + 1}`"
        />
      </div>
    </div>

    <ConsultationModal ref="consultationModal" />

    <!-- /Offer image  -->
  </template>

  <script setup>
  import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
  import ConsultationModal from './ConsultationModal.vue';

  const props = defineProps({
      heroSlides: {
          type: Array,
          default: () => ([]),
      },
  });

  // Falls back to the original static image whenever the admin hasn't
  // uploaded any slides yet — the hero must never render blank.
  const displaySlides = computed(() => (
      props.heroSlides.length ? props.heroSlides : [{ id: null, url: '/images/hero-bg.webp' }]
  ));

  const activeIndex = ref(0);
  const isHovering = ref(false);
  let timer = null;

  const stopRotation = () => {
      if (timer) {
          clearInterval(timer);
          timer = null;
      }
  };

  const startRotation = () => {
      stopRotation();
      if (displaySlides.value.length <= 1) return;
      timer = setInterval(() => {
          if (isHovering.value) return;
          activeIndex.value = (activeIndex.value + 1) % displaySlides.value.length;
      }, 6000);
  };

  // A slide upload/delete in the admin changes the slide count while a
  // visitor already has the page open (Inertia keeps the page mounted) —
  // restart so the interval matches the new slide list instead of looping
  // over stale indexes.
  watch(displaySlides, () => {
      activeIndex.value = 0;
      startRotation();
  });

  onMounted(startRotation);
  onUnmounted(stopRotation);

  const consultationModal = ref(null);

  const openConsultation = () => {
      consultationModal.value?.open();
  };
  </script>
