<script setup>
import { ref, onMounted } from 'vue'
import { getActiveLanguage, loadLanguageAsync } from 'laravel-vue-i18n';
import { Link, usePage, useForm } from '@inertiajs/vue3';
import SelectButton from 'primevue/selectbutton';

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
</svg>`, value: 'uk' },
    { icon: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#clip0_5290_53556)">
<rect width="24" height="24" rx="12" fill="#1A47B8"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M0 16H24V24H0V16Z" fill="#F93939"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M0 0H24V8H0V0Z" fill="white"/>
</g>
<defs>
<clipPath id="clip0_5290_53556">
<rect width="24" height="24" rx="12" fill="white"/>
</clipPath>
</defs>
</svg>`, value: 'ru' },
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
</svg>`, value: 'en' },
]);
const form = useForm({})
const activeLang = getActiveLanguage()
const serverLang = usePage().props.locale
const selectLang = ref(options.value.find(({value}) => value === serverLang))

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
        { name: 'Контакти', route: 'contacts' },
        { name: 'Каталог', route: 'catalog' },
    ],
}
const mobileMenuOpen = ref(false)
const desktopMenuOpen = ref(false)
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
      class="mx-auto flex h-16 max-w-[1200px] items-center justify-between px-5"
    >
      <Link href="/">
        <img
            class="cursor-pointer sm:h-auto sm:w-auto"
            src="/images/company-logo.svg"
            alt="company logo"
          />
      </Link>

      <div class="md:hidden">
        <button @click="mobileMenuOpen = !mobileMenuOpen">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="h-8 w-8"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
            />
          </svg>
        </button>
      </div>

      <form class="hidden h-9 w-2/5 items-center border md:flex">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="mx-3 h-4 w-4"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
          />
        </svg>

        <input
          class="hidden h-9 border-inherit w-11/12 outline-none md:block"
          type="search"
          placeholder="Пошук"
        />

        <button class="ml-auto h-full bg-amber-400 px-4 hover:bg-yellow-300">
          {{ $t('Search') }}
        </button>
      </form>
      <SelectButton v-model="selectLang" :options="options" @update:modelValue="updateLang" optionLabel="value" dataKey="value" aria-labelledby="custom">
        <template #option="slotProps">
            <div v-html="slotProps.option.icon"></div>
        </template>
      </SelectButton>
      <div class="hidden gap-3 md:!flex items-baseline">
        <!-- <a
          href="wishlist.html"
          class="flex cursor-pointer flex-col items-center justify-center"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="h-6 w-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"
            />
          </svg>

          <p class="text-xs">Wishlist</p>
        </a> -->

        <Link
          v-if="$page.props.auth.user"
          :href="route('profile.index')"
          class="relative flex cursor-pointer flex-col items-center justify-center"
        >
        
          <!-- <span class="absolute bottom-[33px] right-1 flex h-2 w-2">
            <span
              class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"
            ></span>
            <span
              class="relative inline-flex h-2 w-2 rounded-full bg-red-500"
            ></span>
          </span> -->

          <i class="pi pi-user" style="font-size: 1.5rem"></i>
          <!-- <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="h-6 w-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"
            />
          </svg> -->

          <!-- <p class="text-xs">Аккаунт</p> -->

        </Link>

        <Link :href="route('cart')">
          <OverlayBadge :value="String($page.props.cart.length)">
            <i class="pi pi-shopping-cart" style="font-size: 1.5rem" />
          </OverlayBadge>
        </Link>
        <!-- <Link :href="route('cart')">
          <Button type="button" icon="pi pi-shopping-cart" rounded text :badge="String($page.props.cart.length)" iconClass="custom-cart-size" badgeClass="custom-badge" badgeSeverity="info" variant="link" />
        </Link> -->
        <!-- <Button :href="route('cart')" variant="outlined" class="!border-2" badge="2">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="currentColor"
            class="h-6 w-6"
          >
            <path
              fill-rule="evenodd"
              d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 004.25 22.5h15.5a1.875 1.875 0 001.865-2.071l-1.263-12a1.875 1.875 0 00-1.865-1.679H16.5V6a4.5 4.5 0 10-9 0zM12 3a3 3 0 00-3 3v.75h6V6a3 3 0 00-3-3zm-3 8.25a3 3 0 106 0v-.75a.75.75 0 011.5 0v.75a4.5 4.5 0 11-9 0v-.75a.75.75 0 011.5 0v.75z"
              clip-rule="evenodd"
            />
          </svg>
          <p class="text-xs">Кошик</p>
      </Button> -->

        <!-- <Link
          :href="route('cart')"
          class="flex cursor-pointer flex-col items-center justify-center"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="currentColor"
            class="h-6 w-6"
          >
            <path
              fill-rule="evenodd"
              d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 004.25 22.5h15.5a1.875 1.875 0 001.865-2.071l-1.263-12a1.875 1.875 0 00-1.865-1.679H16.5V6a4.5 4.5 0 10-9 0zM12 3a3 3 0 00-3 3v.75h6V6a3 3 0 00-3-3zm-3 8.25a3 3 0 106 0v-.75a.75.75 0 011.5 0v.75a4.5 4.5 0 11-9 0v-.75a.75.75 0 011.5 0v.75z"
              clip-rule="evenodd"
            />
          </svg>

          <p class="text-xs">Кошик</p>
        </Link> -->
      </div>
    </header>
    <!-- /Header -->

    <!-- Burger menu  -->
    <section
      v-if="mobileMenuOpen"
      @click.outside="mobileMenuOpen = false"
      class="absolute left-0 right-0 z-50 h-screen w-full bg-white"
    >
      <div class="mx-auto">
        <div class="mx-auto flex w-full justify-center gap-3 py-4">
          <!-- <a
            href="wishlist.html"
            class="flex cursor-pointer flex-col items-center justify-center"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="h-6 w-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"
              />
            </svg>

            <p class="text-xs">Wishlist</p>
          </a> -->

          <Link
              v-if="$page.props.auth.user"
              :href="route('profile.index')"
              class="relative flex cursor-pointer flex-col items-center justify-center"
          >
            <span class="absolute bottom-[33px] right-1 flex h-2 w-2">
              <span
                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"
              ></span>
              <span
                class="relative inline-flex h-2 w-2 rounded-full bg-red-500"
              ></span>
            </span>

            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="h-6 w-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"
              />
            </svg>

            <p class="text-xs">Аккаунт</p>
          </Link>

          <a
            href="cart.html"
            class="flex cursor-pointer flex-col items-center justify-center"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="currentColor"
              class="h-6 w-6"
            >
              <path
                fill-rule="evenodd"
                d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 004.25 22.5h15.5a1.875 1.875 0 001.865-2.071l-1.263-12a1.875 1.875 0 00-1.865-1.679H16.5V6a4.5 4.5 0 10-9 0zM12 3a3 3 0 00-3 3v.75h6V6a3 3 0 00-3-3zm-3 8.25a3 3 0 106 0v-.75a.75.75 0 011.5 0v.75a4.5 4.5 0 11-9 0v-.75a.75.75 0 011.5 0v.75z"
                clip-rule="evenodd"
              />
            </svg>

            <p class="text-xs">Кошик</p>
          </a>
        </div>

        <form class="my-4 mx-5 flex h-9 items-center border">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="mx-3 h-4 w-4"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
            />
          </svg>

          <input
            class="hidden h-9 border-inherit w-11/12 outline-none md:block"
            type="search"
            placeholder="Search"
          />

          <button
            type="submit"
            class="ml-auto h-full bg-amber-400 px-4 hover:bg-yellow-300"
          >
            Пошук
          </button>
        </form>

        <ul class="text-center font-medium">
          <!-- page.route === route().current() -->
          <li class="py-2" v-for="page in navigation.pages">
            <Link
                :key="page.name"
                :href="route(page.route)"
                class="py-2"
            >
                {{ page.name }}
            </Link>
          </li>
        </ul>
      </div>
    </section>
    <!-- /Burger menu  -->

    <!-- Nav bar -->
    <!-- hidden on small devices -->

    <nav class="relative bg-emerald-500">
      <div
        class="mx-auto hidden h-12 w-full max-w-[1200px] items-center md:flex"
      >
        <button
          @click="desktopMenuOpen = !desktopMenuOpen"
          class="ml-5 flex h-full w-40 cursor-pointer items-center justify-center bg-amber-400"
        >
          <div class="flex justify-around" href="#">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="mx-1 h-6 w-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
              />
            </svg>

            Категорії
          </div>
        </button>

        <div class="mx-7 flex gap-8">
          <Link
              v-for="page in navigation.pages"
              :key="page.name"
              :href="route(page.route)"
              class="font-light text-white duration-100 hover:text-yellow-400 hover:underline"
          >
              {{ page.name }}
          </Link>
        </div>

        <div class="ml-auto flex gap-4 px-5">
          <Link
              v-if="$page.props.auth.user?.role === 'admin'"
              :href="route('dashboard')"
              class="font-light text-white duration-100 hover:text-yellow-400 hover:underline"
          >
              Кабінет
          </Link>
          <template v-if="!$page.props.auth.user">
              <Link
                  :href="route('login')"
                  class="font-light text-white duration-100 hover:text-yellow-400 hover:underline"
              >
                  Логін
              </Link>
              <span class="text-white">&#124;</span>
              <Link
                  v-if="$page.props.canRegister"
                  :href="route('register')"
                  class="font-light text-white duration-100 hover:text-yellow-400 hover:underline"
              >
                  Реєстрація
              </Link>
          </template>
        </div>
      </div>
    </nav>
    <!-- /Nav bar -->

    <!-- Menu  -->
    <section
      v-if="desktopMenuOpen"
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
</style>