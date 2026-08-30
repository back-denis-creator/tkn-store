<script setup>
import FileUpload from 'primevue/fileupload';
import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const newOptionValue = ref('');

function onFileSelect(index, event) {
    const file = event.files[0];
    const reader = new FileReader();
    reader.onload = async (e) => {
        form.options[index].new_src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function addOption() {
    const value = newOptionValue.value.trim();
    if (!value) return;
    form.options.push({
        id: 'new',
        value,
        attribute_id: props.attribute.id,
        src: null,
        new_src: null,
        meta: {},
    });
    newOptionValue.value = '';
}

const props = defineProps({
    attribute: {
        type: Object,
        default: () => ({}),
    },
    options: {
        type: Array,
        default: () => ([]),
    },
    color_groups: {
        type: Array,
        default: () => ([]),
    }
});

const form = useForm({
    name: props.attribute.name,
    description: props.attribute.description,
    options: [...props.options.map((option) => {
        return {
            id: option.id,
            value: option.value,
            attribute_id: option.attribute_id,
            src: option.img_url ? option.img_url : null,
            new_src: null,
            meta: option.meta ? props.color_groups.find(({id}) => id === Number(option.meta)) : {}
        }
    })],
});

const submit = () => {
    form.put(route("attributes.update", props.attribute.id));
};
</script>

<template>
    <Head title="Редагувати атрибут" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Редагувати атрибут
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
                                <div class="flex items-end gap-3 mb-6">
                                    <div class="flex-1">
                                        <InputLabel for="new_option" value="Новий варіант" />
                                        <TextInput
                                            id="new_option"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="newOptionValue"
                                            @keydown.enter.prevent="addOption"
                                        />
                                    </div>
                                    <PrimaryButton type="button" @click="addOption">Додати варіант</PrimaryButton>
                                </div>
                                <Accordion value="0">
                                    <AccordionPanel v-for="(option, index) in form.options" :key="index" :value="String(index)">
                                        <AccordionHeader>{{ option.value }}</AccordionHeader>
                                        <AccordionContent>
                                            <Select v-model="option.meta" :options="color_groups" optionLabel="name" placeholder="Обрати группу" class="w-full" />
                                            <FileUpload mode="basic" @select="onFileSelect(index, $event)" customUpload auto severity="secondary" class="p-button-outlined mt-4" />
                                            <img v-if="option.new_src" :src="option.new_src" alt="Image" class="shadow-md rounded-xl w-full sm:w-64" />
                                            <img v-else-if="option.src" :src="option.src" alt="Image" class="shadow-md rounded-xl w-full sm:w-64" />
                                        </AccordionContent>
                                    </AccordionPanel>
                                </Accordion>
                            </div>

                            <PrimaryButton
                                type="submit"
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
