<template>
    <div class="bg-plomoClaro min-h-screen rounded-lg">
      <!-- Header -->
      <header class="bg-granate py-4 rounded-lg mt-4 ">
        <div class="max-w-screen-xl mx-auto flex items-center justify-between px-4">
          <h1 class="text-blancoPuro text-2xl font-bold uppercase">CETPRO Huancané</h1>
        </div>
      </header>

      <!-- Hero Section -->
      <section class="bg-white py-16 rounded-lg shadow-md mx-6 mt-8 relative">
        <div class="max-w-screen-xl mx-auto flex items-center justify-center text-center px-6 dark:bg-dark">
          <div>
            <h2 class="text-granate font-black text-4xl mb-4">Bienvenido al Sistema de Gestión Académica</h2>
            <p class="text-negroClaro text-lg max-w-2xl mb-8">
              Gestiona las especialidades, docentes, estudiantes y procesos académicos de manera eficiente y organizada.
            </p>
            <button class="bg-granate text-blancoPuro px-8 py-3 text-lg font-bold uppercase rounded-full hover:bg-opacity-90">
              Iniciar Recorrido
            </button>
          </div>
        </div>
      </section>

      <!-- Features Section -->
      <section class="bg-plomoClaro py-16 rounded-lg shadow-md mx-2">
        <div class="max-w-screen-xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 px-6">
          <!-- Feature 1 -->
          <div class="bg-blancoPuro p-6 rounded-lg shadow-md text-center">
            <h3 class="text-granate font-bold text-2xl mb-4">Gestión de Especialidades</h3>
            <p class="text-negroClaro text-base">
              Administra las especialidades disponibles en el CETPRO con un sistema optimizado y seguro.
            </p>
          </div>
          <!-- Feature 2 -->
          <div class="bg-blancoPuro p-6 rounded-lg shadow-md text-center">
            <h3 class="text-granate font-bold text-2xl mb-4">Registro de Estudiantes</h3>
            <p class="text-negroClaro text-base">
              Mantén un registro completo y actualizado de los estudiantes matriculados.
            </p>
          </div>
          <!-- Feature 3 -->
          <div class="bg-blancoPuro p-6 rounded-lg shadow-md text-center">
            <h3 class="text-granate font-bold text-2xl mb-4">Automatización de Procesos</h3>
            <p class="text-negroClaro text-base">
              Optimiza procesos administrativos y académicos con herramientas automatizadas.
            </p>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <footer class="bg-granate py-8 rounded-t-lg mt-8">
        <div class="max-w-screen-xl mx-auto flex flex-col md:flex-row justify-between items-center text-blancoPuro px-6">
          <p class="text-sm text-center md:text-left">&copy; 2024 CETPRO Huancané. Todos los derechos reservados.</p>
          <ul class="flex space-x-6 mt-4 md:mt-0">
            <li><a href="#" class="hover:underline">Términos de Servicio</a></li>
            <li><a href="#" class="hover:underline">Políticas de Privacidad</a></li>
          </ul>
        </div>
      </footer>
    </div>
  </template>

  <script>
  export default {
    name: "HomePage",
  };
  </script>

  <style scoped>
  </style>

