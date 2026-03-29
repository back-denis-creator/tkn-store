<template>
  <p class="mx-auto mt-16 mb-8 max-w-[1200px] px-5 text-2xl font-bold uppercase tracking-widest text-gray-800 text-center lg:text-left">
    {{ $t('PortfolioTitle') }}
  </p>

  <!-- Slider  -->
  <Carousel 
    v-if="portfolioItems.length > 0" 
    :value="portfolioItems" 
    :numVisible="4" 
    :numScroll="1" 
    :responsiveOptions="responsiveOptions" 
    circular 
    :autoplayInterval="3000" 
    :showIndicators="false"
    class="mx-auto max-w-[1200px] px-5 py-2 mb-10"
  >
      <template #item="slotProps">
        <div class="flex flex-col m-2 group cursor-pointer" @click="imageClick(slotProps.index)">
            <div class="overflow-hidden rounded-sm shadow-sm ring-1 ring-gray-100 bg-gray-50 portfolio-image-container">
                <Image 
                  :src="slotProps.data.image" 
                  alt="portfolio item" 
                  class="w-full"
                  :pt="{
                    image: { class: 'w-full aspect-square object-cover transition-transform duration-500 group-hover:scale-110' }
                  }"
                />
            </div>
        </div>
      </template>
  </Carousel>
  <div v-else class="flex justify-center items-center h-48 text-gray-400 italic">
    {{ $t('PortfolioEmpty', 'Додаємо нові работы...') }}
  </div>

  <!-- Full Screen Galleria -->
  <Galleria 
    v-model:activeIndex="activeIndex" 
    v-model:visible="displayCustom" 
    :value="portfolioItems" 
    :responsiveOptions="responsiveOptions" 
    :circular="true" 
    :fullScreen="true" 
    :showItemNavigators="true" 
    :showThumbnails="false"
    :pt="{
        itemPrevButton: {
            class: 'custom-galleria-nav !border-amber-400 !bg-white/10 hover:!bg-amber-400 !text-white hover:!text-black !transition-all !w-12 !h-12 !rounded-full !shadow-lg'
        },
        itemNextButton: {
            class: 'custom-galleria-nav !border-amber-400 !bg-white/10 hover:!bg-amber-400 !text-white hover:!text-black !transition-all !w-12 !h-12 !rounded-full !shadow-lg'
        },
        closeButton: {
            class: '!fixed !top-5 !right-5 !z-[100] !bg-black/50 hover:!bg-black/80 !text-white !p-2 !rounded-full !border-none !w-10 !h-10 !flex !items-center !justify-center transition-all'
        }
    }"
  >
      <template #item="slotProps">
          <img :src="slotProps.item.image" alt="portfolio item" class="max-h-screen object-contain" />
      </template>
  </Galleria>

</template>

<script setup>
import { ref, onMounted } from "vue"

// Automatically load all images from public/images/works
const portfolioItems = ref([]);
const displayCustom = ref(false);
const activeIndex = ref(0);

const imageClick = (index) => {
    activeIndex.value = index;
    displayCustom.value = true;
};

onMounted(() => {
    // Vite's import.meta.glob can find all images in the directory
    // We map over the object to get the keys (which are the file paths)
    const images = import.meta.glob('/public/images/works/*.{png,jpg,jpeg,JPG}', { eager: true });
    
    portfolioItems.value = Object.keys(images).map(path => ({
        // We strip '/public' from the path as it's the root for the dev server
        image: path.replace('/public', '')
    }));
});

const responsiveOptions = ref([
    {
        breakpoint: '1400px',
        numVisible: 4,
        numScroll: 1
    },
    {
        breakpoint: '1199px',
        numVisible: 3,
        numScroll: 1
    },
    {
        breakpoint: '767px',
        numVisible: 2,
        numScroll: 1
    },
    {
        breakpoint: '575px',
        numVisible: 1,
        numScroll: 1
    }
]);
</script>

<style>
/* Custom styling for Portfolio Slider */
.p-carousel .p-carousel-prev,
.p-carousel .p-carousel-next {
    background-color: white;
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
    border-radius: 9999px;
    width: 2.5rem;
    height: 2.5rem;
    transition: all 0.3s;
    z-index: 10;
}

.p-carousel .p-carousel-prev:hover,
.p-carousel .p-carousel-next:hover {
    background-color: #fbbf24;
    color: white;
}

/* Ensure PrimeVue Image preview button takes full area for hover activation */
.portfolio-image-container .p-image {
    display: block;
    width: 100%;
}

.portfolio-image-container .p-image-preview-indicator {
    background-color: rgba(0, 0, 0, 0.2) !important;
}

.portfolio-image-container .p-image-preview-icon {
    font-size: 1.5rem;
    color: white;
}
/* Ensure Galleria navigators have yellow border if PrimeVue default is green */
.custom-galleria-nav {
    border: 2px solid #fbbf24 !important;
}

.custom-galleria-nav:hover {
    background-color: #fbbf24 !important;
}
</style>