<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import { Trash2, Pencil, Check, X, Circle, MessageSquare, Paperclip, Download, File, AlertTriangle, Lock, Plus, GripVertical, History } from 'lucide-vue-next';
import Sortable from 'sortablejs';

const props = defineProps({
    project: Object,
    filters: Object,
});

// --- State Management ---
const filterPriority = ref(props.filters.filter_priority || '');
const filterStatus = ref(props.filters.filter_status || '');
const sortBy = ref(props.filters.sort || '');

const taskListEl = ref(null);
const editingTask = ref(null);

const confirmingTaskDeletion = ref(false);
const taskToDelete = ref(null);

const confirmingDependencyDeletion = ref(false);
const dependencyToDelete = ref({ task: null, prerequisite: null });


// --- Forms ---
const taskForm = useForm({ title: '', due_date: null, priority: 'medium' });
const editTaskForm = useForm({ title: '', due_date: null, priority: 'medium' });
const commentForms = ref({});
const attachmentForms = ref({});
const dependencyForms = ref({});


// --- Watcher for Filters ---
watch([filterPriority, filterStatus, sortBy], ([newPriority, newStatus, newSort]) => {
    router.get(`/projects/${props.project.id}`, {
        filter_priority: newPriority,
        filter_status: newStatus,
        sort: newSort,
    }, { preserveState: true, preserveScroll: true, replace: true });
});


// --- Lifecycle Hooks ---
onMounted(() => {
    if (taskListEl.value) {
        Sortable.create(taskListEl.value, {
            animation: 150,
            ghostClass: 'bg-indigo-900/50',
            handle: '.handle',
            onEnd: (event) => {
                const newOrderedIds = Array.from(event.to.children).map(el => el.dataset.taskId);
                router.post('/tasks/reorder', {
                    tasks: newOrderedIds
                }, {
                    preserveScroll: true,
                });
            }
        });
    }
});


// --- Computed Properties ---
const incompleteTasks = computed(() => props.project.tasks.filter(task => !task.is_completed));
const completedTasks = computed(() => props.project.tasks.filter(task => task.is_completed));
const priorityClasses = { low: 'text-gray-400', medium: 'text-yellow-400', high: 'text-red-500' };

const availablePrerequisites = (currentTask) => {
    const prerequisiteIds = currentTask.prerequisites.map(p => p.id);
    const dependentIds = currentTask.dependents ? currentTask.dependents.map(d => d.id) : [];
    return props.project.tasks.filter(task =>
        task.id !== currentTask.id &&
        !prerequisiteIds.includes(task.id) &&
        !dependentIds.includes(task.id) &&
        !task.is_completed
    );
};


// --- Methods ---
const initAndGetCommentForm = (taskId) => {
    if (!commentForms.value[taskId]) commentForms.value[taskId] = useForm({ body: '' });
    return commentForms.value[taskId];
};
const initAndGetAttachmentForm = (taskId) => {
    if (!attachmentForms.value[taskId]) attachmentForms.value[taskId] = useForm({ file: null });
    return attachmentForms.value[taskId];
};
const initAndGetDependencyForm = (taskId) => {
    if (!dependencyForms.value[taskId]) dependencyForms.value[taskId] = useForm({ prerequisite_task_id: '' });
    return dependencyForms.value[taskId];
};

const submitTask = () => taskForm.post(`/projects/${props.project.id}/tasks`, { onSuccess: () => taskForm.reset(), preserveScroll: true });
const submitComment = (taskId) => initAndGetCommentForm(taskId).post(`/tasks/${taskId}/comments`, { preserveScroll: true, onSuccess: () => initAndGetCommentForm(taskId).reset() });
const submitAttachment = (taskId) => initAndGetAttachmentForm(taskId).post(`/tasks/${taskId}/attachments`, { preserveScroll: true, onSuccess: () => initAndGetAttachmentForm(taskId).reset() });
const addDependency = (taskId) => {
    const form = initAndGetDependencyForm(taskId);
    if (!form.prerequisite_task_id) return;
    form.post(`/tasks/${taskId}/dependencies`, { preserveScroll: true, onSuccess: () => form.reset() });
};

const deleteAttachment = (attachmentId) => {
    if (confirm('¿Estás seguro de que quieres eliminar este archivo?')) {
        router.delete(`/attachments/${attachmentId}`, { preserveScroll: true });
    }
};

const confirmDependencyDeletion = (taskId, prerequisiteId) => {
    dependencyToDelete.value = { task: taskId, prerequisite: prerequisiteId };
    confirmingDependencyDeletion.value = true;
};

const deleteDependency = () => {
    const { task, prerequisite } = dependencyToDelete.value;
    router.delete(`/tasks/${task}/dependencies/${prerequisite}`, {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const canCompleteTask = (task) => {
    if (!task.prerequisites || task.prerequisites.length === 0) {
        return true;
    }
    return task.prerequisites.every(p => p.is_completed);
};

const toggleTaskCompletion = (task) => {
    if (!canCompleteTask(task)) {
        alert('No se puede completar esta tarea. Primero debe completar sus dependencias.');
        return;
    }
    router.patch(`/tasks/${task.id}`, { is_completed: !task.is_completed }, { preserveScroll: true });
};

const enterEditMode = (task) => {
    editingTask.value = task.id;
    editTaskForm.title = task.title;
    editTaskForm.due_date = task.due_date ? task.due_date.substring(0, 10) : null;
    editTaskForm.priority = task.priority;
};
const cancelEditMode = () => editingTask.value = null;
const submitTaskUpdate = (task) => editTaskForm.patch(`/tasks/${task.id}`, { onSuccess: () => cancelEditMode(), preserveScroll: true });

const confirmTaskDeletion = (task) => {
    taskToDelete.value = task;
    confirmingTaskDeletion.value = true;
};
const deleteTask = () => router.delete(`/tasks/${taskToDelete.value.id}`, { onSuccess: () => closeModal(), preserveScroll: true });

const closeModal = () => {
    confirmingTaskDeletion.value = false;
    taskToDelete.value = null;
    confirmingDependencyDeletion.value = false;
    dependencyToDelete.value = { task: null, prerequisite: null };
};

const formatDate = (dateString) => new Date(dateString).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-8">
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
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-100">Tareas Pendientes</h3>
                            <div class="flex items-center gap-4">
                                <select v-model="filterPriority" class="text-sm bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Filtrar Prioridad</option>
                                    <option value="high">Alta</option>
                                    <option value="medium">Media</option>
                                    <option value="low">Baja</option>
                                </select>
                                <select v-model="filterStatus" class="text-sm bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Filtrar Estado</option>
                                    <option value="pending">Pendiente</option>
                                    <option value="overdue">Vencida</option>
                                </select>
                                <select v-model="sortBy" class="text-sm bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Ordenar Por</option>
                                    <option value="priority">Prioridad</option>
                                    <option value="due_date">Fecha Límite</option>
                                </select>
                            </div>
                        </div>

                        <div v-if="incompleteTasks.length > 0" class="space-y-3" ref="taskListEl">
                           <div v-for="task in incompleteTasks" :key="task.id" :data-task-id="task.id" class="p-4 border rounded-lg bg-gray-900/50 transition-colors" :class="{'border-red-500/40': task.status === 'overdue', 'border-gray-700': task.status !== 'overdue'}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-grow flex items-center gap-3">
                                        <GripVertical class="handle cursor-grab text-gray-600 hover:text-gray-400"/>
                                        <input type="checkbox" :checked="task.is_completed" @change="toggleTaskCompletion(task)" :disabled="!canCompleteTask(task)" class="h-5 w-5 rounded bg-gray-700 border-gray-600 text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"/>
                                        <Lock v-if="!canCompleteTask(task)" class="h-4 w-4 text-amber-500 flex-shrink-0" title="Esta tarea tiene prerrequisitos sin completar" />
                                        <div v-if="editingTask === task.id" class="flex-grow flex items-center gap-2">
                                            <input type="text" v-model="editTaskForm.title" class="flex-grow bg-gray-700 border-gray-600 rounded-md text-white" @keyup.enter="submitTaskUpdate(task)" @keyup.esc="cancelEditMode" />
                                            <button @click="submitTaskUpdate(task)" class="text-green-500 hover:text-green-400"><Check class="h-5 w-5" /></button>
                                            <button @click="cancelEditMode" class="text-gray-500 hover:text-gray-400"><X class="h-5 w-5" /></button>
                                        </div>
                                        <div v-else class="flex items-center gap-3">
                                            <Circle class="h-4 w-4 flex-shrink-0" :class="priorityClasses[task.priority]" />
                                            <span class="text-gray-200" :class="{ 'text-red-400': task.status === 'overdue' }">{{ task.title }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4 ml-auto">
                                        <span v-if="task.status === 'overdue'" class="text-xs text-red-400 font-semibold flex items-center gap-1">
                                            <AlertTriangle class="h-4 w-4" /> VENCIDA
                                        </span>
                                        <span v-if="task.due_date" class="text-xs text-gray-500">{{ new Date(task.due_date).toLocaleDateString() }}</span>
                                        <div v-if="editingTask !== task.id" class="flex items-center gap-2">
                                            <button @click="enterEditMode(task)" class="text-gray-500 hover:text-indigo-400 transition-colors"><Pencil class="h-5 w-5" /></button>
                                            <button @click="confirmTaskDeletion(task)" class="text-gray-500 hover:text-red-500 transition-colors"><Trash2 class="h-5 w-5" /></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pl-12 border-t border-gray-800 pt-4 space-y-4">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-400 mb-2">Dependencias</h4>
                                        <div class="space-y-1 mb-2" v-if="task.prerequisites && task.prerequisites.length > 0">
                                            <div v-for="p in task.prerequisites" :key="p.id" class="flex items-center justify-between text-xs bg-gray-800 px-2 py-1 rounded">
                                                <span :class="{'line-through text-gray-500': p.is_completed}">{{ p.title }}</span>
                                                <button @click="confirmDependencyDeletion(task.id, p.id)" class="text-gray-500 hover:text-red-500">
                                                    <X class="h-3 w-3" />
                                                </button>
                                            </div>
                                        </div>
                                        <form @submit.prevent="addDependency(task.id)" class="flex gap-2">
                                            <select v-model="initAndGetDependencyForm(task.id).prerequisite_task_id" class="w-full text-xs bg-gray-800 border-gray-700 text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">Añadir dependencia...</option>
                                                <option v-for="p in availablePrerequisites(task)" :key="p.id" :value="p.id">{{ p.title }}</option>
                                            </select>
                                            <button type="submit" class="p-1.5 bg-indigo-600 rounded-md text-white hover:bg-indigo-700 disabled:opacity-50" :disabled="!initAndGetDependencyForm(task.id).prerequisite_task_id">
                                                <Plus class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-400 mb-2">Archivos Adjuntos</h4>
                                        <div class="space-y-2" v-if="task.attachments && task.attachments.length > 0">
                                            <div v-for="attachment in task.attachments" :key="attachment.id" class="flex items-center justify-between text-sm bg-gray-800 p-2 rounded-md">
                                                <a :href="`/attachments/${attachment.id}`" class="flex items-center gap-2 text-indigo-400 hover:underline">
                                                    <File class="h-4 w-4" />
                                                    <span>{{ attachment.original_name }}</span>
                                                </a>
                                                <button @click="deleteAttachment(attachment.id)" class="text-gray-500 hover:text-red-500">
                                                    <Trash2 class="h-4 w-4"/>
                                                </button>
                                            </div>
                                        </div>
                                        <form @submit.prevent="submitAttachment(task.id)" class="mt-2">
                                            <input type="file" @input="initAndGetAttachmentForm(task.id).file = $event.target.files[0]" class="block w-full text-xs text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white hover:file:bg-gray-700"/>
                                            <progress v-if="initAndGetAttachmentForm(task.id).progress" :value="initAndGetAttachmentForm(task.id).progress.percentage" max="100" class="w-full mt-1 h-1"></progress>
                                            <button v-if="initAndGetAttachmentForm(task.id).file" type="submit" :disabled="initAndGetAttachmentForm(task.id).processing" class="mt-2 w-full text-xs bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-1 px-2 rounded disabled:opacity-50">Subir Archivo</button>
                                        </form>
                                    </div>
                                    <div class="border-t border-gray-700/50 pt-4">
                                        <h4 class="text-sm font-semibold text-gray-400 mb-2">Comentarios</h4>
                                        <div class="space-y-3" v-if="task.comments && task.comments.length > 0">
                                            <div v-for="comment in task.comments" :key="comment.id" class="text-sm text-gray-400 flex items-start gap-2">
                                                <div class="font-semibold text-gray-300">{{ comment.user.name }}:</div>
                                                <div class="flex-1">{{ comment.body }}</div>
                                                <div class="text-xs text-gray-500 whitespace-nowrap">{{ formatDate(comment.created_at) }}</div>
                                            </div>
                                        </div>
                                        <form @submit.prevent="submitComment(task.id)" class="mt-4 flex gap-2">
                                            <input type="text" v-model="initAndGetCommentForm(task.id).body" class="w-full bg-gray-800 border-gray-700 text-sm text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Añadir un comentario..." required/>
                                            <button type="submit" :disabled="initAndGetCommentForm(task.id).processing" class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50"><MessageSquare class="h-4 w-4" /></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <p class="text-gray-400">No hay tareas pendientes que coincidan con tus filtros.</p>
                        </div>
                        <div v-if="completedTasks.length > 0" class="mt-8">
                            <h3 class="text-lg font-bold mb-4 text-gray-500">Tareas Completadas</h3>
                            <div class="space-y-3">
                                <div v-for="task in completedTasks" :key="task.id" class="p-4 flex items-center justify-between border border-gray-800 rounded-lg bg-gray-900/50 opacity-60">
                                    <div class="flex-grow flex items-center gap-3">
                                        <input type="checkbox" :checked="task.is_completed" @change="toggleTaskCompletion(task)" class="h-5 w-5 rounded bg-gray-700 border-gray-600 text-indigo-600"/>
                                        <Circle class="h-4 w-4 flex-shrink-0" :class="priorityClasses[task.priority]" />
                                        <span class="line-through text-gray-500">{{ task.title }}</span>
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

                <div class="lg:col-span-1">
                     <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-xl font-bold mb-4 text-gray-100 flex items-center gap-2"><History class="h-6 w-6"/> Actividad del Proyecto</h3>
                        <ul class="space-y-4">
                            <li v-for="activity in project.activities" :key="activity.id" class="flex items-start gap-3">
                                <img class="h-8 w-8 rounded-full object-cover mt-1" :src="activity.user.avatar_url" :alt="activity.user.name">
                                <div class="flex-1 text-sm">
                                    <p class="text-gray-300">
                                        <span class="font-bold">{{ activity.user.name }}</span>
                                        <span v-if="activity.description === 'created_project'"> creó el proyecto.</span>
                                        <span v-else-if="activity.description === 'created_task'"> creó la tarea <span class="font-semibold text-indigo-400">"{{ activity.subject.title }}"</span>.</span>
                                        <span v-else-if="activity.description === 'completed_task'"> completó la tarea <span class="font-semibold text-green-400">"{{ activity.subject.title }}"</span>.</span>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ formatDate(activity.created_at) }}
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="confirmingTaskDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">¿Estás seguro de que quieres eliminar esta tarea?</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Esta acción no se puede deshacer.</p>
                <div class="mt-6 flex justify-end">
                    <button @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-transparent rounded-md hover:bg-gray-700">Cancelar</button>
                    <button @click="deleteTask" class="ms-3 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Eliminar Tarea</button>
                </div>
            </div>
        </Modal>

        <Modal :show="confirmingDependencyDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">¿Eliminar esta dependencia?</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">La tarea ya no necesitará que la otra sea completada primero. ¿Estás seguro?</p>
                <div class="mt-6 flex justify-end">
                    <button @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-transparent rounded-md hover:bg-gray-700">Cancelar</button>
                    <button @click="deleteDependency" class="ms-3 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Eliminar</button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
