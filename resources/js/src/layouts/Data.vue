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
    userStore.setUser(null);
    router.push({ name: "login" });
  }
};

// Copiar correo al portapapeles
const copyEmail = async () => {
  try {
    await navigator.clipboard.writeText(userStore.user?.email);
    alert("Correo copiado al portapapeles");
  } catch (err) {
    console.error("Error al copiar:", err);
  }
};
</script>

<template>
  <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg w-[450px] border border-gray-300 dark:border-gray-700 flex flex-col items-center">
    <!-- Avatar con icono de edición -->
    <div class="relative w-28 h-28 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 cursor-pointer hover:ring-2 hover:ring-gray-400 dark:hover:ring-gray-500 transition-all" @click="goToEditProfile">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-600 dark:text-gray-300">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
      </svg>
      <div class="absolute bottom-0 right-0 bg-white dark:bg-gray-800 p-1 rounded-lg shadow-md hover:bg-gray-300 dark:hover:bg-gray-700 transition-all">
        <span class="text-xs text-gray-500 dark:text-gray-400">Editar</span>
      </div>
    </div>

    <!-- Nombre del usuario -->
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 text-center">
      {{ userStore.user?.name }}
    </h3>

    <!-- Rol -->
    <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
      <span class="font-semibold">Rol:</span> {{ userStore.user.roles[0]?.name }}
    </p>

    <!-- Correo con efecto de copiar -->
    <div class="relative group cursor-pointer" @click="copyEmail">
      <p class="text-sm text-blue-600 dark:text-blue-400 font-medium hover:underline">
        {{ userStore.user?.email }}
      </p>
      <span class="absolute left-0 top-6 text-xs text-gray-500 dark:text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">Copiar</span>
    </div>

    <!-- Controles de tema y cerrar sesión -->
    <div class="flex justify-between items-center w-full mt-4 border-t pt-3 border-gray-300 dark:border-gray-600">
      <button class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors" @click="toggleDarkMode">
        <svg v-if="!isDarkMode" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-400">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
        </svg>
      </button>
      <button @click="onLogout" class="text-white bg-granate hover:bg-granate-dark text-sm px-4 py-2 rounded-lg transition-colors font-medium">
        Cerrar Sesión
      </button>
    </div>
  </div>
</template>
