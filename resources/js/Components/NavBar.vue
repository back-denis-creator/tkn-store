<script setup>
import { ref, onMounted } from 'vue'
import { getActiveLanguage, loadLanguageAsync } from 'laravel-vue-i18n';
import { Link, usePage, useForm } from '@inertiajs/vue3';

const options = ref([
    { 
        icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask_uk" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
                <circle cx="12" cy="12" r="12" fill="white"/>
            </mask>
            <g mask="url(#mask_uk)">
                <rect width="24" height="24" fill="#3A99FF"/>
                <rect y="12" width="24" height="12" fill="#FFDA2C"/>
            </g>
        </svg>`, 
        name: 'Український', 
        value: 'uk' 
    },

    { 
        icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask_en" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
                <circle cx="12" cy="12" r="12" fill="white"/>
            </mask>
            <g mask="url(#mask_en)">
                <rect width="24" height="24" fill="#012169"/>
                <path d="M0 0l24 24M24 0L0 24" stroke="white" stroke-width="2.5"/>
                <path d="M0 0l24 24M24 0L0 24" stroke="#C8102E" stroke-width="1.5"/>
                <path d="M12 0v24M0 12h24" stroke="white" stroke-width="4"/>
                <path d="M12 0v24M0 12h24" stroke="#C8102E" stroke-width="2.5"/>
            </g>
        </svg>`, 
        name: 'English', 
        value: 'en' 
    },
]);
const form = useForm({})
const search = ref('')
const activeLang = getActiveLanguage()
const serverLang = usePage().props.locale
const selectLang = ref(options.value.find(({value}) => value === serverLang) || options.value[0])

onMounted(() => {
  if(serverLang !== activeLang) loadLanguageAsync(serverLang)
})

const updateLang = (lang) => {
  form.get(route('locale.set', lang.value), {
    only: ['locale'],
    preserveScroll: true
  })
}

const navigation = {
    pages: [
        { name: 'About Us', route: 'about' },
        { name: 'HoReCa', route: 'horeca' },
    ],
}
const mobileMenuOpen = ref(false)
const desktopMenuOpen = ref(false)

const isOpen = ref(false)
const setIsOpen = (value) => {
  isOpen.value = value
}
</script>
<template>
  <div>
    <Toast>
      <template #container="{ message, closeCallback }">
        <div class="flex items-start flex-auto justify-between">
          <div class="my-2 mx-2">{{ message.summary }}</div>
          <Button icon="pi pi-times" severity="success" variant="text" text rounded aria-label="Cancel" @click="closeCallback()" />
        </div>
      </template>
    </Toast>
    <!-- Header -->
    <header
      class="sticky top-0 z-50 bg-white shadow-sm"
    >
      <div class="mx-auto flex h-16 max-w-[1200px] items-center justify-between px-5">
      <Link href="/" class="flex items-center">
        <span class="text-2xl font-bold text-gray-900 tracking-[0.2em] uppercase font-sans">casanel</span>
      </Link>

      <div class="flex items-center gap-4 z-40">
          <!-- Language Selector -->
          <div class="hidden lg:block relative group">
              <button
                  type="button"
                  class="inline-flex items-center justify-center px-2 py-2 bg-transparent text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-all"
                  :id="selectLang.value"
              >
                  <div :class="`fi fis fiCircle inline-block fi-${selectLang.value}`" v-html="selectLang.icon" />
              </button>
              
              <!-- Language dropdown on hover -->
              <div
                  class="absolute right-0 mt-0 w-40 pt-2 invisible opacity-0 translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 ease-out z-[60]"
              >
                  <div class="bg-white rounded-md shadow-xl ring-1 ring-black ring-opacity-5 overflow-hidden border border-gray-100">
                      <div class="py-1">
                          <button
                              v-for="language in options"
                              :key="language.value"
                              @click="updateLang(language)"
                              class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-600 flex items-center transition-colors border-b border-gray-50 last:border-none"
                              :class="{'bg-gray-50 font-semibold': selectLang.value === language.value}"
                          >
                              <div :class="`fi fis fiCircle inline-block mr-3 fi-${language.value}`" v-html="language.icon" />
                              <span>{{ language.name }}</span>
                          </button>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Desktop Menu (Hover) -->
          <div class="hidden lg:block relative group">
              <button
                  class="inline-flex items-center justify-center px-4 py-2 bg-transparent text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-all"
              >
                  <i class="pi pi-bars my-1 text-amber-500"></i>
                  <!-- {{ $t('Menu', 'Меню') }} -->
              </button>
              
              <!-- Beautifully appearing menu on hover -->
              <div
                  class="absolute right-0 mt-0 w-48 pt-2 invisible opacity-0 translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 ease-out"
              >
                  <div class="bg-white rounded-md shadow-xl ring-1 ring-black ring-opacity-5 overflow-hidden border border-gray-100">
                      <div class="py-1">
                          <Link 
                              v-for="page in navigation.pages" 
                              :key="page.name"
                              :href="page.href ? page.href : route(page.route)"
                              class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-600 transition-colors border-b border-gray-50 last:border-none"
                          >
                              <i :class="`pi ${page.name === 'Contacts' ? 'pi-phone' : (page.name === 'About Us' ? 'pi-info-circle' : 'pi-briefcase')} mr-3 text-amber-500 text-lg overflow-hidden`" ></i>
                              {{ $t(page.name) }}
                          </Link>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Mobile Burger Menu -->
          <button 
              @click="mobileMenuOpen = true"
              class="lg:hidden p-2 rounded-md text-amber-500 hover:bg-gray-100 transition-colors"
          >
              <i class="pi pi-bars text-xl"></i>
          </button>
      </div>

      <!-- Mobile Drawer -->
      <Drawer v-model:visible="mobileMenuOpen" position="right" class="!w-72">
          <template #header>
              <div class="flex items-center gap-2">
                <span class="text-xl font-bold tracking-widest uppercase">casanel</span>
              </div>
          </template>
          
          <div class="flex flex-col gap-6 py-4">
              <div class="flex flex-col gap-1">
                  <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-2 mb-2">{{ $t('Navigation', 'Навігація') }}</p>
                  <Link 
                      v-for="page in navigation.pages" 
                      :key="page.name"
                      :href="page.href ? page.href : route(page.route)"
                      @click="mobileMenuOpen = false"
                      class="flex items-center gap-3 px-3 py-4 text-gray-700 hover:bg-amber-50 rounded-lg transition-colors border-b border-gray-50"
                  >
                      <i :class="`pi ${page.name === 'Contacts' ? 'pi-phone' : (page.name === 'About Us' ? 'pi-info-circle' : 'pi-briefcase')} text-amber-500 text-lg overflow-hidden`" ></i>
                      <span class="font-medium">{{ $t(page.name) }}</span>
                  </Link>
              </div>

              <div class="mt-4">
                  <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-2 mb-2">{{ $t('Language', 'Мова') }}</p>
                  <div class="grid grid-cols-1 gap-2">
                      <button
                          v-for="language in options"
                          :key="language.value"
                          @click="updateLang(language)"
                          class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all border"
                          :class="selectLang.value === language.value ? 'border-amber-400 bg-amber-50 text-amber-700' : 'border-gray-100 text-gray-600'"
                      >
                          <div :class="`fi fis fiCircle inline-block fi-${language.value}`" v-html="language.icon" />
                          <span>{{ language.name }}</span>
                      </button>
                  </div>
              </div>
          </div>
      </Drawer>

      </div>
    </header>
    <!-- /Header -->

    <!-- /Nav bar -->

    <!-- Menu  -->
    <section
      v-if="false && desktopMenuOpen"
      @click.outside="desktopMenuOpen = false"
      class="absolute left-0 right-0 z-10 w-full border-b border-r border-l bg-white"
    >
      <div class="mx-auto flex max-w-[1200px] py-10">
        <div class="w-[300px] border-r">
          <ul class="px-5">
            <li
              class="active:blue-900 flex items-center gap-2 bg-amber-400 py-2 px-3 active:bg-amber-400"
            >
              <img
                width="15px"
                height="15px"
                src="/images/bed.svg"
                alt="Bedroom icon"
              />
              Bedroom
              <span class="ml-auto"
                ><svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="h-4 w-4"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M8.25 4.5l7.5 7.5-7.5 7.5"
                  />
                </svg>
              </span>
            </li>

            <li
              class="active:blue-900 flex items-center gap-2 py-2 px-3 hover:bg-neutral-100 active:bg-amber-400"
            >
              <img
                width="15px"
                height="15px"
                src="/images/sleep.svg"
                alt="bedroom icon"
              />
              Matrass
              <span class="ml-auto"
                ><svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="h-4 w-4"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M8.25 4.5l7.5 7.5-7.5 7.5"
                  />
                </svg>
              </span>
            </li>

            <li
              class="active:blue-900 flex items-center gap-2 py-2 px-3 hover:bg-neutral-100 active:bg-amber-400"
            >
              <img
                width="15px"
                height="15px"
                src="/images/outdoor.svg"
                alt="bedroom icon"
              />
              Outdoor
              <span class="ml-auto"
                ><svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="h-4 w-4"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M8.25 4.5l7.5 7.5-7.5 7.5"
                  />
                </svg>
              </span>
            </li>

            <li
              class="active:blue-900 flex items-center gap-2 py-2 px-3 hover:bg-neutral-100 active:bg-amber-400"
            >
              <img
                width="15px"
                height="15px"
                src="/images/sofa.svg"
                alt="bedroom icon"
              />
              Sofa
              <span class="ml-auto"
                ><svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="h-4 w-4"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M8.25 4.5l7.5 7.5-7.5 7.5"
                  />
                </svg>
              </span>
            </li>

            <li
              class="active:blue-900 flex items-center gap-2 py-2 px-3 hover:bg-neutral-100 active:bg-amber-400"
            >
              <img
                width="15px"
                height="15px"
                src="/images/kitchen.svg"
                alt="bedroom icon"
              />
              Kitchen
              <span class="ml-auto"
                ><svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="h-4 w-4"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M8.25 4.5l7.5 7.5-7.5 7.5"
                  />
                </svg>
              </span>
            </li>

            <li
              class="active:blue-900 flex items-center gap-2 py-2 px-3 hover:bg-neutral-100 active:bg-amber-400"
            >
              <img
                width="15px"
                height="15px"
                src="/images/food.svg"
                alt="Food icon"
              />
              Living room
              <span class="ml-auto"
                ><svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="h-4 w-4"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M8.25 4.5l7.5 7.5-7.5 7.5"
                  />
                </svg>
              </span>
            </li>
          </ul>
        </div>

        <div class="flex w-full justify-between">
          <div class="flex gap-6">
            <div class="mx-5">
              <p class="font-medium text-gray-500">BEDS</p>
              <ul class="text-sm leading-8">
                <li><a href="product-overview.html">Italian bed</a></li>
                <li><a href="product-overview.html">Queen-size bed</a></li>
                <li><a href="product-overview.html">Wooden craft bed</a></li>
                <li><a href="product-overview.html">King-size bed</a></li>
              </ul>
            </div>

            <div class="mx-5">
              <p class="font-medium text-gray-500">LAMPS</p>
              <ul class="text-sm leading-8">
                <li><a href="product-overview.html">Italian Purple Lamp</a></li>
                <li><a href="product-overview.html">APEX Lamp</a></li>
                <li><a href="product-overview.html">PIXAR lamp</a></li>
                <li><a href="product-overview.html">Ambient Nightlamp</a></li>
              </ul>
            </div>

            <div class="mx-5">
              <p class="font-medium text-gray-500">BEDSIDE TABLES</p>
              <ul class="text-sm leading-8">
                <li><a href="product-overview.html">Purple Table</a></li>
                <li><a href="product-overview.html">Easy Bedside</a></li>
                <li><a href="product-overview.html">Soft Table</a></li>
                <li><a href="product-overview.html">Craft Table</a></li>
              </ul>
            </div>

            <div class="mx-5">
              <p class="font-medium text-gray-500">SPECIAL</p>
              <ul class="text-sm leading-8">
                <li><a href="product-overview.html">Humidifier</a></li>
                <li><a href="product-overview.html">Bed Cleaner</a></li>
                <li><a href="product-overview.html">Vacuum Cleaner</a></li>
                <li><a href="product-overview.html">Pillow</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
<style scoped>
  :deep(.custom-badge) {
    position: absolute;
    margin-right: -20px;
    bottom: 0;
  }
  :deep(.custom-cart-size) {
    font-size: 1.5rem !important;
  }
  :deep(.p-overlaybadge .p-badge) {
    min-width: 1rem;
    height: 1rem;
  }
  .fiCircle {
    width: 24px !important;
    height: 24px !important;
    font-size: 24px !important;
    border-radius: 100%;
    overflow: hidden;
    border: none;
    box-shadow: inset 0 0 0 2px rgba(0, 0, 0, .06);
    background: white;
  }
</style>