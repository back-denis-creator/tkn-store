<script setup>
import { ref, computed, onMounted } from 'vue'
import { getActiveLanguage, loadLanguageAsync } from 'laravel-vue-i18n';
import { Link, usePage, useForm } from '@inertiajs/vue3';
import CartPreview from '@/Components/CartPreview.vue';

const page = usePage()
const cartCount = computed(() => page.props.cartCount || 0)

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
        { name: 'Catalog', route: 'catalog', icon: 'pi-th-large' },
        { name: 'About Us', route: 'about', icon: 'pi-info-circle' },
        { name: 'HoReCa', route: 'horeca', icon: 'pi-briefcase' },
    ],
}

const authUser = computed(() => page.props.auth?.user)
const accountLink = computed(() => authUser.value
    ? { name: authUser.value.name, route: 'profile.index', icon: 'pi-user' }
    : { name: 'Увійти / Реєстрація', route: 'login', icon: 'pi-user' }
)
const userInitial = computed(() => authUser.value?.name?.trim()?.charAt(0)?.toUpperCase() || '?')
const mobileMenuOpen = ref(false)

// The cart/language/menu/mobile-drawer cluster below only renders after the
// client mounts. Something in that combination (never pinned down to one
// exact element) desyncs SSR hydration badly enough that Vue's patch-up can
// drop real DOM nodes on some pages (e.g. Profile's tab list). Rendering
// nothing here for both the SSR pass and the pre-mount client pass keeps
// the two identical, so there's nothing to reconcile.
const isMounted = ref(false)
onMounted(() => { isMounted.value = true })
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
      <div class="flex items-center gap-8">
          <Link href="/" class="flex items-center shrink-0">
            <span class="text-2xl font-bold text-gray-900 tracking-[0.2em] font-cinzel">Casanel</span>
          </Link>

          <!-- Primary navigation lives here now, next to the logo — not only
               inside the hamburger dropdown/mobile drawer — so the store's
               main sections are always visible, not one lone floating link. -->
          <nav class="hidden lg:flex items-center gap-6">
              <Link
                  v-for="page in navigation.pages"
                  :key="page.name"
                  :href="page.href ? page.href : route(page.route)"
                  class="text-sm font-medium text-gray-700 hover:text-amber-600 transition-colors"
              >
                  {{ $t(page.name) }}
              </Link>
          </nav>
      </div>

      <div class="flex items-center gap-4 z-40" v-if="isMounted">
          <!-- Cart -->
          <CartPreview v-if="cartCount > 0" :cart-count="cartCount" />

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

          <!-- Account -->
          <Link
              v-if="!authUser"
              :href="route(accountLink.route)"
              :title="accountLink.name"
              class="hidden lg:inline-flex items-center justify-center p-2 text-gray-700 hover:bg-gray-50 transition-all"
          >
              <i class="pi pi-user text-lg"></i>
          </Link>

          <div v-else class="hidden lg:block relative group">
              <button
                  type="button"
                  class="inline-flex items-center justify-center rounded-full p-0.5 bg-transparent hover:bg-gray-50 focus:outline-none transition-all"
              >
                  <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-400 text-xs font-bold text-black">
                      {{ userInitial }}
                  </span>
              </button>

              <!-- Account dropdown on hover -->
              <div
                  class="absolute right-0 mt-0 w-60 pt-2 invisible opacity-0 translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 ease-out z-[60]"
              >
                  <div class="bg-white rounded-md shadow-xl ring-1 ring-black ring-opacity-5 overflow-hidden border border-gray-100">
                      <div class="flex items-center gap-3 border-b border-gray-100 px-4 py-3">
                          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-400 text-sm font-bold text-black">
                              {{ userInitial }}
                          </span>
                          <div class="min-w-0">
                              <p class="text-xs text-gray-400">{{ $t('Hello') }}</p>
                              <p class="truncate text-sm font-bold text-gray-900">{{ authUser.name }}</p>
                          </div>
                      </div>
                      <div class="py-1">
                          <Link
                              :href="route('profile.index')"
                              class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-600 transition-colors border-b border-gray-50"
                          >
                              <i class="pi pi-user mr-3 text-amber-500 text-lg"></i>
                              {{ $t('Profile Information') }}
                          </Link>
                          <Link
                              :href="route('orders.mine')"
                              class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-600 transition-colors border-b border-gray-50"
                          >
                              <i class="pi pi-shopping-bag mr-3 text-amber-500 text-lg"></i>
                              {{ $t('Order') }}
                          </Link>
                          <Link
                              v-if="authUser.role === 'admin'"
                              :href="route('dashboard')"
                              class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-600 transition-colors border-b border-gray-50"
                          >
                              <i class="pi pi-cog mr-3 text-amber-500 text-lg"></i>
                              Панель адміністратора
                          </Link>
                          <Link
                              :href="route('logout')"
                              method="post"
                              as="button"
                              class="flex w-full items-center px-4 py-3 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-600 transition-colors"
                          >
                              <i class="pi pi-sign-out mr-3 text-amber-500 text-lg"></i>
                              {{ $t('Log Out') }}
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
      <Drawer v-if="isMounted" v-model:visible="mobileMenuOpen" position="right" class="!w-72">
          <template #header>
              <div class="flex items-center gap-2">
                <span class="text-xl font-bold tracking-widest font-cinzel">Сasanel</span>
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
                      <i :class="`pi ${page.icon || 'pi-briefcase'} text-amber-500 text-lg overflow-hidden`" ></i>
                      <span class="font-medium">{{ $t(page.name) }}</span>
                  </Link>
                  <Link
                      :href="route(accountLink.route)"
                      @click="mobileMenuOpen = false"
                      class="flex items-center gap-3 px-3 py-4 text-gray-700 hover:bg-amber-50 rounded-lg transition-colors border-b border-gray-50"
                  >
                      <i :class="`pi ${accountLink.icon} text-amber-500 text-lg overflow-hidden`" ></i>
                      <span class="font-medium">{{ accountLink.name }}</span>
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