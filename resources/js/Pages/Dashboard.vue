<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    projects: Array,
});

const form = useForm({
    title: '',
    description: '',
});

const submit = () => {
    form.post(route('projects.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-2xl font-bold mb-4">Añadir Nuevo Proyecto</h3>

                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-gray-300">Título</label>
                                <input
                                    type="text"
                                    id="title"
                                    v-model="form.title"
                                    class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-white"
                                    required
                                />
                                <div v-if="form.errors.title" class="text-sm text-red-500 mt-1">{{ form.errors.title }}</div>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="block text-sm font-medium text-gray-300">Descripción (Opcional)</label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full bg-gray-900 border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-white"
                                ></textarea>
                                <div v-if="form.errors.description" class="text-sm text-red-500 mt-1">{{ form.errors.description }}</div>
                            </div>

                            <div>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                                >
                                    Guardar Proyecto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-2xl font-bold mb-4">Mis Proyectos</h3>
                        <div v-if="projects && projects.length > 0" class="space-y-4">

                            <Link v-for="project in projects" :key="project.id" :href="route('projects.show', project.id)">
                                <div class="p-4 border border-gray-700 rounded-lg transition-all duration-300 hover:border-indigo-500 hover:bg-gray-700/50 cursor-pointer">
                                    <h4 class="font-bold text-lg">{{ project.title }}</h4>
                                    <p class="text-gray-400 text-sm mt-1">{{ project.description }}</p>
                                </div>
                            </Link>

                        </div>
                        <div v-else>
                            <p class="text-gray-400">No tienes proyectos aún. ¡Añade uno!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
