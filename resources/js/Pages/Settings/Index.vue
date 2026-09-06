<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    announcement_enabled: props.settings.announcement_enabled,
    announcement_mode: props.settings.announcement_mode,
    announcement_custom_text: props.settings.announcement_custom_text,
    free_shipping_threshold: props.settings.free_shipping_threshold,
});

const submit = () => {
    form.patch(route("settings.update"));
};
</script>

<template>
    <Head title="Налаштування" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Налаштування
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="font-bold mb-4">Рекламна стрічка</h3>
                        <form @submit.prevent="submit">
                            <div class="flex items-center gap-2">
                                <input
                                    id="announcement_enabled"
                                    type="checkbox"
                                    v-model="form.announcement_enabled"
                                    class="rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                                />
                                <label for="announcement_enabled" class="text-sm font-medium text-gray-900">
                                    Показувати рекламну стрічку на сайті
                                </label>
                            </div>

                            <div class="mt-6">
                                <InputLabel for="announcement_mode" value="Режим стрічки" />
                                <select
                                    id="announcement_mode"
                                    v-model="form.announcement_mode"
                                    class="mt-1 block w-full border-gray-300 rounded-lg text-sm"
                                >
                                    <option value="free_shipping">Безкоштовна доставка</option>
                                    <option value="custom">Свій текст</option>
                                </select>
                                <InputError :message="form.errors.announcement_mode" />
                            </div>

                            <div v-if="form.announcement_mode === 'free_shipping'" class="mt-6">
                                <InputLabel for="free_shipping_threshold" value="Сума для безкоштовної доставки, грн" />
                                <TextInput
                                    id="free_shipping_threshold"
                                    type="number"
                                    min="0"
                                    step="1"
                                    class="mt-1 block w-full"
                                    v-model="form.free_shipping_threshold"
                                />
                                <InputError :message="form.errors.free_shipping_threshold" />
                            </div>

                            <div v-else class="mt-6">
                                <InputLabel for="announcement_custom_text" value="Текст стрічки" />
                                <TextInput
                                    id="announcement_custom_text"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.announcement_custom_text"
                                />
                                <InputError :message="form.errors.announcement_custom_text" />
                            </div>

                            <PrimaryButton
                                type="submit"
                                class="mt-6"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Зберегти
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
