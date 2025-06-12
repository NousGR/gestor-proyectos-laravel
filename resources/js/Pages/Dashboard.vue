<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ImageIcon, Folder, BarChart, Code, TestTube2, Palette, Film, Music, Plane
} from 'lucide-vue-next';
import { shallowRef } from 'vue';

defineProps({
    projects: Array,
});

const icons = shallowRef({
    Folder, BarChart, Code, TestTube2, Palette, Film, Music, Plane
});

const colors = [
    { name: 'Rojo', hex: '#ef4444' },
    { name: 'Naranja', hex: '#f97316' },
    { name: 'Amarillo', hex: '#eab308' },
    { name: 'Verde', hex: '#22c55e' },
    { name: 'Azul', hex: '#3b82f6' },
    { name: 'Indigo', hex: '#6366f1' },
    { name: 'Violeta', hex: '#8b5cf6' },
    { name: 'Rosa', hex: '#ec4899' },
];

const form = useForm({
    title: '',
    description: '',
    image: null,
    color: colors[4].hex,
    icon: 'Folder',
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
                             <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Color</label>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="color in colors"
                                            :key="color.hex"
                                            type="button"
                                            @click="form.color = color.hex"
                                            class="w-8 h-8 rounded-full transition-transform duration-150"
                                            :style="{ backgroundColor: color.hex }"
                                            :class="{ 'ring-2 ring-offset-2 ring-offset-gray-800 ring-white': form.color === color.hex, 'hover:scale-110': form.color !== color.hex }"
                                        ></button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Icono</label>
                                    <div class="grid grid-cols-4 gap-2 p-2 bg-gray-900/50 rounded-lg">
                                        <button
                                            v-for="(component, name) in icons"
                                            :key="name"
                                            type="button"
                                            @click="form.icon = name"
                                            class="flex items-center justify-center p-2 rounded-md transition-colors"
                                            :class="{
                                                'bg-indigo-600 text-white': form.icon === name,
                                                'text-gray-400 hover:bg-gray-700': form.icon !== name,
                                            }"
                                        >
                                            <component :is="component" class="h-5 w-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Imagen de Portada (Opcional)</label>
                                <input
                                    type="file"
                                    @input="form.image = $event.target.files[0]"
                                    class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-gray-700 dark:file:text-gray-300 hover:file:bg-indigo-100 dark:hover:file:bg-gray-600"
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
                                <div
                                    class="rounded-lg overflow-hidden border border-gray-700 bg-gray-900/50 flex flex-col h-full transition-all duration-300 hover:shadow-2xl hover:border-indigo-500"
                                    :style="{ '--project-color': project.color || '#6366f1' }"
                                >
                                    <div v-if="project.image_url" class="w-full h-40 bg-cover bg-center border-b-4 border-[var(--project-color)]" :style="{ backgroundImage: `url(/storage/${project.image_url})` }"></div>
                                    <div v-else class="w-full h-40 flex items-center justify-center border-b-4" :style="{ backgroundColor: project.color + '20', borderColor: project.color }">
                                        <component v-if="project.icon && icons[project.icon]" :is="icons[project.icon]" class="w-16 h-16" :style="{ color: project.color }" />
                                        <ImageIcon v-else class="w-12 h-12 text-gray-500" />
                                    </div>
                                    <div class="p-4 flex flex-col flex-grow">
                                        <h4 class="font-bold text-lg text-white">{{ project.title }}</h4>
                                        <p class="text-gray-400 text-sm mt-1 truncate flex-grow">{{ project.description }}</p>

                                        <div class="mt-4">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-xs font-semibold text-gray-400">Progreso</span>
                                                <span class="text-xs font-bold text-white">{{ project.progress }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-700 rounded-full h-2.5">
                                                <div class="h-2.5 rounded-full" :style="{ width: project.progress + '%', backgroundColor: 'var(--project-color)' }"></div>
                                            </div>
                                        </div>
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
