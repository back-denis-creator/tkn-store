<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { Head, useForm } from "@inertiajs/vue3";
import CheckboxArray from '@/Components/CheckboxArray.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    products: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Object,
        default: () => ({}),
    },
    attributes: {
        type: Object,
        default: () => ({}),
    }
});

const form = useForm({
    name: "",
    slug: "",
    description: "",
    category_ids: [],
    variations: []
});

const submit = () => {
    form.variations = variations.value
    form.post(route("products.store"))
}

const handleUpdateCategories = (categories) => {
    form.category_ids = categories.reduce((filtered, category) => {
        if (category.value) filtered.push(category.id)
        return filtered
    }, [])
}

const categoryItems = ref([...props.categories.map((category) => {
    return {
        id: category.id,
        name: category.name,
        value: false
    }
})])

const deleteVariation = (index) => {
    if(index > 0) {
        if(variations.value[index].show) variations.value[0].show = true
        variations.value.splice(index, 1)
    }
}

const addVariation = (id) => {
    variations.value.push({
        id,
        code: "",
        price: "",
        show: false,
        images: [],
        attributes: props.attributes.map((attribute, index) => {
            return {
                name: attribute.name,
                value: "",
                id: attribute.id,
                attribute_options: attribute.attribute_options.map((option) => {
                    return {
                        id: option.id,
                        value: option.value
                    }
                }),
                search: (e) => { 
                    searchOptionVariations(index)
                }
            }
        })
    })
}

const selectVariation = (index) => {
    variations.value = variations.value.map((variation, i) => {
        variation.show = i === index
        return variation
    })
}

const selectedVariation = computed(() => variations.value.findIndex((({show}) => show)))

const searchOptionVariations = (index) => {
    variations.value[selectedVariation.value].attributes[index].attribute_options = variations.value[selectedVariation.value].attributes[index].attribute_options.map((option, index) => {
        return {
            id: option.id,
            value: option.value,
        }
    })
}

const variations = ref([{
    id: 1,
    code: "",
    price: "",
    show: true,
    images: [],
    attributes: props.attributes.map((attribute, index) => {
        return {
            name: attribute.name,
            value: "",
            id: attribute.id,
            attribute_options: attribute.attribute_options.map((option, index) => {
                return {
                    id: option.id,
                    value: option.value,
                }
            }),
            search: (e) => { 
                searchOptionVariations(index)
            }
        }
    })
}])

const onFilesVariation = (e) => {
    variations.value[selectedVariation.value].images = e.files
}
</script>

<template>
    <Head title="Створення товару" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Створення товару
            </h2>
        </template>
        <div class="py-12">
            <Card class="max-w-7xl mx-auto">
                <template #content>
                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel for="name" value="Імʼя" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.name"
                            />
                        </div>
                        <div class="my-6">
                            <InputLabel for="slug" value="URL Імʼя" />
                            <TextInput
                                id="slug"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.slug"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.slug"
                            />
                        </div>
                        <div class="my-6">
                            <label
                                for="description"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Опис</label
                            >
                            <Editor id="description" v-model="form.description" editorStyle="height: 320px" />
                            <div
                                v-if="form.errors.description"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </div>
                        </div>
                        <div class="my-6">
                            <span class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Категорії</span>
                            <CheckboxArray :items="categoryItems" @update:checked="handleUpdateCategories" />
                        </div>
                        <div class="my-6">
                            <span class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Варіації</span>
                            <div class="flex overflow-x-auto whitespace-nowrap">
                                <div v-for="(variation, index) in variations" :key="index" @click.prevent="selectVariation(index)" :class="variation.show ? 'border border-b-0 border-gray-300 sm:text-base dark:border-gray-500 rounded-t-md dark:text-white whitespace-nowrap focus:outline-none' : 'bg-transparent border-b border-gray-300 sm:text-base dark:border-gray-500 dark:text-white whitespace-nowrap cursor-base focus:outline-none hover:border-gray-400 dark:hover:border-gray-300'" class="inline-flex items-center h-12 px-4 py-2 text-sm text-center text-gray-700">
                                    <span>{{ `№${index}` }}</span>
                                    <Button :disabled="index === 0" @click.stop="deleteVariation(index)" icon="pi pi-times" class="ml-2" severity="danger" text size="small" rounded aria-label="Cancel" />
                                </div>
                                <div @click.prevent="addVariation(variations.length + 1)" class="inline-flex items-center h-12 px-4 py-2 text-sm text-center text-gray-700 bg-transparent border-b border-gray-300 sm:text-base dark:border-gray-500 dark:text-white whitespace-nowrap cursor-base focus:outline-none hover:border-gray-400 dark:hover:border-gray-300">
                                    <i class="pi pi-plus" style="font-size: 0.9rem"></i>
                                </div>
                                <div class="border-b inline-flex w-full border-gray-300 dark:border-gray-500"></div>
                            </div>
                            <div v-for="variation in variations" :key="variation.id" v-show="variation.show">
                                <div class="flex flex-row">
                                    <div class="basis-1/2 pr-2">
                                        <div class="my-6 ml-2">
                                            <InputLabel for="code" value="Артикул" />
                                            <TextInput
                                                id="code"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="variation.code"
                                            />
                                            <!-- <InputError
                                                class="mt-2"
                                                :message="form.errors.code"
                                            /> -->
                                        </div>
                                        <div class="my-6 ml-2">
                                            <InputLabel for="price" value="Ціна" />
                                            <TextInput
                                                id="price"
                                                type="number"
                                                class="mt-1 block w-full"
                                                v-model="variation.price"
                                            />
                                            <!-- <InputError
                                                class="mt-2"
                                                :message="form.errors.price"
                                            /> -->
                                        </div>
                                        <div class="ml-2">
                                            <div v-for="attribute in variation.attributes" :key="`${attribute.id}${variation.id}`" class="my-6">
                                                <InputLabel :for="attribute.id + variation.id" :value="attribute.name" />
                                                <AutoComplete class="w-full" :dataKey="`${attribute.id}${variation.id}`" :inputId="`${attribute.id}${variation.id}`" v-model="attribute.value" optionLabel="value" dropdown :suggestions="attribute.attribute_options" @complete="attribute.search" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="basis-1/2 pl-2 pt-12">
                                        <FileUpload @select="onFilesVariation($event)" multiple accept="image/*" :maxFileSize="1000000">
                                            <template #empty>
                                                <span>Перетягніть зображення.</span>
                                            </template>
                                        </FileUpload>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <PrimaryButton
                            type="submit"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Створити
                        </PrimaryButton>
                    </form>
                </template>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
