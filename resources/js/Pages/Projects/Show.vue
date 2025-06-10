<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Trash2, Pencil, Check, X, Circle } from 'lucide-vue-next';

const props = defineProps({
    project: Object,
});

const taskForm = useForm({
    title: '',
    due_date: null,
    priority: 'medium',
});

const editingTask = ref(null);
const editTaskForm = useForm({
    title: '',
    due_date: null,
    priority: 'medium',
});

const confirmingTaskDeletion = ref(false);
const taskToDelete = ref(null);

const incompleteTasks = computed(() => {
    if (!props.project.tasks) return [];
    return props.project.tasks.filter(task => !task.is_completed);
});

const completedTasks = computed(() => {
    if (!props.project.tasks) return [];
    return props.project.tasks.filter(task => task.is_completed);
});

const priorityClasses = {
    low: 'text-gray-400',
    medium: 'text-yellow-400',
    high: 'text-red-500',
};

const confirmTaskDeletion = (task) => {
    taskToDelete.value = task;
    confirmingTaskDeletion.value = true;
};

const closeModal = () => {
    confirmingTaskDeletion.value = false;
    taskToDelete.value = null;
};

const submitTask = () => {
    taskForm.post(`/projects/${props.project.id}/tasks`, {
        onSuccess: () => taskForm.reset(),
        preserveScroll: true,
    });
};

const enterEditMode = (task) => {
    editingTask.value = task.id;
    editTaskForm.title = task.title;
    editTaskForm.due_date = task.due_date ? task.due_date.substring(0, 10) : null;
    editTaskForm.priority = task.priority;
};

const cancelEditMode = () => {
    editingTask.value = null;
    editTaskForm.reset();
};

const submitTaskUpdate = (task) => {
    editTaskForm.patch(`/tasks/${task.id}`, {
        onSuccess: () => cancelEditMode(),
        preserveScroll: true,
    });
};

const toggleTaskCompletion = (task) => {
    router.patch(`/tasks/${task.id}`, {
        is_completed: !task.is_completed
    }, {
        preserveScroll: true,
    });
};

const deleteTask = () => {
    router.delete(`/tasks/${taskToDelete.value.id}`, {
        onSuccess: () => closeModal(),
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="'Proyecto: ' + project.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    <Link href="/dashboard" class="hover:underline">Proyectos</Link>
                    <span class="mx-2">/</span>
                    {{ project.title }}
                </h2>
                <Link :href="`/projects/${project.id}/edit`">
                    <button class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        <Pencil class="h-4 w-4 mr-2" />
                        Editar Proyecto
                    </button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4 text-gray-100">Añadir Nueva Tarea</h3>
                    <form @submit.prevent="submitTask" class="space-y-4">
                        <div>
                            <input
                                type="text"
                                v-model="taskForm.title"
                                class="w-full bg-gray-900 border-gray-700 dark:text-gray-300 rounded-md shadow-sm"
                                placeholder="¿Qué necesitas hacer?"
                                required
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-400 mb-1">Fecha Límite</label>
                                <input type="date" id="due_date" v-model="taskForm.due_date" class="w-full bg-gray-900 border-gray-700 dark:text-gray-300 rounded-md shadow-sm" />
                            </div>
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-400 mb-1">Prioridad</label>
                                <select id="priority" v-model="taskForm.priority" class="w-full bg-gray-900 border-gray-700 dark:text-gray-300 rounded-md shadow-sm">
                                    <option value="low">Baja</option>
                                    <option value="medium">Media</option>
                                    <option value="high">Alta</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button
                                    type="submit"
                                    :disabled="taskForm.processing"
                                    class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    Añadir Tarea
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4 text-gray-100">Tareas Pendientes</h3>
                    <div v-if="incompleteTasks.length > 0" class="space-y-3">
                        <div v-for="task in incompleteTasks" :key="task.id" class="p-4 flex items-center justify-between border border-gray-700 rounded-lg bg-gray-900/50">
                            <div v-if="editingTask === task.id" class="flex-grow flex items-center gap-2">
                                <input type="text" v-model="editTaskForm.title" class="flex-grow bg-gray-700 border-gray-600 rounded-md text-white" @keyup.enter="submitTaskUpdate(task)" @keyup.esc="cancelEditMode" />
                                <button @click="submitTaskUpdate(task)" class="text-green-500 hover:text-green-400"><Check class="h-5 w-5" /></button>
                                <button @click="cancelEditMode" class="text-gray-500 hover:text-gray-400"><X class="h-5 w-5" /></button>
                            </div>
                            <div v-else class="flex-grow flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    :checked="task.is_completed"
                                    @change="toggleTaskCompletion(task)"
                                    class="h-5 w-5 rounded bg-gray-700 border-gray-600 text-indigo-600"
                                />
                                <Circle class="h-4 w-4 flex-shrink-0" :class="priorityClasses[task.priority]" />
                                <span class="text-gray-200">{{ task.title }}</span>
                                <span v-if="task.due_date" class="text-xs text-gray-500 ml-auto me-4">{{ new Date(task.due_date).toLocaleDateString() }}</span>
                            </div>
                            <div v-if="editingTask !== task.id" class="flex items-center gap-2">
                                <button @click="enterEditMode(task)" class="text-gray-500 hover:text-indigo-400 transition-colors"><Pencil class="h-5 w-5" /></button>
                                <button @click="confirmTaskDeletion(task)" class="text-gray-500 hover:text-red-500 transition-colors"><Trash2 class="h-5 w-5" /></button>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-gray-400">¡Felicidades! No tienes tareas pendientes.</p>
                    </div>

                    <div v-if="completedTasks.length > 0" class="mt-8">
                        <h3 class="text-lg font-bold mb-4 text-gray-500">Tareas Completadas</h3>
                        <div class="space-y-3">
                            <div v-for="task in completedTasks" :key="task.id" class="p-4 flex items-center justify-between border border-gray-800 rounded-lg bg-gray-900/50 opacity-60">
                                <div class="flex-grow flex items-center gap-3">
                                    <input
                                        type="checkbox"
                                        :checked="task.is_completed"
                                        @change="toggleTaskCompletion(task)"
                                        class="h-5 w-5 rounded bg-gray-700 border-gray-600 text-indigo-600"
                                    />
                                    <Circle class="h-4 w-4 flex-shrink-0" :class="priorityClasses[task.priority]" />
                                    <span class="line-through text-gray-500">
                                        {{ task.title }}
                                    </span>
                                    <span v-if="task.due_date" class="text-xs text-gray-500 ml-auto me-4">{{ new Date(task.due_date).toLocaleDateString() }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="confirmTaskDeletion(task)" class="text-gray-500 hover:text-red-500 transition-colors"><Trash2 class="h-5 w-5" /></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="confirmingTaskDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    ¿Estás seguro de que quieres eliminar esta tarea?
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Esta acción no se puede deshacer.
                </p>
                <div class="mt-6 flex justify-end">
                    <button @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-transparent rounded-md hover:bg-gray-700">
                        Cancelar
                    </button>
                    <button @click="deleteTask" class="ms-3 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Eliminar Tarea
                    </button>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>
