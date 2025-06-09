<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ImageIcon } from 'lucide-vue-next';

defineProps({
    projects: Array,
});

const form = useForm({
    title: '',
    description: '',
    image: null,
});

const submit = () => {
    form.post('/projects', {
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

                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                                <input
                                    type="text"
                                    id="title"
                                    v-model="form.title"
                                    class="mt-1 block w-full bg-gray-900 border-gray-700 dark:text-gray-300 rounded-md shadow-sm"
                                    required
                                />
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción (Opcional)</label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full bg-gray-900 border-gray-700 dark:text-gray-300 rounded-md shadow-sm"
                                ></textarea>
                            </div>

                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Imagen del Proyecto (Opcional)</label>
                                <input
                                    type="file"
                                    @input="form.image = $event.target.files[0]"
                                    class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full mt-2">
                                    {{ form.progress.percentage }}%
                                </progress>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
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
                        <div v-if="projects && projects.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                            <Link v-for="project in projects" :key="project.id" :href="`/projects/${project.id}`">
                                <div class="rounded-lg overflow-hidden border border-gray-700 bg-gray-900/50 transition-all duration-300 hover:shadow-2xl hover:shadow-indigo-500/20 hover:border-indigo-500">
                                    <div v-if="project.image_url" class="w-full h-40 bg-cover bg-center" :style="{ backgroundImage: `url(/storage/${project.image_url})` }"></div>
                                    <div v-else class="w-full h-40 bg-gray-800 flex items-center justify-center">
                                        <ImageIcon class="w-12 h-12 text-gray-500" />
                                    </div>
                                    <div class="p-4">
                                        <h4 class="font-bold text-lg text-white">{{ project.title }}</h4>
                                        <p class="text-gray-400 text-sm mt-1 truncate">{{ project.description }}</p>
                                    </div>
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
