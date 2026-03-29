<script setup>
import { ref, onMounted } from 'vue'
import { getActiveLanguage, loadLanguageAsync } from 'laravel-vue-i18n';
import { Link, usePage, useForm } from '@inertiajs/vue3';

const options = ref([
    { icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#clip0_5293_53603)">
<rect width="24" height="24" rx="12" fill="white"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M0 0H24V12H0V0Z" fill="#3A99FF"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M0 12H24V24H0V12Z" fill="#FFDA2C"/>
</g>
<defs>
<clipPath id="clip0_5293_53603">
<rect width="24" height="24" rx="12" fill="white"/>
</clipPath>
</defs>
</svg>`, name: 'Український', value: 'uk' },

    { icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<mask id="mask0_2283_149218" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
<circle cx="12" cy="12" r="12" fill="#C4C4C4"/>
</mask>
<g mask="url(#mask0_2283_149218)">
<rect x="-3.78906" width="31.579" height="24" fill="url(#pattern2)"/>
</g>
<defs>
<pattern id="pattern2" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_2283_149218" transform="matrix(0.0158333 0 0 0.0208333 -0.00666661 0)"/>
</pattern>
<image id="image0_2283_149218" width="64" height="48" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAAAwCAYAAAChS3wfAAAACXBIWXMAABYlAAAWJQFJUiTwAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAGaSURBVHgB7Zi/SgNBEMa/kzMIiYWVBpJCxDZ9GgOiL6BY2Qu2koBd0t8D+AIWYiy1MViktEhjaUhlEQNCCu9QVIjuwh13kqSbnSWzP7g/e98Wy+x9M8N6ld3LCSzmrn8OSpYgnEwAtrfW5k7m1inQASiu5/XVrFf1czW/nJnErVPiqRygIh80ayhu5DF8jXDa6GA4ipJJnPrj/TEo0X9AfzBG72mkP9x2BpnF2aBTkuSA579F7B209Q7EpD3JoZvAm1YGlQ8VQauGRquLMPzCe/RtTE/zUOmBkqkB4M4JaVj6gEX2/H9m9gHcOcEU2gLcnp+nd09yoMT1AeomuQ/w45fYkzvVUiKqnVGL49IVnzdtUGJ9H8BSBguFHC6Cfb1D6qnGJnWTiO8D/PSA2/NpPWbl8AiUWN8HGCmD3J7nzAmuD4hfZtVhbv1tswxKPHcsLhzxAfA+fiZWW4AaZwEIxw/rZ5CM91IuuRwgGVcGx1fXoi3g+gAIx6c+dbUdVwYhHJcDqE9dbceVQQjH5QDqU1fbcRaAcMQH4BcJFg/2Rbu2uwAAAABJRU5ErkJggg=="/>
</defs>
</svg>`, name: 'English', value: 'en' },
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
        { name: 'Contacts', route: 'contacts' },
        { name: 'Catalog', route: 'catalog' },
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

      <div class="flex items-center z-40">
          <div class="relative inline-block text-left">
              <div>
                <!-- focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 -->
                  <button
                      @click="setIsOpen(!isOpen)"
                      type="button"
                      class="inline-flex items-center justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none"
                      :id="selectLang.value"
                      aria-haspopup="true"
                      :aria-expanded="isOpen"
                  >
                      <div :class="`fi fis fiCircle inline-block mr-2 fi-${selectLang.value}`" v-html="selectLang.icon" />
                      {{ selectLang.name }}
                  </button>
              </div>
              <div
                  v-if="isOpen"
                  class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                  role="menu"
                  aria-orientation="vertical"
                  aria-labelledby="language-selector"
              >
                  <div class="py-1 grid grid-cols-1 gap-2" role="none">
                      <button
                          v-for="(language, index) in options"
                          :key="language.value"
                          @click="updateLang(language)"
                          :class="`${selectLang.value === language.value ? 'bg-gray-100 text-gray-900' : 'text-gray-700'}  block px-4 py-2 text-sm text-left items-center inline-flex hover:bg-gray-100 ${index % 2 === 0 ? 'rounded-r' : 'rounded-l'}`"
                          role="menuitem"
                      >
                          <div :class="`fi fis fiCircle inline-block mr-2 fi-${language.value}`" v-html="language.icon" />
                          <span class="truncate">{{ language.name }}</span>
                      </button>
                  </div>
              </div>
          </div>
      </div>

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
    border: none;
    box-shadow: inset 0 0 0 2px rgba(0, 0, 0, .06);
    background: white;
  }
</style>