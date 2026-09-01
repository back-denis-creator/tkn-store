<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password">
            <meta name="robots" content="noindex, nofollow" />
        </Head>
        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>
        <div class="container mx-auto rounded-lg border border-gray-200 px-5 py-5 shadow-sm md:w-1/2 mt-10 mb-10">
            <div class="">
                <p class="text-4xl font-bold">Забули пароль? 🔒</p>
                <p>Введіть свою електронну адресу, і ми надішлемо вам інструкції щодо зміни пароля</p>
            </div>
            <form class="mt-6 flex flex-col" @submit.prevent="submit">
                <InputLabel for="email" value="Пошта" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    class="mb-3 mt-1 block w-full focus:!border-amber-500 focus:!ring-amber-500"
                    type="email"
                    placeholder="youremail@domain.com"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError :message="form.errors.email" />

                <Button
                    type="submit"
                    class="my-5 h-10 w-full !border-none !bg-amber-400 uppercase !text-black hover:!bg-yellow-300"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Надіслати лінку для скидання
                </Button>
            </form>
        </div>
    </GuestLayout>
</template>
