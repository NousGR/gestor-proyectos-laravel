<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    _method: 'patch',
    name: user.name,
    email: user.email,
    avatar: null,
});

const avatarPreview = ref(user.avatar_url);
const avatarInput = ref(null);

const updateAvatarPreview = () => {
    const file = avatarInput.value.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        avatarPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);

    form.avatar = file;
};

const submit = () => {
    form.post(route('profile.update'), {
        onSuccess: () => {
            form.reset('avatar');
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Información del Perfil
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Actualiza la información de tu perfil y tu dirección de correo electrónico.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <div>
                <InputLabel for="avatar" value="Avatar" />
                <div class="mt-2 flex items-center gap-x-3">
                    <img v-if="avatarPreview" :src="avatarPreview" class="h-16 w-16 rounded-full object-cover" alt="Avatar">
                    <div v-else class="h-16 w-16 rounded-full bg-gray-700 flex items-center justify-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <input
                        ref="avatarInput"
                        type="file"
                        @input="updateAvatarPreview"
                        class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-600 file:text-white hover:file:bg-gray-700"
                    >
                </div>
                 <InputError class="mt-2" :message="form.errors.avatar" />
            </div>

            <div>
                <InputLabel for="name" value="Nombre" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full bg-gray-900 border-gray-700 dark:text-gray-300"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-gray-900 border-gray-700 dark:text-gray-300"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Tu dirección de correo no está verificada.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Haz clic aquí para reenviar el correo de verificación.
                    </Link>
                </p>
                <div v-show="status === 'verification-link-sent'" class="mt-2 font-medium text-sm text-green-600">
                    Se ha enviado un nuevo enlace de verificación a tu correo.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Guardar</PrimaryButton>
                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Guardado.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
