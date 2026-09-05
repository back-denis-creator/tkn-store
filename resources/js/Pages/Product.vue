<template>
    <GuestLayout>
        <Head>
            <title>{{ product.name }}</title>
            <meta name="description" :content="metaDescription" />
            <link rel="canonical" :href="route('product', product.slug)" />
            <!-- TODO: remove once the catalog is fully populated and linked from the UI. -->
            <meta name="robots" content="noindex, nofollow" />

            <meta property="og:type" content="product" />
            <meta property="og:title" :content="product.name" />
            <meta property="og:description" :content="metaDescription" />
            <meta v-if="product.default_image" property="og:image" :content="product.default_image" />
            <meta v-if="product.default_price" property="product:price:amount" :content="String(product.default_price)" />
            <meta property="product:price:currency" content="UAH" />

            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="product.name" />
            <meta name="twitter:description" :content="metaDescription" />
            <meta v-if="product.default_image" name="twitter:image" :content="product.default_image" />

            <component :is="'script'" type="application/ld+json" v-html="productJsonLd" />
        </Head>

    <section
        class="container flex-grow mx-auto max-w-[1200px] border-b py-5 lg:grid lg:grid-cols-2 lg:py-10"
      >
        <!-- image gallery -->
         <div class="mx-auto w-full max-w-[500px] px-4">
            <div
                ref="imageContainer"
                class="relative aspect-square w-full overflow-hidden rounded-sm bg-gray-50 lg:cursor-zoom-in"
                @mousemove="onImageMouseMove"
                @mouseenter="zoomActive = true"
                @mouseleave="zoomActive = false"
            >
                <img :src="activeImage?.original_url" :alt="product.name" class="h-full w-full object-contain" />
                <div
                    v-if="zoomActive && activeImage"
                    class="pointer-events-none absolute hidden rounded-sm border-2 border-white shadow-lg lg:block"
                    :style="lensStyle"
                ></div>
            </div>

            <div v-if="media.length > 1" class="mt-3 flex gap-2 overflow-x-auto">
                <button
                    v-for="(item, index) in media"
                    :key="item.id"
                    type="button"
                    @click="activeIndex = index"
                    class="h-16 w-16 shrink-0 overflow-hidden rounded-sm border-2 transition-colors"
                    :class="activeIndex === index ? 'border-amber-400' : 'border-transparent hover:border-gray-200'"
                >
                    <img :src="item.original_url" :alt="product.name" class="h-full w-full object-cover" />
                </button>
            </div>
         </div>
        <!-- /image gallery  -->

        <!-- description  -->

        <div class="mx-auto px-5 lg:px-5">
          <h2 class="pt-3 text-2xl font-bold lg:pt-0 uppercase">{{ product.name }}</h2>

          <p v-if="product.categories?.length" class="mt-5 font-bold">
            Категорії: <span class="font-normal">{{ product.categories.map((c) => c.full_path || c.name).join(', ') }}</span>
          </p>
          <p class="font-bold">
            {{ $t("Sku") }}: <span class="font-normal">{{ selectedSku?.code }}</span>
          </p>

          <p class="mt-4 text-4xl font-bold text-amber-600">
            {{ totalPrice }} грн.
          </p>

          <div class="mt-6" v-for="attribute in attributes" :key="attribute.name">
            <p class="pb-2 text-xs text-gray-500">{{ attribute.name }}</p>

            <div class="flex flex-wrap gap-2" v-if="attribute.name === 'Колір'">
              <button
                v-for="option in attribute.options" :key="option.value"
                type="button"
                :title="option.value"
                @click="attrModels[attribute.name] = option.value"
                class="h-8 w-8 shrink-0 overflow-hidden rounded-full border border-gray-200 bg-gray-100 transition-shadow"
                :class="{'ring-2 ring-amber-400 ring-offset-1': attrModels[attribute.name] === option.value}"
              >
                <img
                    v-if="option.image_url"
                    :src="option.image_url"
                    :alt="option.value"
                    class="h-full w-full object-cover"
                />
              </button>
            </div>
            <div v-else class="flex flex-col gap-2">
                <label v-for="(option, index) in attribute.options" :key="option.value" :for="`${index}_${attribute.name}`" class="flex items-center gap-2 cursor-pointer">
                    <RadioButton v-model="attrModels[attribute.name]" :inputId="`${index}_${attribute.name}`" :name="attribute.name" :value="option.value" />
                    <span>{{ option.value }}</span>
                </label>
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
            <Button
                :label="$t('Add to cart')"
                icon="pi pi-shopping-bag"
                @click="addToCart()"
                class="!px-8 !py-3 whitespace-nowrap"
            />
          </div>
        </div>
      </section>

      <!-- product details  -->

      <section class="container mx-auto max-w-[1200px] px-5 py-5 lg:py-10">
        <h2 class="text-xl">Опис товара</h2>
        <div class="mt-4 lg:w-3/4" v-html="product.description"></div>
      </section>
      <!-- /product details  -->

      <!-- /description  -->


      <template v-if="relatedProducts.length">
        <p class="mx-auto mt-10 mb-5 max-w-[1200px] px-5">Схожі товари</p>

        <!-- Recommendations -->
        <section
          class="container mx-auto grid max-w-[1200px] grid-cols-2 gap-3 px-5 pb-10 lg:grid-cols-4"
        >
          <div class="flex flex-col" v-for="relatedProduct in relatedProducts" :key="relatedProduct.id">
            <Link :href="route('product', relatedProduct.slug)">
              <img
                v-if="relatedProduct.default_image"
                :src="relatedProduct.default_image"
                class="aspect-square w-full object-cover"
                :alt="relatedProduct.name"
              />
              <div v-else class="aspect-square w-full bg-gray-100"></div>
            </Link>

            <div>
              <Link :href="route('product', relatedProduct.slug)">
                <p class="mt-2">{{ relatedProduct.name }}</p>
              </Link>
              <p class="font-medium text-amber-600">
                {{ relatedProduct.default_price }} грн.
              </p>

              <div>
                <button
                  class="my-5 h-10 w-full bg-amber-400 text-black hover:bg-yellow-300"
                  @click="addRelatedToCart(relatedProduct)"
                >
                  {{ $t("Add to cart") }}
                </button>
              </div>
            </div>
          </div>
        </section>
        <!-- /Recommendations -->
      </template>

    </GuestLayout>
</template>
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, onMounted, computed, watch, inject } from "vue"
import { useToast } from "primevue/usetoast"

const toast = useToast()
// The template's bare `route(...)` calls (e.g. the canonical <link>) resolve
// fine via Vue's globalProperties in both client and SSR renders. But this
// file also builds JSON-LD in a plain computed() below — that's regular JS,
// not template-compiled, so it can't see globalProperties and needs the
// route helper injected explicitly (ZiggyVue provides it under 'route').
// Without this, SSR throws "ReferenceError: route is not defined" since,
// unlike the browser, there's no global `route()` script running server-side.
const route = inject('route')
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
    relatedProducts: {
        type: Array,
        default: () => []
    },
    status: {
        required: true,
    }
})
const metaDescription = computed(() => {
    const text = (props.product.description || '').replace(/\s+/g, ' ').trim()
    if (text) {
        return text.length > 160 ? text.slice(0, 157) + '…' : text
    }
    return `${props.product.name} — індивідуальне пошиття від Casanel. Замовляйте текстиль ручної роботи за вашими розмірами.`
})

const productJsonLd = computed(() => JSON.stringify({
    '@context': 'https://schema.org',
    '@type': 'Product',
    name: props.product.name,
    description: metaDescription.value,
    image: props.product.default_image || undefined,
    offers: props.product.default_price ? {
        '@type': 'Offer',
        priceCurrency: 'UAH',
        price: props.product.default_price,
        availability: 'https://schema.org/InStock',
        url: route('product', props.product.slug),
    } : undefined,
}))

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
// Falls back to the first SKU for products with no attribute options at all
// (e.g. a simple, single-variant product) — otherwise intersectionIds is
// empty and the page would show no image/price/code at all.
const selectedSku = computed(() => props.product.skus.find(({id}) => id === intersectionIds.value[0]) || props.product.skus[0])
const totalPrice = computed(() => (selectedSku.value?.price || 0) * quantity.value)

const media = computed(() => selectedSku.value?.media || [])
const activeIndex = ref(0)
const activeImage = computed(() => media.value[activeIndex.value] || media.value[0])
watch(selectedSku, () => { activeIndex.value = 0 })

const imageContainer = ref(null)
const zoomActive = ref(false)
const mousePos = ref({ x: 0, y: 0 })
const containerSize = ref({ width: 0, height: 0 })
const ZOOM_FACTOR = 2.5
const LENS_SIZE = 160

const onImageMouseMove = (e) => {
    const rect = imageContainer.value.getBoundingClientRect()
    containerSize.value = { width: rect.width, height: rect.height }
    mousePos.value = {
        x: Math.min(Math.max(e.clientX - rect.left, 0), rect.width),
        y: Math.min(Math.max(e.clientY - rect.top, 0), rect.height),
    }
}

const lensStyle = computed(() => {
    const { width, height } = containerSize.value
    if (!width || !height) return {}

    const half = LENS_SIZE / 2
    const left = Math.min(Math.max(mousePos.value.x - half, 0), Math.max(width - LENS_SIZE, 0))
    const top = Math.min(Math.max(mousePos.value.y - half, 0), Math.max(height - LENS_SIZE, 0))
    const bgWidth = width * ZOOM_FACTOR
    const bgHeight = height * ZOOM_FACTOR
    const bgX = Math.min(0, Math.max(-(bgWidth - LENS_SIZE), -(mousePos.value.x * ZOOM_FACTOR - half)))
    const bgY = Math.min(0, Math.max(-(bgHeight - LENS_SIZE), -(mousePos.value.y * ZOOM_FACTOR - half)))

    return {
        width: `${LENS_SIZE}px`,
        height: `${LENS_SIZE}px`,
        left: `${left}px`,
        top: `${top}px`,
        backgroundImage: `url(${activeImage.value?.original_url})`,
        backgroundSize: `${bgWidth}px ${bgHeight}px`,
        backgroundPosition: `${bgX}px ${bgY}px`,
        backgroundRepeat: 'no-repeat',
    }
})
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
const addRelatedToCart = (relatedProduct) => {
    const sku = relatedProduct.skus?.[0]
    if (!sku) return
    useForm({
        product_id: relatedProduct.id,
        sku_id: sku.id,
        quantity: 1,
        price: sku.price,
    }).post(route('cart.add'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Додано', life: 3000 })
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