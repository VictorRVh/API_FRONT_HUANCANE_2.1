<script setup>
import { computed, inject } from 'vue';
import { DocumentArrowDownIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  name: {
    type: String,
    default: 'Botón',
  },
  color: {
    type: String,
    default: '#921733', // Color base en modo claro
  },
});

// Inyectamos el tema desde la configuración global (asegúrate de proveer "theme" en tu app)
const { isDarkMode } = inject('theme');

// Computamos las variables CSS según el modo
const buttonStyle = computed(() => {
  if (!isDarkMode.value) {
    return {
      '--btn-bg': props.color + '22',       // Fondo con opacidad
      '--btn-border': props.color,            // Borde del mismo color
      '--btn-text': props.color,              // Texto e ícono
      '--btn-hover-bg': props.color + '33',    // Hover: fondo un poco más opaco
      '--btn-hover-border': props.color,       // Hover: mismo borde
    };
  } else {
    // Colores en modo oscuro (puedes ajustar estos valores a tu preferencia)
    return {
      '--btn-bg': '#374151',          // Fondo oscuro
      '--btn-border': '#4b5563',       // Borde oscuro
      '--btn-text': '#f3f4f6',         // Texto claro en modo oscuro
      '--btn-hover-bg': '#4b5563',     // Hover: fondo un poco más claro
      '--btn-hover-border': '#6b7280', // Hover: borde más claro
    };
  }
});
</script>

<template>
  <button
    class="w-[135px] h-[70px] flex flex-col items-center justify-center gap-1 rounded-md px-4 py-2 border transition-all duration-300 button"
    :style="buttonStyle"
  >
    <!-- Ícono PDF -->
    <DocumentArrowDownIcon class="w-10 h-10" style="color: var(--btn-text)" />

    <!-- Texto -->
    <span class="text-xs font-semibold text-center" style="color: var(--btn-text)">
      {{ props.name }}
    </span>
  </button>
</template>

<style scoped>
.button {
  background-color: var(--btn-bg);
  border-color: var(--btn-border);
  color: var(--btn-text);
}

.button:hover {
  background-color: var(--btn-hover-bg);
  border-color: var(--btn-hover-border);
}
</style>
