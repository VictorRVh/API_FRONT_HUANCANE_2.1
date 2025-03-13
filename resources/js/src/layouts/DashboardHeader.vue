<script setup>
import { inject, computed } from "vue";
import useHttpRequest from "../composables/useHttpRequest";
import useUserStore from "../store/useUserStore";
import useRoleStore from "../store/useRoleStore";
import usePermissionStore from "../store/usePermissionStore";
import useAppRouter from "../composables/useAppRouter";

// Inyecta el tema y otras propiedades globales
const { windowWidth } = inject("theme");
const { index: logout } = useHttpRequest("/logout");
const { pushToRoute } = useAppRouter();

// Stores
const userStore = useUserStore();
const roleStore = useRoleStore();
const permissionStore = usePermissionStore();

// Obtener permisos del usuario
const userPermissions = computed(
  () => userStore.user?.permissions.map(p => p.name) || []
);

// Definición de elementos del menú
const menuItems = [
{ name: "Home", icon: "HomeIcon", route: "home", permissions: [] },
  { name: "Usuarios", icon: "UsersIcon", route: "users", permissions: ["users-all", "users-icon"] },
  { name: "Roles", icon: "BookmarkIcon", route: "roles", permissions: ["roles-all", "roles-icon"] },
  { name: "Permisos", icon: "BookmarkSquareIcon", route: "permissions", permissions: ["permissions-all", "permissions-icon"] },
  { name: "Docente", icon: "UserIcon", route: "docentes", permissions: ["teachers-all", "teachers-icon"] },
  { name: "Estudiante", icon: "AcademicCapIcon", route: "estudiantes", permissions: ["students-all", "students-icon"] },
  { name: "PlanFormativo", icon: "CalendarIcon", route: "plan", permissions: ["plan-all", "plan-icon"] },
  { name: "Sede", icon: "BookmarkSquareIcon", route: "sedes", permissions: ["places-all", "places-icon"] },
  { name: "Especialidad", icon: "BuildingOfficeIcon", route: "especialidad", permissions: ["specialties-all", "specialties-icon"] },
  { name: "Grupos", icon: "UsersIcon", route: "grupos", permissions: ["groups-all", "groups-icon"] },
  { name: "Matricula", icon: "BookOpenIcon", route: "matriculas", permissions: ["enrollmentStudent-all", "enrollmentStudent-icon"] },
  { name: "Notas", icon: "ClipboardDocumentListIcon", route: "notas", permissions: ["note-all", "note-icon"] },
  { name: "Notas", icon: "NewspaperIcon", route: "notaStudent", permissions: ["note-student-all", "note-student-icon"] },
  { name: "Reportes", icon: "ChartBarIcon", route: "reporte", permissions: ["users-all", "users-icon"] },
  { name: "Certificados", icon: "FolderIcon", route: "certificado", permissions: ["users-all", "users-icon"] },
];

// Agrupar el menú en secciones (sin mostrar el título de cada sección ni línea divisoria)
const sections = [
  { title: "Principal", items: menuItems.filter(item => item.route === "home") },
  { 
    title: "Administración Académica", 
    items: menuItems.filter(item =>
      ["docentes", "estudiantes", "matriculas", "especialidad", "users", "roles", "permissions", "sedes", "grupos,plan", "reporte", "certificado","notas", "notaStudent"].includes(item.route)
    )
  },
 
];

// Función para verificar permisos
const hasPermission = (itemPermissions) =>
  itemPermissions.some(perm => userPermissions.value.includes(perm));

// Modo reducido para resolución entre 1024 y 1280px
const smallMode = computed(() => windowWidth.value >= 1000 && windowWidth.value < 1280);
</script>

<template>
  <div
    class="flex flex-col min-h-screen bg-blancoPuro dark:bg-gray-800 dark:text-gray-400 font-inter w-12/12 max-w-3xl"
  >
    <!-- Título "HERRAMIENTAS" -->
    <h2 class="text-lg font-semibold text-negroClaro dark:text-gray-400 mt-4 mb-2 pl-4">
      Menu
    </h2>

    <!-- Menú de elementos con scroll vertical -->
    <div
      class="flex flex-col items-start w-full space-y-2 overflow-y-auto h-[70vh] custom-scrollbar"
    >
    <RouterLink
  v-for="item in menuItems"
  :key="item.name"
  v-show="item.name === 'Home' || hasPermission(item.permissions)"
  :to="{ name: item.route  /*, params: { id: item.id } */ }"
  class="text-white w-full flex pl-4  items-center rounded-md group transition-all duration-200 hover:bg-granate dark:hover:bg-granate-dark"
>
  <template v-slot="{ isActive }">
    <span
      class="text-sm font-normal py-2  pl-2 flex items-center justify-start w-full h-full group-hover:text-white transition-all duration-300 rounded-lg"
      :class="[
        isActive
          ? 'bg-granate text-white dark:text-white transition-all ml-5 duration-300 rounded-lg px-6'
          : 'text-granate dark:text-white'
      ]"
    >
      <component :is="item.icon" class="w-6 h-6 mr-2" />
      <p>{{ item.name }} </p>
    </span>
  </template>
</RouterLink>

    </div>
  </div>
</template>
<style>
.custom-scrollbar {
  overflow-x: hidden;
}
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #a0a0a0;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #8a8a8a;
}
</style>
