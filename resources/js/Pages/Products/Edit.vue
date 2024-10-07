<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, useForm } from "@inertiajs/vue3";
import CheckboxArray from '@/Components/CheckboxArray.vue';
import { ref } from 'vue';

const props = defineProps({
    product: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Object,
        default: () => ({}),
    }
})

const form = useForm({
    id: props.product.id,
    name: props.product.name,
    price: props.product.price,
    description: props.product.description,
    category_ids: props.product.categories.reduce((filtered, category) => {
        if (category.value) filtered.push(category.id)
        return filtered
    }, [])
})

const submit = () => {
    form.put(route("products.update", props.product.id));
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
        value: !!(props.product.categories.findIndex((cat) => cat.id === category.id) >= 0)
    }
})])
</script>

<template>
    <Head title="Редагування товару" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Редагування товару
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="name" value="Імʼя" />

                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required
                                    autofocus autocomplete="username" />

                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>
                            <div class="my-6">
                                <InputLabel for="price" value="Ціна" />

                                <TextInput id="price" type="text" class="mt-1 block w-full" v-model="form.price" required
                                    autofocus autocomplete="username" />

                                <InputError class="mt-2" :message="form.errors.price" />
                            </div>
                            <div class="my-6">
                                <label for="slug"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Опис</label>
                                <textarea type="text" v-model="form.description" name="content" id=""
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>

                                <div v-if="form.errors.description" class="text-sm text-red-600">
                                    {{ form.errors.description }}
                                </div>
                            </div>
                            <div class="my-6">
                                <span class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Категорії</span>
                                <CheckboxArray :items="categoryItems" @update:checked="handleUpdateCategories" />
                            </div>
                            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing">
                                Зберегти
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
