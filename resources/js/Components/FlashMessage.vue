<script setup>
import { computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircleIcon, XCircleIcon } from 'lucide-vue-next';

const page = usePage();
const message = computed(() => page.props.flash?.success);
const error = computed(() => page.props.flash?.error);

watch(message, (newMessage) => {
    if (newMessage) {
        setTimeout(() => {
            if (page.props.flash) {
                page.props.flash.success = null;
            }
        }, 3000);
    }
}, { immediate: true });

watch(error, (newError) => {
    if (newError) {
        setTimeout(() => {
            if (page.props.flash) {
                page.props.flash.error = null;
            }
        }, 3000);
    }
}, { immediate: true });


const close = () => {
    if (page.props.flash) {
        page.props.flash.success = null;
        page.props.flash.error = null;
    }
};
</script>

<template>
    <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="message || error" class="fixed top-5 right-5 w-full max-w-sm z-50 bg-white dark:bg-gray-800 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <CheckCircleIcon v-if="message" class="h-6 w-6 text-green-400" aria-hidden="true" />
                        <XCircleIcon v-if="error" class="h-6 w-6 text-red-400" aria-hidden="true" />
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ message || error }}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="close" class="bg-white dark:bg-gray-800 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
