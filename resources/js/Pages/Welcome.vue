<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome" />
    <div class="relative min-h-screen flex flex-col items-center justify-center bg-gray-900 text-white selection:bg-indigo-500 selection:text-white">

        <div class="absolute top-0 right-0 p-6">
            <nav v-if="canLogin" class="flex flex-1 justify-end">
                <Link
                    v-if="$page.props.auth.user"
                    href="/dashboard"
                    class="rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-white/70 focus:outline-none focus-visible:ring-indigo-500"
                >
                    Dashboard
                </Link>

                <template v-else>
                    <Link
                        href="/login"
                        class="rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-white/70 focus:outline-none focus-visible:ring-indigo-500"
                    >
                        Log in
                    </Link>

                    <Link
                        v-if="canRegister"
                        href="/register"
                        class="ms-4 rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-white/70 focus:outline-none focus-visible:ring-indigo-500"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </div>

        <div class="text-center">
            <div class="flex justify-center mb-4">
                <svg class="h-16 w-16 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h6.375M9 12h6.375m-6.375 5.25h6.375M5.25 6h.008v.008H5.25V6Zm.75 5.25h.008v.008H6v-.008Zm-.75 5.25h.008v.008H5.25v-.008Zm9-5.25h.008v.008H15v-.008Zm-.75 5.25h.008v.008h-.008v-.008Zm.75-5.25h.008v.008h-.008v-.008Zm.75 5.25h.008v.008h-.008v-.008Zm-.75-5.25h.008v.008h-.008V12Z" />
                </svg>
            </div>
            <h1 class="text-4xl font-bold">Bienvenido al Gestor de Proyectos</h1>
            <p class="mt-2 text-lg text-gray-400">Inicia sesión o regístrate para empezar a organizar tus tareas.</p>
        </div>

    </div>
</template>
