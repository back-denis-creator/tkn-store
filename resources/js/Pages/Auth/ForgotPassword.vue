<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
// import InputError from '@/Components/InputError.vue';
// import InputLabel from '@/Components/InputLabel.vue';
// import PrimaryButton from '@/Components/PrimaryButton.vue';
// import TextInput from '@/Components/TextInput.vue';
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
        <div class="container mx-auto border px-5 py-5 shadow-sm md:w-1/2 mt-10 mb-10">
            <div class="">
                <p class="text-4xl font-bold">Забули пароль? 🔒</p>
                <p>Введіть свою електронну адресу, і ми надішлемо вам інструкції щодо зміни пароля</p>
            </div>
            <form class="mt-6 flex flex-col" @submit.prevent="submit">
                <label for="email">Пошта</label>
                <input
                    id="email"
                    v-model="form.email"
                    class="mb-3 mt-3 border px-4 py-2"
                    type="email"
                    placeholder="youremail@domain.com"
                    required
                    autofocus
                    autocomplete="username"
                />
                <!-- <InputError class="mt-2" :message="form.errors.email" /> -->
                <button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="my-5 w-full bg-amber-400 py-2 text-black hover:bg-yellow-300">
                    НАДІСЛАТИ ЛІНКУ ДЛЯ СКИДАННЯ
                </button>
            </form>
        </div>
    </GuestLayout>
</template>
