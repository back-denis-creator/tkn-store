<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { Head, useForm } from "@inertiajs/vue3";
import CheckboxArray from '@/Components/CheckboxArray.vue';
import { ref } from 'vue';
import DropdownMenu from '@/Components/DropdownMenu.vue';

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

const addVariation = () => {
    variations.value.push(Object.assign({}, variations.value[variations.value.length - 1], {show: false}))
}

const selectVariation = (index) => {
    variations.value = variations.value.map((variation, i) => {
        variation.show = i === index
        return variation
    })
}

const variations = ref([{
    price: "",
    show: true,
    attributes: props.attributes.map((attribute) => {
        return {
            name: attribute.name,
            value: "",
            id: attribute.id
        }
    })
}])
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
            <div class="mx-auto max-w-7xl">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="name" value="Імʼя" />

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="username"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.name"
                                />
                            </div>
                            <div class="my-6">
                                <label
                                    for="description"
                                    class="block mb-2 text-sm font-medium text-gray-900"
                                    >Опис</label
                                >
                                <textarea
                                    type="text"
                                    v-model="form.description"
                                    name="description"
                                    id=""
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                ></textarea>

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
                                    <button v-for="(variation, index) in variations" :key="index" @click.prevent="selectVariation(index)" :class="variation.show ? 'border border-b-0 border-gray-300 sm:text-base dark:border-gray-500 rounded-t-md dark:text-white whitespace-nowrap focus:outline-none' : 'bg-transparent border-b border-gray-300 sm:text-base dark:border-gray-500 dark:text-white whitespace-nowrap cursor-base focus:outline-none hover:border-gray-400 dark:hover:border-gray-300'" class="inline-flex items-center h-12 px-4 py-2 text-sm text-center text-gray-700">
                                        {{ `Варіація ${index}` }}
                                    </button>
                                    <button @click.prevent="addVariation" class="inline-flex items-center h-12 px-4 py-2 text-sm text-center text-gray-700 bg-transparent border-b border-gray-300 sm:text-base dark:border-gray-500 dark:text-white whitespace-nowrap cursor-base focus:outline-none hover:border-gray-400 dark:hover:border-gray-300">
                                        +
                                    </button>
                                    <button class="border-b inline-flex w-full border-gray-300 dark:border-gray-500"></button>
                                </div>
                                <div v-for="(variation, index) in variations" :key="index" v-show="variation.show">
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
                                        <div v-for="(attribute, index) in variation.attributes" :key="index" class="my-6">
                                            <InputLabel :for="attribute.id" :value="attribute.name" />

                                            <TextInput
                                                :id="attribute.id"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="attribute.value"
                                            />
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
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
