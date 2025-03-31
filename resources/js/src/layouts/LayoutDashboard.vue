<script setup>
import { ref, onMounted, onBeforeUnmount, watch,computed } from "vue";
import { useRouter } from "vue-router";
import DashboardHeader from "./DashboardHeader.vue";
import SuspenseFallback from "./SuspenseFallback.vue";
import useUserStore from "../store/useUserStore";
import Data from "./Data.vue"; // Importación de Data.vue
import Breadcrumb from '../components/pagination/Breadcrumb.vue';


import { useBreadcrumb } from "../composables/useBreadcrumb"; // Asegúrate de importar correctamente

const router = useRouter();
const { breadcrumbs } = useBreadcrumb();

const userStore = useUserStore();

console.log("datos usuario: ", userStore);

const asyncLoading = ref(false);
const sidebarOpen = ref(false);
const isLargeScreen = ref(window.innerWidth >= 1280);


const mostrarData = ref(false); // Estado para mostrar u ocultar el componente Data.vue

// Función para alternar la visibilidad de Data.vue
const toggleData = () => {
  mostrarData.value = !mostrarData.value;
};

// Función para cerrar el cuadro desplegable
const closeData = () => {
  mostrarData.value = false;
};

// Evento para detectar clics fuera del cuadro
const handleClickOutside = (event) => {
  const dropdown = document.querySelector("#data-dropdown");
  if (dropdown && !dropdown.contains(event.target)) {
    closeData();
  }
};

// Evento para ajustar el tamaño de la pantalla
const handleResize = () => {
  isLargeScreen.value = window.innerWidth >= 1280;
};

// Registramos los eventos al montar el componente
onMounted(() => {
  window.addEventListener("resize", handleResize);
  window.addEventListener("click", handleClickOutside);
});

// Eliminamos los eventos al desmontar el componente
onBeforeUnmount(() => {
  window.removeEventListener("resize", handleResize);
  window.removeEventListener("click", handleClickOutside);
});

// Cerrar sidebar automáticamente al seleccionar una herramienta en móviles
watch(() => router.fullPath, () => {
  if (!isLargeScreen.value) {
    sidebarOpen.value = false;
  }
});
// Computed para obtener la última ruta válida del breadcrumb
const lastBreadcrumbRoute = computed(() => {
  return breadcrumbs.value.length > 1
    ? breadcrumbs.value[breadcrumbs.value.length - 2].path // Toma la penúltima ruta
    : "/home"; // Ruta por defecto si no hay historial
});
// Función para regresar a la última ruta del breadcrumb
const goBack = () => {
  router.push(lastBreadcrumbRoute.value);
};
</script>

<template>
  <!-- Contenedor general -->
  <div class="flex flex-col lg:flex-row w-full min-h-screen bg-white dark:bg-gray-800">

    <!-- Sidebar Header -->
    <!-- Utiliza w-full en móviles, w-[15%] en pantallas lg y la clase personalizada para resoluciones >=1550px -->
    <header v-if="sidebarOpen || isLargeScreen"
      class="w-full lg:w-[220px] bg-plomoClaro dark:bg-gray-800 h-auto lg:h-screen sticky top-0 shadow-lg z-10 overflow-auto sidebar-responsive">
      <DashboardHeader class="container mx-auto px-4 xl:px-0 gap-4 text-negroClaro dark:text-white" />
    </header>

    <!-- Main Content (sin cambios) -->
    <main class="flex-1 bg-gray-100 dark:bg-gray-900 overflow-auto relative">
      <!-- Botón flotante solo en pantallas menores a lg -->
      <button v-if="!isLargeScreen" @click="sidebarOpen = !sidebarOpen"
        class="fixed top-1/2 -translate-y-1/2 left-0 z-20 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 shadow hover:ring-2 hover:ring-gray-500 dark:hover:ring-gray-300 transition duration-150 rounded-lg p-1 flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
          class="w-2 h-24 text-gray-500 dark:text-gray-300">
          <path stroke-linecap="round" stroke-linejoin="round"
            :d="sidebarOpen ? 'M8.25 19.5L15.75 12 8.25 4.5' : 'M15.75 19.5L8.25 12 15.75 4.5'" />
        </svg>
      </button>

      <!-- Cabecera de INTRANET -->
      <div
        class="flex items-center justify-between p-4 bg-blancoPuro dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600 mx-auto rounded-lg w-[99%] mt-2">
        <div class="text-left rounded-lg">
          <p class="text-lg font-regular text-dark-fondo dark:text-white">INTRANET</p>
          <p class="text-xs font-semibold mt-1 text-dark-surface dark:text-gray-300">CETPRO HUANCANÉ</p>
        </div>
        <div class="flex items-center space-x-4 relative">
          <div class="text-right">
            <p class="text-sm text-dark-fondo dark:text-white">
              {{ userStore.user?.name }} {{ userStore.user?.apellido_paterno }}
            </p>
            <p class="text-xs text-dark-surface dark:text-gray-300">
              {{ userStore.user.roles[0]?.name }}
            </p>
          </div>
          <div
            class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-300 cursor-pointer"
            @click.stop="toggleData">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-8 h-8">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
          </div>
          <div v-if="mostrarData" id="data-dropdown"
            class="absolute top-12 right-4 w-640 bg-transparent dark:bg-gray-700 p-4 z-50 transition-transform transform"
            :class="mostrarData ? 'opacity-100 scale-100' : 'opacity-0 scale-95'">
            <Data />
          </div>
        </div>
      </div>

      <!-- Navegación / botón para regresar -->
      <div
        class="flex items-center justify-start p-4 bg-blancoPuro dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600 mx-auto rounded-lg w-[99%] mt-1">
        <button @click="goBack"
          class="flex items-center justify-center w-8 h-6 rounded-full border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:ring-2 hover:ring-gray-500 dark:hover:ring-gray-300 transition duration-150">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
          </svg>
        </button>
        <p class="ml-2 bg-gray-200 text-gray-700 px-3 py-1 rounded text-sm dark:bg-gray-700 dark:text-gray-300">
          <Breadcrumb />
        </p>
      </div>

      <!-- Contenedor del contenido principal -->
      <div
        class="container mx-auto max-w-full w-[99%] bg-white rounded-md shadow-lg mt-1 pl-4 pr-4 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600 overflow-auto custom-scrollbar">
        <RouterView v-slot="{ Component }">
          <template v-if="Component">
            <Suspense @pending="asyncLoading = true" @resolve="asyncLoading = false">
              <div class="w-full h-full">
                <component :is="Component" />
              </div>
              <template #fallback>
                <SuspenseFallback />
              </template>
            </Suspense>
          </template>
        </RouterView>
      </div>
    </main>
  </div>
</template>

<style>
@media (min-width: 1550px) {

  /* Para resoluciones mayores o iguales a 1550px, el sidebar se fija a 232px (15% de 1550) */
  .sidebar-responsive {
    width: 280px !important;
  }

  @media (max-width: 1280px) and (min-width: 1024px) {

    /* Para resoluciones entre 1024px y 1280px, el sidebar tendrá 250px */
    .sidebar-responsive {
      width: 220px !important;
    }
  }
}
</style>
