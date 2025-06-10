<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Trash2, ImageOff } from 'lucide-vue-next';

const props = defineProps({
    project: Object,
});

const form = useForm({
    _method: 'patch',
    title: props.project.title,
    description: props.project.description,
    image: null,
    remove_image: false,
});

const confirmingProjectDeletion = ref(false);
const imagePreview = ref(props.project.image_url ? `/storage/${props.project.image_url}` : null);
const imageInput = ref(null);

const confirmProjectDeletion = () => {
    confirmingProjectDeletion.value = true;
};

const closeModal = () => {
    confirmingProjectDeletion.value = false;
};

const updateImagePreview = () => {
    const file = imageInput.value.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);

    form.image = file;
    form.remove_image = false;
};

const removeImage = () => {
    imagePreview.value = null;
    form.image = null;
    form.remove_image = true;
    if (imageInput.value) {
        imageInput.value.value = '';
    }
};

const submitProjectUpdate = () => {
    form.post(`/projects/${props.project.id}`, {
        onSuccess: () => {
            if (!imagePreview.value) {
                props.project.image_url = null;
            }
            form.reset('image');
        },
    });
};

const deleteProject = () => {
    form.delete(`/projects/${props.project.id}`, {
        onFinish: () => closeModal(),
    });
};
</script>

<template>
    <Head :title="'Editar: ' + project.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <Link href="/dashboard" class="hover:underline">Proyectos</Link>
                <span class="mx-2">/</span>
                <Link :href="`/projects/${project.id}`" class="hover:underline">{{ project.title }}</Link>
                <span class="mx-2">/</span>
                Editar
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Información del Proyecto</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Actualiza el título, la descripción y la imagen de tu proyecto.
                            </p>
                        </header>

                        <form @submit.prevent="submitProjectUpdate" class="mt-6 space-y-6">
                            <div>
                                <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Título</label>
                                <input id="title" type="text" class="mt-1 block w-full bg-gray-900 border-gray-700 dark:text-gray-300 rounded-md" v-model="form.title" required />
                            </div>

                            <div>
                                <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Descripción</label>
                                <textarea id="description" class="mt-1 block w-full bg-gray-900 border-gray-700 dark:text-gray-300 rounded-md" v-model="form.description" rows="4"></textarea>
                            </div>

                             <div>
                                <label for="image" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Imagen de Portada</label>
                                <div class="mt-2 flex items-center gap-x-4">
                                    <div class="w-48 h-28 rounded-md bg-gray-900 flex items-center justify-center overflow-hidden">
                                        <img v-if="imagePreview" :src="imagePreview" alt="Vista previa" class="h-full w-full object-cover">
                                        <ImageOff v-else class="h-12 w-12 text-gray-500" />
                                    </div>
                                    <div class="flex flex-col gap-y-2">
                                        <input
                                            ref="imageInput"
                                            id="image"
                                            type="file"
                                            @input="updateImagePreview"
                                            class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-600 file:text-white hover:file:bg-gray-700"
                                        />
                                        <button v-if="imagePreview" @click="removeImage" type="button" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                            <Trash2 class="h-4 w-4 mr-2" />
                                            Eliminar Imagen
                                        </button>
                                    </div>
                                </div>
                            </div>


                            <div class="flex items-center gap-4">
                                <button :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50">Guardar Cambios</button>
                                <transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Guardado.</p>
                                </transition>
                            </div>
                        </form>
                    </section>
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                     <section class="space-y-6">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Eliminar Proyecto</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Una vez que el proyecto es eliminado, todos sus recursos y datos, incluidas sus tareas, serán borrados permanentemente.
                            </p>
                        </header>
                        <button @click="confirmProjectDeletion" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Eliminar Proyecto</button>
                    </section>
                </div>
            </div>
        </div>

        <Modal :show="confirmingProjectDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    ¿Estás seguro de que quieres eliminar este proyecto?
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Esta acción es irreversible y borrará todas las tareas asociadas.
                </p>
                <div class="mt-6 flex justify-end">
                    <button @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-transparent rounded-md hover:bg-gray-700">
                        Cancelar
                    </button>
                    <button @click="deleteProject" class="ms-3 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 disabled:opacity-50">
                        Sí, Eliminar Proyecto
                    </button>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>
