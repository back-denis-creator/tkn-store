<template>
    <div>
    <!-- Desktop Footer  -->
    <footer
      class="mx-auto w-full max-w-[1200px] justify-between pb-10 flex flex-col lg:flex-row px-5"
    >
      <div class="max-w-sm">
        <div class="mt-10 mb-5">
          <span class="text-2xl font-bold text-gray-900 tracking-[0.2em] uppercase font-cinzel">casanel</span>
        </div>
        <p class="text-gray-500 text-sm leading-relaxed">
          {{ $t('FooterAbout') }}
        </p>
        <div class="mt-8 flex gap-6">
          <a href="https://t.me/YafLa1GM5sYxNmRi" target="_blank" rel="noopener noreferrer" class="hover:opacity-80 transition-all hover:scale-110">
            <img class="h-8 w-8" src="/images/telegram.svg" alt="telegram" />
          </a>
          <a href="https://www.instagram.com/casanel.ua?igsh=OHQybTZiYzBhNnVu&utm_source=qr" target="_blank" rel="noopener noreferrer" class="hover:opacity-80 transition-all hover:scale-110">
            <img class="h-8 w-8" src="/images/instagram.svg" alt="instagram" />
          </a>
          <a href="https://www.facebook.com/share/18Bk66SNbU/?mibextid=wwXIfr" target="_blank" rel="noopener noreferrer" class="hover:opacity-80 transition-all hover:scale-110">
            <img class="h-8 w-8" src="/images/facebook.svg" alt="facebook" />
          </a>
        </div>
      </div>

      <div class="flex flex-col md:flex-row gap-12 lg:gap-24 mt-10 lg:mt-0">
        <div class="flex flex-col">
          <p class="font-bold text-gray-800 uppercase tracking-wider mb-4">{{ $t('Contacts') }}</p>
          <ul class="text-sm space-y-3">
            <li class="flex flex-col">
              <span class="text-gray-400 text-xs">{{ $t('FooterAddressLabel') }}</span>
              <span class="text-gray-600">{{ $t('FooterAddress') }}</span>
            </li>
            <li class="flex flex-col mt-2">
              <span class="text-gray-400 text-xs">{{ $t('Email') }}</span>
              <a href="mailto:casanel.connect@gmail.com" class="text-gray-600 hover:text-amber-600 transition-colors">casanel.connect@gmail.com</a>
            </li>
            <li class="flex flex-col mt-2">
              <span class="text-gray-400 text-xs">Phone</span>
              <a :href="'tel:' + $t('FooterPhone')" class="text-gray-600 hover:text-amber-600 transition-colors font-medium">{{ $t('FooterPhone') }}</a>
            </li>
          </ul>
        </div>

        <!-- <div class="flex flex-col">
          <p class="font-bold text-gray-800 uppercase tracking-wider mb-4">{{ $t('Catalog') }}</p>
          <ul class="text-sm space-y-2">
            <li><a href="#" class="text-gray-600 hover:text-amber-600 transition-colors">{{ $t('Service1') }}</a></li>
            <li><a href="#" class="text-gray-600 hover:text-amber-600 transition-colors">{{ $t('Service2') }}</a></li>
            <li><a href="#" class="text-gray-600 hover:text-amber-600 transition-colors">{{ $t('Service3') }}</a></li>
          </ul>
        </div> -->
      </div>
    </footer>
    <!-- /Desktop Footer  -->

    <!-- Payment and copyright  -->

    <!-- <section class="h-11 bg-amber-400">
      <div
        class="mx-auto flex max-w-[1200px] items-center justify-between px-4 pt-2"
      >
        <p>&copy; Denys Basarab, 2024</p>
        <div class="flex items-center space-x-3">
          <img
            class="h-8"
            src="https://cdn-icons-png.flaticon.com/512/5968/5968299.png"
            alt="Visa icon"
          />
          <img
            class="h-8"
            src="https://cdn-icons-png.flaticon.com/512/349/349228.png"
            alt="AE icon"
          />
          <img
            class="h-8"
            src="https://cdn-icons-png.flaticon.com/512/5968/5968144.png"
            alt="Apple pay icon"
          />
        </div>
      </div>
    </section> -->
    <!-- /Payment and copyright  -->
    </div>
</template>