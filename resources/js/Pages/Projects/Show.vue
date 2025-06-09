<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import { Trash2 } from 'lucide-vue-next';

const props = defineProps({
    project: Object,
});

const form = useForm({
    title: '',
});

const submitTask = () => {
    form.post(route('tasks.store', { project_id: props.project.id }), {
        onSuccess: () => form.reset(),
    });
};

const toggleTaskCompletion = (task) => {
    router.patch(route('tasks.update', task.id), {
        is_completed: !task.is_completed
    }, {
        preserveScroll: true,
    });
};

const deleteTask = (task) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta tarea?')) {
        router.delete(route('tasks.destroy', task.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head :title="'Proyecto: ' + project.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <Link :href="route('dashboard')" class="hover:underline">Proyectos</Link>
                <span class="mx-2">/</span>
                {{ project.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-2 text-gray-100">Descripción del Proyecto</h3>
                    <p class="text-gray-400">{{ project.description }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4 text-gray-100">Añadir Nueva Tarea</h3>
                    <form @submit.prevent="submitTask">
                         <div class="flex items-center gap-4">
                            <input
                                type="text"
                                v-model="form.title"
                                class="flex-grow bg-gray-900 border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-white"
                                placeholder="¿Qué necesitas hacer?"
                                required
                            />
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
                            >
                                Añadir Tarea
                            </button>
                        </div>
                        <div v-if="form.errors.title" class="text-sm text-red-500 mt-1">{{ form.errors.title }}</div>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4 text-gray-100">Tareas del Proyecto</h3>
                    <div v-if="project.tasks && project.tasks.length > 0" class="space-y-3">
                        <div v-for="task in project.tasks" :key="task.id" class="p-4 flex items-center justify-between border border-gray-700 rounded-lg bg-gray-900/50">
                            <div class="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    :checked="task.is_completed"
                                    @change="toggleTaskCompletion(task)"
                                    class="h-5 w-5 rounded bg-gray-700 border-gray-600 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span :class="{'line-through text-gray-500': task.is_completed, 'text-gray-200': !task.is_completed}">
                                    {{ task.title }}
                                </span>
                            </div>
                            <button @click="deleteTask(task)" class="text-gray-500 hover:text-red-500 transition-colors">
                                <Trash2 class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-gray-400">Este proyecto no tiene tareas todavía.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
