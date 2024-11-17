<template>
    <GuestLayout>
        <Head :title="product.name" />

    <section
        class="container flex-grow mx-auto max-w-[1200px] border-b py-5 lg:grid lg:grid-cols-2 lg:py-10"
      >
        <!-- image gallery -->
         <div class="container mx-auto px-4">
            <Galleria :value="selectedSku?.media" :responsiveOptions="responsiveOptions" :numVisible="5" :circular="true" :showItemNavigators="true" :showItemNavigatorsOnHover="true" containerStyle="mx-auto px-4">
            <template #item="slotProps">
                <img :src="slotProps.item.original_url" :alt="slotProps.item.collection_name" style="width: 100%" />
            </template>
            <template #thumbnail="slotProps">
                <img :src="slotProps.item.original_url" :alt="slotProps.item.collection_name" />
            </template>
        </Galleria>
         </div>
        <!-- /image gallery  -->

        <!-- description  -->

        <div class="mx-auto px-5 lg:px-5">
          <h2 class="pt-3 text-2xl font-bold lg:pt-0 uppercase">{{ product.name }}</h2>
          <div class="mt-1">
            <div class="flex items-center">
              <Rating v-model="rating" readonly />
              <p class="ml-3 text-sm text-gray-400">(150 reviews)</p>
            </div>
          </div>

          <p class="mt-5 font-bold">
            Availability: <span class="text-green-600">In Stock</span>
          </p>
          <!-- <p class="font-bold">Brand: <span class="font-normal">Apex</span></p> -->
          <p class="font-bold">
            Категорії: <span class="font-normal">Sofa</span>
          </p>
          <p class="font-bold">
            {{ $t("Sku") }}: <span class="font-normal">{{ selectedSku?.code }}</span>
          </p>

          <p class="mt-4 text-4xl font-bold text-emerald-500">
            {{ selectedSku?.price }} грн.
            <!-- <span class="text-xs text-gray-400 line-through">$550</span> -->
          </p>

          <p class="pt-5 text-sm leading-5 text-gray-500">
            Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quidem
            exercitationem voluptate sint eius ea assumenda provident eos
            repellendus qui neque! Velit ratione illo maiores voluptates commodi
            eaque illum, laudantium non!
          </p>
    
            <!-- <span>{{ intersectionIds }}</span>
            <span>{{ attrModels }}</span>
            <span>{{ attributes }}</span> -->

          <div class="mt-6" v-for="attribute in attributes" :key="attribute.name">
            <p class="pb-2 text-xs text-gray-500">{{ attribute.name }}</p>

            <div class="flex gap-1" v-if="attribute.name === 'Колір'">
              <div
                v-for="(option, index) in attribute.options" :key="option.value"
                class="flex h-8 w-8 cursor-pointer items-center justify-center border duration-100 hover:bg-neutral-100 focus:ring-2 focus:ring-gray-500 active:ring-2 active:ring-gray-500"
              >
                <img
                    @click="attrModels[attribute.name] = option.value"
                    :src="option.image_url"
                    class="w-8"
                    :class="{'ring-2 ring-emerald-500': attrModels[attribute.name] === option.value}"
                />
              </div>
            </div>
            <div v-else>
                <div v-for="(option, index) in attribute.options" :key="option.value">
                    <label :for="`${index}_${attribute.name}`">
                        <RadioButton v-model="attrModels[attribute.name]" :inputId="`${index}_${attribute.name}`" :name="attribute.name" :value="option.value" />
                        {{ option.value }}
                    </label>
                </div>
            </div>
          </div>

          <div class="mt-6">
            <p class="pb-2 text-xs text-gray-500">{{ $t("Quantity") }}</p>

            <div class="flex">
              <button
                @click="updateQuantity('-')"
                class="flex h-8 w-8 cursor-pointer items-center justify-center border duration-100 hover:bg-neutral-100 focus:ring-2 focus:ring-gray-500 active:ring-2 active:ring-gray-500"
              >
                &minus;
              </button>
              <div
                class="flex h-8 w-8 cursor-text items-center justify-center border-t border-b active:ring-gray-500"
              >
                {{ quantity }}
              </div>
              <button
                @click="updateQuantity('+')"
                class="flex h-8 w-8 cursor-pointer items-center justify-center border duration-100 hover:bg-neutral-100 focus:ring-2 focus:ring-gray-500 active:ring-2 active:ring-gray-500"
              >
                &#43;
              </button>
            </div>
          </div>

          <div class="mt-7 flex flex-row items-center gap-6">
            <!-- class="flex h-12 w-1/3 items-center justify-center" -->
            <Button
                :label="$t('Add to cart')"
                icon="pi pi-shopping-bag"
                @click="addToCart()"
                class="w-1/3"
            />
              <!-- Додати у кошик
            </Button> -->
            <!-- <button
              class="flex h-12 w-1/3 items-center justify-center bg-amber-400 duration-100 hover:bg-yellow-300"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="mr-3 h-4 w-4"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"
                />
              </svg>

              Wishlist
            </button> -->
          </div>
        </div>
      </section>

      <!-- product details  -->

      <section class="container mx-auto max-w-[1200px] px-5 py-5 lg:py-10">
        <h2 class="text-xl">Опис товара</h2>
        <div class="mt-4 lg:w-3/4" v-html="product.description"></div>

        <table class="mt-7 w-full table-auto divide-x divide-y lg:w-1/2">
          <tbody class="divide-x border">
            <tr>
              <td class="border pl-4 font-bold">Color</td>
              <td class="border pl-4">Black, Brown, Red</td>
            </tr>

            <tr>
              <td class="border pl-4 font-bold">Material</td>
              <td class="border pl-4">Latex</td>
            </tr>

            <tr>
              <td class="border pl-4 font-bold">Weight</td>
              <td class="border pl-4">55 Kg</td>
            </tr>
          </tbody>
        </table>
      </section>
      <!-- /product details  -->

      <!-- /description  -->

      <p class="mx-auto mt-10 mb-5 max-w-[1200px] px-5">RELATED PRODUCTS</p>

      <!-- Recommendations -->
      <section
        class="container mx-auto grid max-w-[1200px] grid-cols-2 gap-3 px-5 pb-10 lg:grid-cols-4"
      >
        <!-- 1 -->

        <div class="relative flex flex-col">
          <div
            class="absolute flex h-1/2 w-full justify-center gap-3 pt-16 opacity-0 duration-150 hover:opacity-100"
          >
            <span
              class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-amber-400"
            >
              <svg
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
                  d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                />
              </svg>
            </span>
            <span
              class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-amber-400"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
                class="h-4 w-4"
              >
                <path
                  d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"
                />
              </svg>
            </span>
          </div>
          <img
            class=""
            src="/images/product-chair.png"
            alt="sofa image"
          />

          <div>
            <p class="mt-2">CHAIR</p>
            <p class="font-medium text-emerald-500">
              $45.00
              <span class="text-sm text-gray-500 line-through">$500.00</span>
            </p>

            <div class="flex items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-gray-200"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>
              <p class="text-sm text-gray-400">(38)</p>
            </div>

            <div>
              <button class="my-5 h-10 w-full bg-emerald-500 text-white">
                Add to cart
              </button>
            </div>
          </div>
        </div>

        <!-- 2 -->

        <div class="flex flex-col">
          <img
            class=""
            src="/images/product-sofa.png"
            alt="sofa image"
          />

          <div>
            <p class="mt-2">SOFA</p>
            <p class="font-medium text-emerald-500">
              $45.00
              <span class="text-sm text-gray-500 line-through">$500.00</span>
            </p>

            <div class="flex items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-gray-200"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>
              <p class="text-sm text-gray-400">(38)</p>
            </div>

            <div>
              <button class="my-5 h-10 w-full bg-emerald-500 text-white">
                Add to cart
              </button>
            </div>
          </div>
        </div>

        <!-- 3 -->

        <div class="flex flex-col">
          <img class="" src="/images/kitchen.png" alt="sofa image" />

          <div>
            <p class="mt-2">GUYER KITCHEN</p>
            <p class="font-medium text-emerald-500">
              $45.00
              <span class="text-sm text-gray-500 line-through">$500.00</span>
            </p>

            <div class="flex items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-gray-200"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>
              <p class="text-sm text-gray-400">(38)</p>
            </div>

            <div>
              <button class="my-5 h-10 w-full bg-emerald-500 text-white">
                Add to cart
              </button>
            </div>
          </div>
        </div>

        <!-- 4 -->

        <div class="flex flex-col">
          <img
            class=""
            src="/images/living-room.png"
            alt="sofa image"
          />

          <div>
            <p class="mt-2">GUYER ROOM</p>
            <p class="font-medium text-emerald-500">
              $45.00
              <span class="text-sm text-gray-500 line-through">$500.00</span>
            </p>

            <div class="flex items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-yellow-400"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="h-4 w-4 text-gray-200"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                  clip-rule="evenodd"
                />
              </svg>
              <p class="text-sm text-gray-400">(38)</p>
            </div>

            <div>
              <button class="my-5 h-10 w-full bg-emerald-500 text-white">
                Add to cart
              </button>
            </div>
          </div>
        </div>
      </section>
      <!-- /Recommendations -->

    </GuestLayout>
</template>
<script setup>
import Galleria from 'primevue/galleria'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, onMounted, computed } from "vue"
import { useToast } from "primevue/usetoast"
import Rating from 'primevue/rating'

const toast = useToast()
const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    product: {
        type: Object,
        required: true
    },
    status: {
        required: true,
    }
})
const rating = ref(3)
const responsiveOptions = ref([
    { breakpoint: '1300px', numVisible: 4 },
    { breakpoint: '575px', numVisible: 1 }
])
const quantity = ref(1)
const attrModels = ref({})
const intersectionIds = computed(() => {
    // Получаем массив id для выбранных атрибутов
    const ids = Object.entries(attrModels.value).map(([key, value]) => {
        const attribute = attributes.value.find(attr => attr.name === key)
        if (attribute) {
            const option = attribute.options.find(opt => opt.value === value)
            return option ? option.id : []
        }
        return [];
    }).filter(arr => arr.length > 0)
    // Проверяем, есть ли id для пересечения
    if (ids.length === 0) return [] // Если нет id, возвращаем пустой массив
    // Находим пересечение id
    return ids.reduce((acc, curr) => acc.filter(id => curr.includes(id)))
})
const selectedSku = computed(() => props.product.skus.find(({id}) => id === intersectionIds.value[0]))
// Generate the attributes structure for rendering
const attributes = computed(() => {
    const uniqueAttributes = {}
    props.product.skus.forEach((sku) => {
        sku.attribute_options.forEach((option) => {
            const attrName = option.attribute.name
            if (!attrName) return
            if (!uniqueAttributes[attrName]) uniqueAttributes[attrName] = { name: attrName, options: [] }
            if (!uniqueAttributes[attrName].options.some((opt) => opt.value === option.value)) {
                uniqueAttributes[attrName].options.push({
                    value: option.value,
                    image_url: option.media[0]?.original_url,
                    id: [sku.id]
                })
            } else {
                uniqueAttributes[attrName].options.find(({value}) => value === option.value)?.id?.push(sku.id)
            }
        })
    })
    return Object.values(uniqueAttributes)
})
const updateQuantity = (action) => {
    if (action === '+') {
        quantity.value ++
    } else if (quantity.value > 1) {
        quantity.value --
    }
}
const form = useForm({
    product_id: props.product.id,
    sku_id: null,
    quantity: quantity.value,
    price: null,
})
const addToCart = () => {
    Object.assign(form, {
        sku_id: selectedSku.value?.id,
        price: selectedSku.value?.price
    })
    form.post(route('cart.add'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Додано', life: 3000 })
        },
        onError: () => {

        },
    })
}
onMounted(() => {
    props.product.skus.forEach((sku) => {
        sku.attribute_options.forEach((option) => {
            if(!attrModels.value.hasOwnProperty(option.attribute.name)) {
                attrModels.value = Object.assign(attrModels.value, {[option.attribute.name]: option.value})
            }
        })
    })
})
</script>