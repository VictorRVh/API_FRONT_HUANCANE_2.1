<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import DashboardHeader from "./DashboardHeader.vue";
import PageLoader from "./PageLoader.vue";
import SuspenseFallback from "./SuspenseFallback.vue";
import useUserStore from "../store/useUserStore";

const userStore = useUserStore();
const asyncLoading = ref(false);
const sidebarOpen = ref(false);
const isLargeScreen = ref(window.innerWidth >= 1280);

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};

const handleResize = () => {
  isLargeScreen.value = window.innerWidth >= 1280;
};

onMounted(() => {
  window.addEventListener("resize", handleResize);
});

onBeforeUnmount(() => {
  window.removeEventListener("resize", handleResize);
});
</script>

<template>
  <div class="flex flex-col lg:flex-row w-full h-screen bg-white dark:bg-gray-800 overflow-hidden">
    <!-- Botón para abrir el sidebar en pantallas pequeñas -->
    <button
      v-if="!isLargeScreen && !sidebarOpen"
      @click="toggleSidebar"
      class="absolute top-20 left-4 p-2 z-20 bg-granate text-white hover:bg-opacity-80 transition-all duration-300 rounded-full shadow"
    >
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
        <path fill-rule="evenodd" d="M3 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 5.25Zm0 4.5A.75.75 0 0 1 3.75 9h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 9.75Zm0 4.5a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Zm0 4.5a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
      </svg>
    </button>

    <!-- Sidebar Header -->
    <header
      v-if="sidebarOpen || isLargeScreen"
      class="w-full lg:w-[15%] bg-plomoClaro dark:bg-gray-800 h-full sticky top-0 shadow-lg z-10"
    >
      <DashboardHeader class="container mx-auto px-4 xl:px-0 gap-4 text-negroClaro dark:text-white" />

      <!-- Botón para cerrar el sidebar en pantallas pequeñas -->
      <button
        v-if="!isLargeScreen && sidebarOpen"
        @click="toggleSidebar"
        class="absolute top-4 right-4 p-2 bg-granate text-white rounded-full shadow hover:bg-opacity-80 transition-all duration-300"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>
      </button>
    </header>

    <!-- Main Content -->
    <main class="flex-1 h-full bg-gray-100 dark:bg-gray-900 overflow-hidden">
      <div class="flex items-center justify-between p-4 bg-blancoPuro dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600 mx-auto rounded-lg w-[95%] max-w-[1240px]">
        <!-- Left Section -->
        <div class="text-left rounded-lg p-2">
          <p class="text-lg font-regular text-dark-fondo dark:text-white">INTRANET</p>
          <p class="text-xs font-semibold mt-1 text-dark-surface dark:text-gray-300">CEPRO HUANCANÉ</p>
        </div>

        <!-- Right Section -->
        <div class="flex items-center space-x-4 p-2">
          <div class="text-right">
            <p class="text-sm text-dark-fondo dark:text-white">{{ userStore.user?.name }} {{ userStore.user?.apellido_paterno }}</p>
            <p class="text-xs text-dark-surface dark:text-gray-300">{{ userStore.user.roles[0]?.name }}</p>
          </div>
          <img src="/path/to/image.jpg" alt="Rol Icono" class="w-8 h-8 rounded-full object-cover" />
        </div>
      </div>

      <PageLoader :loading="asyncLoading" />

      <!-- Contenido principal sin scroll -->
      <div class="container mx-auto max-w-full w-[95%] h-full max-h-full bg-white rounded-md shadow-lg mt-2 p-4 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600 overflow-auto">
        <!-- Aquí van los botones -->
        <div class="flex justify-between items-center mb-4">
          <p>Aquí van los botones</p>
          <button class="btn-primary">Botón 1</button>
          <button class="btn-secondary">Botón 2</button>
        </div>

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
