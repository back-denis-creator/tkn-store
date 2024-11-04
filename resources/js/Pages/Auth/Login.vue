<script setup>
// import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
// import InputError from '@/Components/InputError.vue';
// import InputLabel from '@/Components/InputLabel.vue';
// import PrimaryButton from '@/Components/PrimaryButton.vue';
// import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />
        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>
        <!-- Login card  -->
        <div class="container mx-auto border px-5 py-5 shadow-sm md:w-1/2 mt-10 mb-10">
          <div class="">
            <p class="text-4xl font-bold">Авторизація</p>
            <p>Ласкаво просимо! 👋🏻</p>
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
            <label for="password">Пароль</label>
            <input
              id="password"
              v-model="form.password"
              class="mt-3 border px-4 py-2"
              type="password"
              placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
              required
              autocomplete="current-password"
            />
            <!-- <InputError class="mt-2" :message="form.errors.password" /> -->
            <div class="mt-4 flex justify-between">
              <div class="flex gap-2 items-center">
                <input type="checkbox" v-model="form.remember" />
                <label for="checkbox">Запам'ятати</label>
              </div>
              <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="text-emerald-500"
              >
                Забули пароль?
              </Link>
            </div>

            <button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="my-5 w-full bg-emerald-500 py-2 text-white">
              УВІЙТИ
            </button>

            <p class="text-center text-gray-500">АБО ЧЕРЕЗ</p>

            <div class="my-5 flex gap-2">
              <button class="w-1/2 bg-blue-800 py-2 text-white">FACEBOOK</button>
              <button class="w-1/2 bg-orange-500 py-2 text-white">GOOGLE</button>
            </div>

            <p class="text-center">
              Немає акаунта?
              <Link
                :href="route('register')"
                class="text-emerald-500"
              >
                Створити
              </Link>
              <!-- <a href="sign-up.html" class="text-violet-900">Створити</a> -->
            </p>
          </form>
        </div>
      <!-- /Login Card  -->
    </GuestLayout>
</template>
