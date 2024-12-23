<template>
    <div :class="{ 'dark': isDarkMode }" class="flex flex-col min-h-screen bg-blancoPuro dark:bg-gray-800 text-granate dark:text-gray-400 font-inter">
      <!-- Header -->
      <header class="bg-granate py-4 rounded-lg mt-4">
        <div class="max-w-screen-xl mx-auto flex items-center justify-between px-4">
          <h1 class="text-blancoPuro text-2xl font-bold uppercase">CETPRO Huancané</h1>
        </div>
      </header>

      <!-- Hero Section -->
      <section class="bg-white dark:bg-gray-900 py-16 rounded-lg shadow-md mx-6 mt-8 relative">
        <div class="max-w-screen-xl mx-auto flex items-center justify-center text-center px-6">
          <div>
            <h2 class="text-granate dark:text-granateLight font-black text-4xl mb-4">
              Bienvenido al Sistema de Gestión Académica
            </h2>
            <p class="text-negroClaro dark:text-gray-300 text-lg max-w-2xl mb-8">
              Gestiona las especialidades, docentes, estudiantes y procesos académicos de manera eficiente y organizada.
            </p>
            <button class="bg-granate text-blancoPuro px-8 py-3 text-lg font-bold uppercase rounded-full hover:bg-opacity-90">
              Iniciar Recorrido
            </button>
          </div>
        </div>
      </section>

      <!-- Features Section -->
      <section class="bg-plomoClaro dark:bg-gray-700 py-16 rounded-lg shadow-md mx-6 mt-8">
        <div class="max-w-screen-xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 px-6">
          <!-- Feature 1 -->
          <div class="bg-blancoPuro dark:bg-gray-800 p-6 rounded-lg shadow-md text-center">
            <h3 class="text-granate dark:text-granateLight font-bold text-2xl mb-4">Gestión de Especialidades</h3>
            <p class="text-negroClaro dark:text-gray-300 text-base">
              Administra las especialidades disponibles en el CETPRO con un sistema optimizado y seguro.
            </p>
          </div>
          <!-- Feature 2 -->
          <div class="bg-blancoPuro dark:bg-gray-800 p-6 rounded-lg shadow-md text-center">
            <h3 class="text-granate dark:text-granateLight font-bold text-2xl mb-4">Registro de Estudiantes</h3>
            <p class="text-negroClaro dark:text-gray-300 text-base">
              Mantén un registro completo y actualizado de los estudiantes matriculados.
            </p>
          </div>
          <!-- Feature 3 -->
          <div class="bg-blancoPuro dark:bg-gray-800 p-6 rounded-lg shadow-md text-center">
            <h3 class="text-granate dark:text-granateLight font-bold text-2xl mb-4">Automatización de Procesos</h3>
            <p class="text-negroClaro dark:text-gray-300 text-base">
              Optimiza procesos administrativos y académicos con herramientas automatizadas.
            </p>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <footer class="bg-granate dark:bg-gray-800 py-8 rounded-t-lg mt-8">
        <div class="max-w-screen-xl mx-auto flex flex-col md:flex-row justify-between items-center text-blancoPuro px-6">
          <p class="text-sm text-center md:text-left">&copy; 2024 CETPRO Huancané. Todos los derechos reservados.</p>
          <ul class="flex space-x-6 mt-4 md:mt-0">
            <li>
              <a href="#" class="hover:underline text-blancoPuro dark:text-gray-300">Términos de Servicio</a>
            </li>
            <li>|</li>
            <li>
              <a href="#" class="hover:underline text-blancoPuro dark:text-gray-300">Políticas de Privacidad</a>
            </li>
          </ul>
        </div>
      </footer>
    </div>
  </template>

  <script setup>
  import { ref } from 'vue';

  // Estado para controlar si el modo oscuro está activado
  const isDarkMode = ref(false);

  // Función para alternar entre modo claro y oscuro
  function toggleDarkMode() {
    isDarkMode.value = !isDarkMode.value;
  }

  // Puedes usar esta función para cambiar el tema en tu interfaz de usuario.
  </script>
