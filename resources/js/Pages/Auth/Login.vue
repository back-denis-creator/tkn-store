<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
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
        <Head :title="$t('Log in')">
            <meta name="robots" content="noindex, nofollow" />
        </Head>
        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>
        <!-- Login card  -->
        <div class="container mx-auto rounded-lg border border-gray-200 px-5 py-5 shadow-sm md:w-1/2 mt-10 mb-10">
          <div class="">
            <p class="text-4xl font-bold uppercase">{{ $t("Log in") }}</p>
            <p>{{ $t("Welcome") }}! 👋🏻</p>
          </div>

          <form class="mt-6 flex flex-col" @submit.prevent="submit">
            <InputLabel for="email" :value="$t('Email')" />
            <TextInput
              id="email"
              v-model="form.email"
              class="mt-1 block w-full focus:!border-amber-500 focus:!ring-amber-500"
              type="email"
              placeholder="youremail@domain.com"
              required
              autofocus
              autocomplete="username"
            />
            <InputError :message="form.errors.email" />

            <InputLabel class="mt-3" for="password" :value="$t('Password')" />
            <TextInput
              id="password"
              v-model="form.password"
              class="mt-1 block w-full focus:!border-amber-500 focus:!ring-amber-500"
              type="password"
              placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
              required
              autocomplete="current-password"
            />
            <InputError :message="form.errors.password" />

            <div class="mt-4 flex justify-between">
              <label class="flex gap-2 items-center">
                <Checkbox v-model:checked="form.remember" class="!text-amber-500 focus:!ring-amber-500" />
                <span>{{ $t("Remember me") }}</span>
              </label>
              <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="text-amber-600 hover:text-amber-700"
              >
                {{ $t("Forgot Password?") }}
              </Link>
            </div>

            <Button
              type="submit"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              class="my-5 h-10 w-full !border-none !bg-amber-400 uppercase !text-black hover:!bg-yellow-300"
            >
              {{ $t("Log in") }}
            </Button>

            <p v-if="canRegister" class="text-center text-sm text-gray-600">
              {{ $t("Don't have an account?") }}
              <Link :href="route('register')" class="font-medium text-amber-600 hover:text-amber-700">
                {{ $t("Sign up") }}
              </Link>
            </p>
          </form>
        </div>
      <!-- /Login Card  -->
    </GuestLayout>
</template>
