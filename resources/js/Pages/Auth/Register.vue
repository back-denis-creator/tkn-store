<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
// import InputError from '@/Components/InputError.vue';
// import InputLabel from '@/Components/InputLabel.vue';
// import PrimaryButton from '@/Components/PrimaryButton.vue';
// import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

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
        <Head title="Register" />
        <!-- Register card  -->
        <div class="container mx-auto border px-5 py-5 shadow-sm md:w-1/2">
          <div class="">
            <p class="text-4xl font-bold uppercase">{{ $t("Sign Up") }}</p>
            <p>{{ $t("Create a user") }}</p>
          </div>

          <form class="mt-6 flex flex-col">
            <label for="name">{{ $t("Name") }}</label>
            <input
              id="name"
              class="mb-3 mt-3 border px-4 py-2"
              type="text"
              placeholder="Bogdan Bulakh"
              v-model="form.name"
              required
              autofocus
              autocomplete="name"
            />
            <!-- <InputError class="mt-2" :message="form.errors.name" /> -->
            <label class="mt-3" for="email">{{ $t("Email") }}</label>
            <input
              id="email"
              class="mt-3 border px-4 py-2"
              type="email"
              placeholder="user@mail.com"
              v-model="form.email"
              required
              autocomplete="username"
            />
            <!-- <InputError class="mt-2" :message="form.errors.email" /> -->
            <label class="mt-5" for="password">{{ $t("Password") }}</label>
            <input
              id="password"
              class="mt-3 border px-4 py-2"
              type="password"
              placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
              v-model="form.password"
              required
              autocomplete="new-password"
            />
            <!-- <InputError class="mt-2" :message="form.errors.password" /> -->
            <label class="mt-5" for="password_confirmation">{{ $t("Confirm Password") }}</label>
            <input
              id="password_confirmation"
              class="mt-3 border px-4 py-2"
              type="password"
              placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
              v-model="form.password_confirmation"
              required
              autocomplete="new-password"
            />
            <!-- <InputError class="mt-2" :message="form.errors.password_confirmation" /> -->

            <div class="mt-4 flex justify-between">
              <div class="flex gap-2 items-center">
                <input type="checkbox" />
                <label for="checkbox">
                  I have read and agree with
                  <a href="#" class="text-emerald-500">terms &amp; conditions</a>
                </label>
              </div>
            </div>

            <Button class="my-5 w-full uppercase" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submit">
              {{ $t("Sign Up") }}
            </Button>
          </form>
        </div>
      <!-- /Register Card  -->
    </GuestLayout>
</template>
