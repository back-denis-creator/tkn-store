<template>
    <GuestLayout>
        <Head title="Catalog" />

        <section
            class="container mx-auto flex-grow max-w-[1200px] border-b py-5 lg:flex lg:flex-row lg:py-10"
        >
            <!-- sidebar  -->
            <section class="hidden w-[300px] flex-shrink-0 px-4 lg:block">
                <div class="flex border-b pb-5">
                    <div class="w-full">
                        <p class="mb-3 font-medium">КАТЕГОРІЇ</p>
                        <Tree v-model:selectionKeys="selectedKey" v-model:expandedKeys="expandedKeys" :value="categories" @update:selectionKeys="handleUpdateCategory" selectionMode="checkbox" class="flex w-full" :pt="{root: 'my-root'}"></Tree>
                    </div>
                </div>

                <div class="flex border-b py-5">
                    <div class="w-full">
                    <p class="mb-6 font-medium">ЦІНА</p>
                    <div class="w-full">
                        <Slider v-model="priceRange" @update:modelValue="handleUpdatePrice" range :min="filters.prices.min" :max="filters.prices.max" class="w-auto mx-2" />
                        <div class="flex justify-between w-auto ml-1 mt-2">
                            <span>{{ filters.prices.min }}</span>
                            <span>{{ filters.prices.max }}</span>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="flex py-5" v-for="attribute in filters.attributes">
                    <div class="w-full">
                        <p class="mb-3 font-medium uppercase">{{ attribute.name }}</p>
                        <div class="flex gap-2" v-if="attribute.name === 'Колір'">
                            <Accordion :value="colorGroupsOpened" multiple class="w-full" lazy>
                                <AccordionPanel v-for="group in attribute.color_groups" :value="group.id" class="px-0" v-show="groupHasColors(group.id)">
                                    <AccordionHeader class="px-0">{{ group.name }}</AccordionHeader>
                                    <AccordionContent>
                                        <div class="flex gap-2">
                                            <img
                                                v-for="option in getGroupColors(group.id)"
                                                @click="handleUpdateColor(option.value)"
                                                :src="option.image_url"
                                                class="w-8"
                                                :class="{'ring-2 ring-emerald-500': selectedColors.includes(option.value)}" 
                                            />
                                        </div>
                                    </AccordionContent>
                                </AccordionPanel>
                            </Accordion>                            
                        </div>
                        <div class="flex flex-col gap-2" v-else>
                            <div v-for="option in attribute.attribute_options" :key="option.id" class="flex items-center gap-2">
                                <Checkbox v-model="attribute.checked" @update:modelValue="handleUpdateOption(attribute.slug, $event)" :inputId="String(option.id)" name="category" :value="option.value" />
                                <label :for="option.id">{{ option.value }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /sidebar  -->

            <div class="w-full">
                <div class="mb-5 flex items-center justify-between px-5">
                    <div class="flex gap-3">
                        <Select
                            v-model="selectedSort"
                            @update:modelValue="handleUpdateSort"
                            :options="sorts"
                            optionLabel="name"
                            class=""
                        />

                        <button
                            class="flex items-center justify-center border px-6 py-2 md:hidden"
                        >
                            Filters
                            <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="mx-2 h-4 w-4"
                            >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                            </svg>
                        </button>
                    </div>

                    <div class="hidden gap-3 lg:flex">
                        <!-- <button class="border bg-amber-400 py-2 px-2">
                            <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-5 w-5"
                            >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"
                            />
                            </svg>
                        </button>

                        <button class="border py-2 px-2">
                            <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-5 w-5"
                            >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"
                            />
                            </svg>
                        </button> -->
                    </div>
                </div>

                <section
                    class="mx-auto grid max-w-[1200px] grid-cols-2 gap-3 px-5 pb-10 lg:grid-cols-3"
                >
                    <div class="flex flex-col" v-for="product in products.data">
                        <Link
                            class="cursor-pointer"
                            :href="route('product', product.slug)"
                        >
                            <div class="relative flex">
                                <img
                                    class=""
                                    src="/images/product-chair.png"
                                    alt="sofa image"
                                />
                                <div
                                    class="absolute flex h-full w-full items-center justify-center gap-3 opacity-0 duration-150 hover:opacity-100"
                                >
                                    <!-- <Link
                                        :href="route('product', product.slug)"
                                        class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-amber-400"
                                    > -->
                                    <span class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-amber-400">
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
                                </div>
                                <div class="absolute right-1 mt-3 flex items-center justify-center bg-amber-400">
                                    <p class="px-2 py-2 text-sm">&minus; 25&percnt; OFF</p>
                                </div>
                            </div>
                        </Link>

                        <div>
                            <p class="mt-2">{{ product.name }}</p>
                            <p class="font-medium text-emerald-500">
                                {{ product.default_price }} грн.
                                <!-- <span class="text-sm text-gray-500 line-through"
                                    >$500.00</span
                                > -->
                            </p>

                            <div class="flex items-center">
                                <Rating :defaultValue="product?.rating" readonly />
                                <p class="ml-3 text-sm text-gray-400">(38)</p>
                            </div>

                            <div>
                                <Button class="my-5 h-10 w-full" @click="addToCart(product.id)">
                                    Додати у кошик
                                </Button>
                            </div>
                        </div>
                    </div>
                </section>
                <Paginator @page="handleUpdatePage($event)" :first="offset" :rows="products.per_page" :totalRecords="products.total" :rowsPerPageOptions="[12, 24, 48]"></Paginator>
            </div>
        </section>
    </GuestLayout>
</template>
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
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
    filters: {
        type: Object,
        required: true
    },
    products: {
        type: Object,
        required: true
    }
})

const selectedSort = ref({ name: 'Назва (Я-А)', key: 'desc' })
const sorts = ref([
    { name: 'Назва (А-Я)', key: 'asc' },
    { name: 'Назва (Я-А)', key: 'desc' },
    { name: 'Ціна спадання', key: 'price-max-min' },
    { name: 'Ціна зростання', key: 'price-min-max' },
])
const offset = computed(() => (props.products.per_page * (props.products.current_page - 1)) > props.products.total ? props.products.total : (props.products.per_page * (props.products.current_page - 1)))
const form = useForm({
    product_id: null,
    sku_id: null,
    quantity: 1,
    price: null,
})
const addToCart = (productId) => {
    let skus = props.products.data.find(({id}) => id === productId).skus
    console.log('skus: ', skus)
    Object.assign(form, {
        product_id: productId,
        sku_id: skus[0]?.id,
        price: skus[0]?.price
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

function debounce(func, delay) {
  let timeoutId
  return function (...args) {
    clearTimeout(timeoutId)
    timeoutId = setTimeout(() => {
      func.apply(this, args)
    }, delay)
  }
}

function transformCategories(arr) {
    return arr.map(item => {
        const transformedItem = {
            key: item.id,
            label: item.name,
         // data: 'Documents Folder',
         // icon: 'pi pi-fw pi-inbox',
        }
        if (item.children && item.children.length > 0) {
            transformedItem.children = transformCategories(item.children)
        }
        return transformedItem
    })
}

const categories = ref(transformCategories(props.filters.categories))
const filter = ref({})
const selectedKey = ref({})
const expandedKeys = ref({})
const priceRange = ref([props.filters.prices.min, props.filters.prices.max])
const selectedColors = ref([])
const colorGroupsOpened = ref([])

const expandParentNodes = (node, parentKeys = []) => {
    //MB TODO
    if (node.children && node.children.length) {
        expandedKeys.value = Object.assign({}, expandedKeys.value, {[Number(node.key)]: true})
        node.children.forEach(child => {
            expandParentNodes(child, [...parentKeys, node.key]);
        })
    } else {
        parentKeys.forEach(key => {
            expandedKeys.value = Object.assign({}, expandedKeys.value, {[Number(key)]: true})
        })
    }
}

onMounted(() => {
    //Set url filter
    const urlParams = new URLSearchParams(window.location.search)
    urlParams.forEach((value, key) => {
        console.log('value: ', value)
        console.log('key: ', key)
        if (key.startsWith('category')) {
            selectedKey.value = Object.assign({}, selectedKey.value, {[Number(value)]: {checked: true}})
            Object.assign(filter.value, {category: [Number(value)]})
        }
        if (key === 'max_price') {
            Object.assign(filter.value, {max_price: Number(value)})
            priceRange.value[1] = props.filters.prices.max >= Number(value) && props.filters.prices.min <= Number(value) ? Number(value) : props.filters.prices.max
        }
        if (key === 'min_price') {
            Object.assign(filter.value, {min_price: Number(value)})
            priceRange.value[0] = props.filters.prices.min <= Number(value) && Number(value) <= props.filters.prices.max ? Number(value) : props.filters.prices.min
        }
        if (key.startsWith('colors')) {
            selectedColors.value.push(value)
            colorGroupsOpened.value.push(getGroupColorIdByValue(value))
            Object.assign(filter.value, {colors: selectedColors.value})
        }
        if (key === 'page') {
            Object.assign(filter.value, { page: value })
        }
        if (key === 'rows') {
            Object.assign(filter.value, { rows: value })
        }
        if (key === 'sort') {
            Object.assign(filter.value, { sort: value })
            selectedSort.value = sorts.value.find(({key}) => key === value)
        }
        props.filters.attributes.forEach((attribute) => {
            if(key.includes(attribute.slug)) {
                if(key.includes('[')) {
                    key = key.replace(/\[.*$/, '')
                    if(filter.value[key]) {
                        filter.value[key].push(value)
                    } else {
                        Object.assign(filter.value, {[key]: [value]})
                    }
                } else {
                    Object.assign(filter.value, {[key]: value})
                }
            }
        })
    })
    Object.keys(selectedKey.value).forEach((id) => {
        if(!filter.value.category.includes(Number(id))) {
            filter.value.category.push(Number(id))
        }
    })
    categories.value.forEach(node => expandParentNodes(node))
})
const handleUpdateSort = (sort) => {
    Object.assign(filter.value, { sort: sort.key })
    router.visit(route('catalog', filter.value), { preserveScroll: true })
}
const handleUpdateCategory = (e) => {
    Object.assign(filter.value, { category: Object.keys(selectedKey.value).filter((id) => selectedKey.value[id].checked) })
    delete filter.value.min_price
    delete filter.value.max_price
    delete filter.value.page
    router.visit(route('catalog', filter.value), { preserveScroll: true })
}
const handleUpdatePrice = debounce((e) => {
    Object.assign(filter.value, { min_price: e[0], max_price: e[1] })
    delete filter.value.page
    router.visit(route('catalog', filter.value), { preserveScroll: true })
}, 200)
const handleUpdateOption = (slug, checkedOptions) => {
    Object.assign(filter.value, { [slug]: checkedOptions })
    delete filter.value.page
    router.visit(route('catalog', filter.value), { preserveScroll: true })
}
const handleUpdateColor = (color) => {
    if(!selectedColors.value.includes(color)) {
        selectedColors.value.push(color)
    } else {
        let findedIndex = selectedColors.value.findIndex((element) => element === color)
        if(findedIndex >= 0) {
            delete selectedColors.value[findedIndex]
        }
    }
    Object.assign(filter.value, { colors: selectedColors.value })
    delete filter.value.page
    router.visit(route('catalog', filter.value), { preserveScroll: true })
}
const getGroupColors = (groupId) => {
    let color = props.filters.attributes.find(({name}) => name === 'Колір')
    return color?.attribute_options.filter(({meta}) => meta == groupId)
}
const getGroupColorIdByValue = (colorValue) => {
    let color = props.filters.attributes.find(({name}) => name === 'Колір')
    let meta = color?.attribute_options.find(({value}) => value == colorValue)?.meta
    return color?.color_groups.find(({id}) => id == meta).id
}
const groupHasColors = (groupId) => {
    let color = props.filters.attributes.find(({name}) => name === 'Колір')
    return !!color?.attribute_options.find(({meta}) => meta == groupId)
}
const handleUpdatePage = (paginator) => {
    Object.assign(filter.value, { page: paginator.page + 1, rows: paginator.rows })
    router.visit(route('catalog', filter.value), { preserveScroll: true })
}
</script>
<style scoped>
    .my-root {
        padding: 0 !important;
    }
    .p-accordionheader {
        padding-inline: 0 !important;
        transition: none !important;
    }
    .p-accordioncontent {
        transition: none !important;
    }
    :deep(.p-accordioncontent-content) {
        padding-top: 0.125rem !important;
        padding-left: 0.125rem !important;
        padding-right: 0.125rem !important;
    }
</style>