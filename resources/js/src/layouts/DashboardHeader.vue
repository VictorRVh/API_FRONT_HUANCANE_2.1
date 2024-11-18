<script setup>
import { inject, computed } from "vue";
import useHttpRequest from "../composables/useHttpRequest";
import useUserStore from "../store/useUserStore";
import useRoleStore from "../store/useRoleStore";
import usePermissionStore from "../store/usePermissionStore";
import useAppRouter from "../composables/useAppRouter";

// Inyecta el tema y otras propiedades globales
const { isDarkMode, updateDarkMode, windowWidth } = inject("theme");
const { index: logout } = useHttpRequest("/logout");
const { pushToRoute } = useAppRouter();

// Stores
const userStore = useUserStore();
const roleStore = useRoleStore();
const permissionStore = usePermissionStore();

// Obtener permisos del usuario
const userPermissions = computed(
  () => userStore.user?.permissions.map((p) => p.name) || []
);

// Función de logout
const onLogout = async () => {
  const isLoggedOut = await logout();
  if (isLoggedOut) {
    userStore.setUser(null);
    roleStore.roles = [];
    permissionStore.permissions = [];
    await pushToRoute({ name: "login" });
  }
};

// Crear elementos de menú dinámicamente
const menuItems = [
  {
    name: "Home",
    icon: "HomeIcon",
    route: "users",
    permissions: ["users-all", "users-view"],
  },
  {
    name: "Docente",
    icon: "UserIcon",
    route: "docentes",
    permissions: ["teachers-all", "teachers-view"],
    id: 7
  },
  {
    name: "Estudiante",
    icon: "AcademicCapIcon",
    route: "estudiantes",
    permissions: ["students-all", "students-view"],
    id: 8
  },
  {
    name: "PlanFormativo",
    icon: "CalendarIcon",
    route: "plan",
    permissions: ["plan-all", "plan-view"],
    id: 8
  },
  {
    name: "Matricula",
    icon: "BookOpenIcon",
    route: "matriculas",
    permissions: ["enrollmentStudent-all", "enrollmentStudent-view"],
  },
  {
    name: "Especialidad",
    icon: "BuildingOfficeIcon",
    route: "especialidad",
    permissions: ["specialties-all", "specialties-view"],
  },
  {
    name: "Reportes",
    icon: "ChartBarIcon",
    route: "users",
    permissions: ["users-all", "users-view"],
  },
  {
    name: "Certificados",
    icon: "FolderIcon",
    route: "users",
    permissions: ["users-all", "users-view"],
  },
  {
    name: "Users",
    icon: "UsersIcon",
    route: "users",
    permissions: ["users-all", "users-view"],
  },
  {
    name: "Roles",
    icon: "BookmarkIcon",
    route: "roles",
    permissions: ["roles-all", "roles-view"],
  },
  {
    name: "Permissions",
    icon: "BookmarkSquareIcon",
    route: "permissions",
    permissions: ["permissions-all", "permissions-view"],
  },
  {
    name: "Sede",
    icon: "BookmarkSquareIcon",
    route: "sedes",
    permissions: ['places-all', 'places-view'],
  },
  {
    name: "Grupos",
    icon: "UsersIcon",
    route: "grupos",
    permissions: ["groups-all", "groups-view"],
  },
  

];

// Comprobar permisos
const hasPermission = (itemPermissions) =>
  itemPermissions.some((perm) => userPermissions.value.includes(perm));
</script>
<template>
  <div
    class="flex flex-col min-h-screen bg-blancoPuro dark:bg-gray-800 text-granate dark:text-gray-400 font-inter w-12/12 max-w-3xl"
  >
    <!-- Título "HERRAMIENTAS" -->
    <h2 class="text-lg font-semibold text-negroClaro dark:text-gray-400 mt-4 mb-2 pl-4">
      Menu
    </h2>

    <!-- Menú de elementos -->
    <div class="flex flex-col items-start w-full mt-2 space-y-2">
      <RouterLink
        v-for="item in menuItems"
        :key="item.name"
        v-show="hasPermission(item.permissions)"
        :to="{ name: item.route, params: { id: item.id } }"
        class="w-full flex items-center pl-4 py-2 rounded-md hover:bg-granate dark:hover:bg-granate-dark group transition-all duration-200"
      >
        <template v-slot="{ isActive }">
          <span
            class="text-sm font-normal flex items-center justify-start w-full h-full group-hover:text-white"
            :class="[
              isActive
                ? 'mt-2 border-b border-b-granate text-granate dark:text-white'
                : 'text-granate dark:text-gray-400',
            ]"
          >
            <component :is="item.icon" class="w-5 h-5 mr-2" />
            <p>{{ item.name }}</p>
          </span>
        </template>
      </RouterLink>
    </div>

    <!-- Logout y Modo Oscuro -->
    <div class="flex items-center mb-4 space-x-2 pl-4 mt-auto">
      <!-- Logout -->
      <span
        class="cursor-pointer text-negroClaro dark:text-gray-400 hover:text-white hover:bg-granate dark:hover:bg-granate-dark rounded-md px-4 py-1 transition-colors font-normal text-sm"
        @click="onLogout"
      >
        Salir
      </span>

      <!-- Botón de Modo Oscuro -->
      <button
        class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
        @click="updateDarkMode(!isDarkMode)"
      >
        <svg
          v-if="!isDarkMode"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="w-6 h-6 text-gray-400"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
          />
        </svg>
        <svg
          v-else
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="w-6 h-6 text-white"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"
          />
        </svg>
      </button>
    </div>
  </div>
</template>
