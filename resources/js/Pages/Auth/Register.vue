<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({
    siteKey: String,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    'g-recaptcha-response': '',
});

const recaptchaContainer = ref(null);

const handleRecaptchaVerify = (token) => {
    form['g-recaptcha-response'] = token;
};

window.handleRecaptchaVerify = handleRecaptchaVerify;

onMounted(() => {
    const checkGrecaptcha = setInterval(() => {
        if (window.grecaptcha && window.grecaptcha.render && recaptchaContainer.value) {
            clearInterval(checkGrecaptcha);
            grecaptcha.render(recaptchaContainer.value, {
                'sitekey' : props.siteKey,
                'callback' : 'handleRecaptchaVerify',
            });
        }
    }, 100);
});

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4">
                <div ref="recaptchaContainer"></div>
                <InputError class="mt-2" :message="form.errors['g-recaptcha-response']" />

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    ¿No ves el CAPTCHA? Podría estar siendo bloqueado por tu navegador. Intenta desactivar el protector de privacidad para este sitio.
                </p>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    href="/login"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Already registered?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
