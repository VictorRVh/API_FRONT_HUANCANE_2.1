<script setup>
import { ref, computed } from 'vue';
import Table from "../components/table/Table.vue";
import THead from "../components/table/THead.vue";
import TBody from "../components/table/TBody.vue";
import Tr from "../components/table/Tr.vue";
import Th from "../components/table/Th.vue";
import Td from "../components/table/Td.vue";
import CreateButton from "../components/ui/CreateButton.vue";
import EditButton from "../components/ui/EditButton.vue";
import DeleteButton from "../components/ui/DeleteButton.vue";
import AuthorizationFallback from "../components/page/AuthorizationFallback.vue";
import PermissionSlider from "../components/page/PermissionSlider.vue";
import Pagination from "../components/pagination/page.vue";

import useUserStore from "../store/useUserStore";
import useRoleStore from "../store/useRoleStore";
import usePermissionStore from "../store/usePermissionStore";

import useSlider from "../composables/useSlider";
import useModalToast from "../composables/useModalToast";
import useHttpRequest from "../composables/useHttpRequest";
import useAuth from "../composables/useAuth";

const userStore = useUserStore();
const roleStore = useRoleStore();
const permissionStore = usePermissionStore();

if (!permissionStore.permissions.length) await permissionStore.loadPermissions();

const { slider, sliderData, showSlider, hideSlider } = useSlider("permission-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deletePermission, deleting } = useHttpRequest("/permissions");
const { isUserAuthenticated } = useAuth();

// Variables para paginación
const currentPage = ref(1);
const itemsPerPage = 5; // Número de permisos por página

// Calcular el total de páginas basado en la cantidad de permisos
const totalPages = computed(() => Math.ceil(permissionStore.permissions.length / itemsPerPage));

// Calcular los permisos que se mostrarán en la página actual
const paginatedPermissions = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return permissionStore.permissions.slice(start, end);
});

// Cambiar de página
const handlePageChange = (page) => {
  currentPage.value = page;
};

// Función para eliminar permisos
const onDelete = (permission) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deletePermission(permission?.id);
    if (isDeleted) {
      showToast(`Permission "${permission?.name}" deleted successfully...`);
      await permissionStore.loadPermissions();
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();
    }
  });
};
</script>

<template>
  <AuthorizationFallback :permissions="['permissions-all', 'permissions-view']">
    <div class="w-full space-y-4 py-4"> <!-- Reducido padding vertical -->
      <div class="flex justify-between">
        <h2 class="text-active font-bold text-xl">Permissions</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <Table>
          <THead>
            <Tr>
              <Th> Id </Th>
              <Th> Permission </Th>
              <Th> Action </Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="permission in paginatedPermissions" :key="permission.id">
              <Td class="py-1 px-2">{{ permission?.id }}</Td>
              <Td class="py-1 px-2">
                <div class="text-granate dark:text-white font-bold">
                  {{ permission?.name }}
                </div>
              </Td>
              <Td class="py-1 px-2">
                <div class="flex gap-1 justify-center"> <!-- Botones en fila -->
                  <EditButton @click="showSlider(true, permission)" />
                  <DeleteButton @click="onDelete(permission)" />
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>

      <!-- Paginación -->
      <div class="flex justify-end mt-2">
        <Pagination
          :current-page="currentPage"
          :total-pages="totalPages"
          @page-changed="handlePageChange"
        />
      </div>
    </div>

    <PermissionSlider :show="slider" :permission="sliderData" @hide="hideSlider" />
  </AuthorizationFallback>
</template>

<style scoped>
.py-1 {
  padding: 1rem;

}
</style>
