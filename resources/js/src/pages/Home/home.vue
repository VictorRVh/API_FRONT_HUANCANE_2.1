<template>
  <div class="flex flex-col min-h-screen w-full bg-brancoPuro dark:bg-gray-800 font-inter">
    <!-- Área principal: centrada, desplazada 150px hacia arriba -->
    <div
      class="flex-grow flex flex-col justify-center items-center px-6 py-10 bg-white dark:bg-gray-800"
      style="transform: translateY(-150px);"
    >
      <h1 
        class="text-5xl font-bold text-granate dark:text-granateLight transition-opacity duration-1000 ease-in-out opacity-0"
        :class="{ 'opacity-100': showTitle }"
      >
        Bienvenido
      </h1>
      <h3 
        class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mt-2 transition-opacity duration-1000 ease-in-out opacity-0"
        :class="{ 'opacity-100': showSubtitle }"
      >
        Sistema de Gestión Académica
      </h3>
      <h5 
        class="text-gray-600 dark:text-gray-400 text-base mt-3 transition-opacity duration-1000 ease-in-out opacity-0"
        :class="{ 'opacity-100': showText }"
      >
        Explora las opciones y funcionalidades disponibles en nuestro sistema.
      </h5>
    </div>

    <!-- Footer: desplazado 150px hacia arriba -->
    <footer
      class="w-full border-t border-gray-400 dark:border-gray-600 py-2"
      style="transform: translateY(-180px);"
    >
      <div class="flex justify-between items-center px-2 text-gray-700 dark:text-gray-300 text-sm font-medium">
        <p>&copy; 2024 CETPRO Huancané</p>
        <div class="flex space-x-2">
          <a 
            href="/terminos" 
            target="_blank" 
            rel="noopener noreferrer"
            class="hover:text-gray-900 dark:hover:text-white hover:scale-105 transition-all duration-300"
          >
            Términos y Condiciones
          </a>
          <span>|</span>
          <a 
            href="/politicas" 
            target="_blank" 
            rel="noopener noreferrer"
            class="hover:text-gray-900 dark:hover:text-white hover:scale-105 transition-all duration-300"
          >
            Política de Privacidad
          </a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const showTitle = ref(false);
const showSubtitle = ref(false);
const showText = ref(false);

onMounted(() => {
  setTimeout(() => { showTitle.value = true; }, 200);
  setTimeout(() => { showSubtitle.value = true; }, 600);
  setTimeout(() => { showText.value = true; }, 1000);
});
</script>
