<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import OptionsEditor from "./Partials/OptionsEditor.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    color_groups: {
        type: Array,
        default: () => ([]),
    },
});

const form = useForm({
    name: "",
    description: "",
    options: [],
});

const isColor = computed(() => form.name.trim() === 'Колір');

const optionsEditor = ref(null);

const submit = () => {
    if (optionsEditor.value?.isBusy) return;
    form.post(route("attributes.store"));
};
</script>

<template>
    <Head title="Створити атрибут" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Створити атрибут
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
                                />

                                <InputError
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
                                <OptionsEditor
                                    ref="optionsEditor"
                                    :options="form.options"
                                    :is-color="isColor"
                                    :color-groups="color_groups"
                                    :error="form.errors.options"
                                />
                            </div>

                            <PrimaryButton
                                type="submit"
                                :class="{ 'opacity-25': form.processing || optionsEditor?.isBusy }"
                                :disabled="form.processing || optionsEditor?.isBusy"
                            >
                                {{ optionsEditor?.isBusy ? 'Обробка фото…' : 'Зберегти' }}
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
