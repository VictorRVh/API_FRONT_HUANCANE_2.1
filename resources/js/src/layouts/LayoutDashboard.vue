<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useRouter, useRoute } from "vue-router";
import DashboardHeader from "./DashboardHeader.vue";
import SuspenseFallback from "./SuspenseFallback.vue";
import useUserStore from "../store/useUserStore";

const userStore = useUserStore();
const asyncLoading = ref(false);
const sidebarOpen = ref(false);
const isLargeScreen = ref(window.innerWidth >= 1280);
const route = useRoute();
const router = useRouter();
const previousRoute = ref(null);

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

router.afterEach((to, from) => {
  previousRoute.value = from.fullPath;
});

const goBack = () => {
  router.back();
};
</script>

<template>
  <div class="flex flex-col lg:flex-row w-full h-screen bg-white dark:bg-gray-800 overflow-hidden">
    <!-- Sidebar Header -->
    <header
      v-if="sidebarOpen || isLargeScreen"
      class="w-full lg:w-[15%] bg-plomoClaro dark:bg-gray-800 h-full sticky top-0 shadow-lg z-10"
    >
      <DashboardHeader class="container mx-auto px-4 xl:px-0 gap-4 text-negroClaro dark:text-white" />
    </header>

    <!-- Main Content -->
    <main class="flex-1 h-full bg-gray-100 dark:bg-gray-900 overflow-hidden relative">
      <!-- Botón flotante solo en pantallas <= 1280px -->
      <button
        v-if="!isLargeScreen"
        @click="toggleSidebar"
        class="fixed top-1/2 -translate-y-1/2 left-0 z-20 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 shadow hover:ring-2 hover:ring-gray-500 dark:hover:ring-gray-300 transition duration-150 rounded-lg p-1 flex items-center justify-center"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="2"
          stroke="currentColor"
          class="w-2 h-24 text-gray-500 dark:text-gray-300"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            :d="sidebarOpen ? 'M8.25 19.5L15.75 12 8.25 4.5' : 'M15.75 19.5L8.25 12 15.75 4.5'"
          />
        </svg>
      </button>

      <!-- Cabecera de INTRANET -->
      <div class="flex items-center justify-between p-4 bg-blancoPuro dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600 mx-auto rounded-lg w-[99%] mt-2">
        <div class="text-left rounded-lg">
          <p class="text-lg font-regular text-dark-fondo dark:text-white">INTRANET</p>
          <p class="text-xs font-semibold mt-1 text-dark-surface dark:text-gray-300">CEPRO HUANCANÉ</p>
        </div>
        <div class="flex items-center space-x-4">
          <div class="text-right">
            <p class="text-sm text-dark-fondo dark:text-white">{{ userStore.user?.name }} {{ userStore.user?.apellido_paterno }}</p>
            <p class="text-xs text-dark-surface dark:text-gray-300">{{ userStore.user.roles[0]?.name }}</p>
          </div>
          <!-- Ícono de usuario -->
          <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-300">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-8 h-8"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
              />
            </svg>
          </div>
        </div>
      </div>

      <!-- Botón retroceso -->
      <div class="flex items-center justify-start p-4 bg-blancoPuro dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600 mx-auto rounded-lg w-[99%] mt-1">
        <button
          @click="goBack"
          class="flex items-center justify-center w-8 h-6 rounded-full border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:ring-2 hover:ring-gray-500 dark:hover:ring-gray-300 transition duration-150"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="w-5 h-5"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"
            />
          </svg>
        </button>
        <p class="ml-2 bg-gray-200 text-gray-700 px-3 py-1 rounded text-sm dark:bg-gray-700 dark:text-gray-300">
          {{ route.fullPath }}
        </p>
      </div>

      <!-- Contenido principal con scroll delgado -->
      <div
        class="container mx-auto max-w-full w-[99%] h-[calc(90%-5rem)] bg-white rounded-md shadow-lg mt-1 pl-4 pr-4  dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600 overflow-auto custom-scrollbar"
      >
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
/* Scrollbar personalizado */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #c1c1c1;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
</style>
