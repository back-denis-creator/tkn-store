<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const agreedToTerms = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="$t('Sign up')">
            <meta name="robots" content="noindex, nofollow" />
        </Head>
        <!-- Register card  -->
        <div class="container mx-auto rounded-lg border border-gray-200 px-5 py-5 shadow-sm md:w-1/2">
          <div class="">
            <p class="text-4xl font-bold uppercase">{{ $t("Sign up") }}</p>
            <p>{{ $t("Create a user") }}</p>
          </div>

          <form class="mt-6 flex flex-col" @submit.prevent="submit">
            <InputLabel for="name" :value="$t('Name')" />
            <TextInput
              id="name"
              class="mt-1 block w-full focus:!border-amber-500 focus:!ring-amber-500"
              type="text"
              placeholder="Bogdan Bulakh"
              v-model="form.name"
              required
              autofocus
              autocomplete="name"
            />
            <InputError :message="form.errors.name" />

            <InputLabel class="mt-3" for="email" :value="$t('Email')" />
            <TextInput
              id="email"
              class="mt-1 block w-full focus:!border-amber-500 focus:!ring-amber-500"
              type="email"
              placeholder="user@mail.com"
              v-model="form.email"
              required
              autocomplete="username"
            />
            <InputError :message="form.errors.email" />

            <InputLabel class="mt-5" for="password" :value="$t('Password')" />
            <TextInput
              id="password"
              class="mt-1 block w-full focus:!border-amber-500 focus:!ring-amber-500"
              type="password"
              placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
              v-model="form.password"
              required
              autocomplete="new-password"
            />
            <InputError :message="form.errors.password" />

            <InputLabel class="mt-5" for="password_confirmation" :value="$t('Confirm Password')" />
            <TextInput
              id="password_confirmation"
              class="mt-1 block w-full focus:!border-amber-500 focus:!ring-amber-500"
              type="password"
              placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
              v-model="form.password_confirmation"
              required
              autocomplete="new-password"
            />
            <InputError :message="form.errors.password_confirmation" />

            <div class="mt-4 flex justify-between">
              <label class="flex gap-2 items-center">
                <Checkbox v-model:checked="agreedToTerms" class="!text-amber-500 focus:!ring-amber-500" />
                <span>
                  I have read and agree with
                  <a href="#" class="text-amber-600 hover:text-amber-700">terms &amp; conditions</a>
                </span>
              </label>
            </div>

            <Button
              type="submit"
              class="my-5 h-10 w-full !border-none !bg-amber-400 uppercase !text-black hover:!bg-yellow-300"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              {{ $t("Sign up") }}
            </Button>
          </form>
        </div>
      <!-- /Register Card  -->
    </GuestLayout>
</template>
