<template>
    <Dialog 
        v-model:visible="visible" 
        modal 
        :header="$t('ConsultationDisplay')" 
        :style="{ width: '90vw', maxWidth: '400px' }"
        :pt="{
            closeButton: {
                class: '!border-none !ring-0 !bg-transparent !shadow-none hover:!bg-gray-100 transition-colors focus:!ring-0 focus:!outline-none !outline-none'
            }
        }"
    >
        <form @submit.prevent="submitForm" class="flex flex-col gap-6 pt-4">
            <div v-if="form.service" class="flex flex-col gap-2">
                <label for="service" class="text-sm font-medium text-gray-500">{{ $t('ServiceLabel') }}</label>
                <InputText id="service" v-model="form.service" class="w-full !opacity-100 !bg-gray-50 font-semibold text-gray-800" disabled />
            </div>
            <div class="flex flex-col gap-2">
                <label for="name" class="text-sm font-medium text-gray-700">{{ $t('ConsultationName') }}</label>
                <InputText id="name" v-model="form.name" class="w-full" autocomplete="off" required />
            </div>
            <div class="flex flex-col gap-2">
                <label for="phone" class="text-sm font-medium text-gray-700">{{ $t('ConsultationPhone') }}</label>
                <InputMask id="phone" v-model="form.phone" mask="+38 (099) 999-99-99" placeholder="+38 (0__) ___-__-__" class="w-full" required />
            </div>
            <div class="flex flex-col gap-3 mt-4">
                <Button type="submit" :label="$t('ConsultationSubmit')" :loading="loading" class="w-full !bg-amber-400 !border-none !text-black hover:!bg-amber-500"></Button>
                <Button type="button" :label="$t('Cancel')" severity="secondary" variant="text" @click="visible = false" class="w-full"></Button>
            </div>
            
            <transition name="fade">
                <Message v-if="success" severity="success" icon="pi pi-check-circle" class="mt-2">{{ $t('ConsultationSuccess') }}</Message>
            </transition>
            <transition name="fade">
                <Message v-if="error" severity="error" icon="pi pi-exclamation-triangle" class="mt-2">{{ $t('ConsultationError') }}</Message>
            </transition>
        </form>
    </Dialog>
</template>

<script setup>
import { ref, reactive } from 'vue';

const visible = ref(false);
const loading = ref(false);
const success = ref(false);
const error = ref(false);

const form = reactive({
    name: '',
    phone: '',
    service: ''
});

const open = (serviceName = '') => {
    visible.value = true;
    success.value = false;
    error.value = false;
    form.service = serviceName;
};

const submitForm = async () => {
    loading.value = true;
    error.value = false;
    success.value = false;
    
    try {
        const GOOGLE_SCRIPT_URL = 'https://script.google.com/macros/s/AKfycbw-sDbFPiyOie4ksI2a3cWDQ6oUdRkfNcJof4XvGvGVUUPcIOfmQwqkUr2Jau-n1Okb4Q/exec';
        
        // Only simulate if no real URL is provided
        if (!GOOGLE_SCRIPT_URL || GOOGLE_SCRIPT_URL.includes('YOUR_GOOGLE_SCRIPT_URL_HERE')) {
            console.warn('Google Script URL not set. Simulating success.');
            await new Promise(resolve => setTimeout(resolve, 1500));
            success.value = true;
            form.name = '';
            form.phone = '';
            setTimeout(() => visible.value = false, 3500);
            return;
        }

        const response = await fetch(GOOGLE_SCRIPT_URL, {
            method: 'POST',
            mode: 'no-cors',
            cache: 'no-cache',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                ...form,
                phone: form.phone.replace('+', ''),
                date: new Date().toLocaleString(),
                source: window.location.hostname
            })
        });

        success.value = true;
        form.name = '';
        form.phone = '';
        setTimeout(() => visible.value = false, 3500);
    } catch (err) {
        console.error('Submission error:', err);
        error.value = true;
    } finally {
        loading.value = false;
    }
};

defineExpose({ open });
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
