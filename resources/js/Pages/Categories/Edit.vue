<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, useForm } from "@inertiajs/vue3";
import DropdownMenu from '@/Components/DropdownMenu.vue';

const props = defineProps({
    category: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Object,
        default: () => ({}),
    }
});

const form = useForm({
    id: props.category.id,
    name: props.category.name,
    description: props.category.description,
    parent_id: props.category.parent_id
});

const submit = () => {
    form.put(route("categories.update", props.category.id));
};

const handleUpdateCategory = (category) => {
    form.parent_id = category.id
}
</script>

<template>
    <Head title="Редагувати категорію" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Редагувати категорію
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 sm:rounded-lg">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white sm:rounded-lg border-gray-200">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="name" value="Імʼя" />

                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required
                                    autofocus  />

                                <InputError class="mt-2" :message="form.errors.name" />
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
                            <DropdownMenu class="my-6" :items="categories" title="Батьківська категорія" @update:selected="handleUpdateCategory" :selected="props.category.parent_id" />
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
