<template>
    <li>
        <!-- Botón de página anterior o siguiente -->
        <button
            v-if="icon"
            :disabled="disabled"
            @click="$emit('click')"
            :class="[
                'px-3 py-1 rounded border h-8 flex items-center justify-center',
                {
                    'bg-white text-gray-600 border-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': !disabled,
                    'bg-gray-200 text-gray-400 dark:bg-gray-700 dark:text-gray-500': disabled
                }
            ]"
        >
            <!-- Mostramos el ícono de "prev" o "next" según la propiedad `icon` -->
            <span v-if="icon === 'prev'">«</span>
            <span v-if="icon === 'next'">»</span>
        </button>

        <!-- Botón de número de página -->
        <button
            v-else
            :class="[
                'px-3 py-1 rounded h-8 flex items-center justify-center border',
                {
                    'bg-granate text-white border-granate dark:bg-gray-700 dark:border-gray-700': active,
                    'bg-white text-gray-600 border-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': !active
                }
            ]"
            @click="$emit('click')"
        >
            {{ page }}
        </button>
    </li>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    page: {
        type: Number,
        default: null
    },
    icon: {
        type: String,
        default: null // Puede ser "prev" o "next" para botones de navegación
    },
    active: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['click']);
</script>
