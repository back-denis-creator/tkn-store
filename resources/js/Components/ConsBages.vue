<template>
    <section
      class="container mx-auto my-6 lg:my-12 flex flex-col justify-center gap-4 lg:gap-6 lg:flex-row px-5"
    >
      <!-- 1: Individual Approach -->
      <div 
        class="relative flex-1 min-h-[110px] lg:min-h-[110px] cursor-pointer"
        @click="toggleActive(0)"
      >
        <div
          class="absolute top-0 left-0 w-full flex flex-col items-start border border-gray-100 bg-white p-6 transition-all duration-500 rounded-sm"
          :class="[activeIndex === 0 ? 'shadow-xl z-30' : 'shadow-sm z-10']"
        >
          <div class="flex flex-row items-center w-full">
              <div 
                class="p-3 rounded-full transition-colors duration-300 shrink-0"
                :class="[activeIndex === 0 ? 'bg-amber-100' : 'bg-amber-50']"
              >
                  <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="h-6 w-6 lg:h-8 lg:w-8 text-amber-500"
                  >
                      <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"
                      />
                  </svg>
              </div>
              <div class="ml-4 lg:ml-5 flex flex-col">
                  <h3 class="text-xs lg:text-sm font-bold text-gray-800 uppercase tracking-wider">{{ $t('Benefit1Title') }}</h3>
                  <p class="text-gray-500 text-[10px] lg:text-xs mt-1">
                      {{ $t('Benefit1Sub') }}
                  </p>
              </div>
          </div>

          <div 
            class="overflow-hidden opacity-0 transition-all duration-700 ease-in-out w-full"
            :class="[activeIndex === 0 ? 'max-h-[600px] opacity-100 mt-4' : 'max-h-0']"
          >
            <p 
                class="text-gray-500 text-xs mt-0 leading-relaxed border-t border-gray-50 pt-4 italic text-justify"
                v-html="$t('Benefit1Detail')"
            ></p>
            <div class="flex justify-end mt-4 lg:hidden">
                <span class="text-amber-500 text-[10px] font-bold uppercase tracking-widest">{{ $t('Close', 'Закрити') }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 2: Quality Materials -->
      <div 
        class="relative flex-1 min-h-[110px] lg:min-h-[110px] cursor-pointer"
        @click="toggleActive(1)"
      >
        <div
          class="absolute top-0 left-0 w-full flex flex-col items-start border border-gray-100 bg-white p-6 transition-all duration-500 rounded-sm"
          :class="[activeIndex === 1 ? 'shadow-xl z-30' : 'shadow-sm z-10']"
        >
          <div class="flex flex-row items-center w-full">
              <div 
                class="p-3 rounded-full transition-colors duration-300 shrink-0"
                :class="[activeIndex === 1 ? 'bg-amber-100' : 'bg-amber-50']"
              >
                  <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="h-6 w-6 lg:h-8 lg:w-8 text-amber-500"
                  >
                      <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.744c0 5.633 3.409 10.473 8.25 12.526a11.959 11.959 0 008.25-12.526c0-1.334-.218-2.617-.622-3.821A11.959 11.959 0 0112 2.714z"
                      />
                  </svg>
              </div>
              <div class="ml-4 lg:ml-5 flex flex-col">
                  <h3 class="text-xs lg:text-sm font-bold text-gray-800 uppercase tracking-wider">{{ $t('Benefit2Title') }}</h3>
                  <p class="text-gray-500 text-[10px] lg:text-xs mt-1">
                      {{ $t('Benefit2Sub') }}
                  </p>
              </div>
          </div>

          <div 
            class="overflow-hidden opacity-0 transition-all duration-700 ease-in-out w-full"
            :class="[activeIndex === 1 ? 'max-h-[600px] opacity-100 mt-4' : 'max-h-0']"
          >
            <p 
                class="text-gray-500 text-xs mt-0 leading-relaxed border-t border-gray-50 pt-4 italic text-justify"
                v-html="$t('Benefit2Detail')"
            ></p>
            <div class="flex justify-end mt-4 lg:hidden">
                <span class="text-amber-500 text-[10px] font-bold uppercase tracking-widest">{{ $t('Close', 'Закрити') }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 3: Own Production -->
      <div 
        class="relative flex-1 min-h-[110px] lg:min-h-[110px] cursor-pointer"
        @click="toggleActive(2)"
      >
        <div
          class="absolute top-0 left-0 w-full flex flex-col items-start border border-gray-100 bg-white p-6 transition-all duration-500 rounded-sm"
          :class="[activeIndex === 2 ? 'shadow-xl z-30' : 'shadow-sm z-10']"
        >
          <div class="flex flex-row items-center w-full">
              <div 
                class="p-3 rounded-full transition-colors duration-300 shrink-0"
                :class="[activeIndex === 2 ? 'bg-amber-100' : 'bg-amber-50']"
              >
                  <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="h-6 w-6 lg:h-8 lg:w-8 text-amber-500"
                  >
                      <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"
                      />
                      <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1.001A3.75 3.75 0 0012 18z"
                      />
                  </svg>
              </div>
              <div class="ml-4 lg:ml-5 flex flex-col">
                  <h3 class="text-xs lg:text-sm font-bold text-gray-800 uppercase tracking-wider">{{ $t('Benefit3Title') }}</h3>
                  <p class="text-gray-500 text-[10px] lg:text-xs mt-1">
                      {{ $t('Benefit3Sub') }}
                  </p>
              </div>
          </div>

          <div 
            class="overflow-hidden opacity-0 transition-all duration-700 ease-in-out w-full"
            :class="[activeIndex === 2 ? 'max-h-[600px] opacity-100 mt-4' : 'max-h-0']"
          >
            <p 
                class="text-gray-500 text-xs mt-0 leading-relaxed border-t border-gray-50 pt-4 italic text-justify"
                v-html="$t('Benefit3Detail')"
            ></p>
            <div class="flex justify-end mt-4 lg:hidden">
                <span class="text-amber-500 text-[10px] font-bold uppercase tracking-widest">{{ $t('Close', 'Закрити') }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>
</template>

<script setup>
import { ref } from 'vue';

const activeIndex = ref(null);

const toggleActive = (index) => {
    if (activeIndex.value === index) {
        activeIndex.value = null;
    } else {
        activeIndex.value = index;
    }
};
</script>