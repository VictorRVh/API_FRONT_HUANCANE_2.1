<script setup>
import { inject } from "vue";
import { useRouter } from "vue-router";
import useUserStore from "../store/useUserStore";
import useHttpRequest from "../composables/useHttpRequest";

const userStore = useUserStore();
const router = useRouter();
const { index: logout } = useHttpRequest("/logout");

console.log('usuario logeado', userStore.user.id)

// Inyectamos el tema desde la configuración global
const { isDarkMode, updateDarkMode } = inject("theme");

// Alternar modo oscuro
const toggleDarkMode = () => {
  updateDarkMode(!isDarkMode.value);
  document.documentElement.classList.toggle("dark", isDarkMode.value);
};

const goToEditProfile = () => {
  router.push({
    name: "datopersonal",
    params: { id: userStore.user.id },
  });
};


// Cerrar sesión
const onLogout = async () => {
  const isLoggedOut = await logout();
  if (isLoggedOut) {
    userStore.setUser(null); // Limpia el usuario del store
    router.push({ name: "login" });
  }
};
</script>

<template>
  <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow-md w-64 border border-gray-300 dark:border-gray-600">
    <div class="mb-4 border-b pb-2 border-gray-300 dark:border-gray-500">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Información del Usuario</h3>
    </div>

    <div class="mb-4 space-y-2">
      <p class="text-sm text-gray-600 dark:text-gray-300"><span class="font-bold">Nombres:</span> {{ userStore.user?.name }}</p>
      <p class="text-sm text-gray-600 dark:text-gray-300"><span class="font-bold">Apellidos:</span> {{ userStore.user?.apellido_paterno }} {{ userStore.user?.apellido_materno }}</p>
      <p class="text-sm text-gray-600 dark:text-gray-300"><span class="font-bold">Rol:</span> {{ userStore.user.roles[0]?.name }}</p>
      <p class="text-sm text-gray-600 dark:text-gray-300"><span class="font-bold">Cuenta:</span> {{ userStore.user?.email }}</p>
    </div>

    <div class="mb-6 flex items-center border-t pt-3 border-gray-300 dark:border-gray-500">
      <button @click="goToEditProfile" class="flex items-center space-x-2 text-granate hover:underline dark:text-granateLigth">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
        </svg>
        <span>Editar Perfil</span>
      </button>
    </div>

    <div class="flex justify-between items-center border-t pt-3 border-gray-300 dark:border-gray-500">
      <button class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors" @click="toggleDarkMode">
        <svg v-if="!isDarkMode" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-400">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
        </svg>
      </button>

      <button @click="onLogout" class="text-dark text-sm px-4 py-2 rounded-lg hover:bg-granate hover:text-white transition-colors dark:text-white dark:hover:bg-granate-dark">
        Cerrar Sesión
      </button>
    </div>
  </div>
</template>
