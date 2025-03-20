<template>
  <AuthorizationFallback :permissions="['permissions-all', 'permissions-view']">
    <div class="w-full space-y-4 py-4">
      <div class="flex justify-between items-center">
        <h2 class="text-active font-bold text-xl">Administrador de Permisos</h2>
        <div class="flex items-center gap-4 ml-auto">
          <Filter @filter="aplicarFiltro" />
          <CreateButton @click="showSlider(true)" />
          <Buscador v-model="searchQuery" />
        </div>
      </div>

      <div class="w-full">
        <Table class="border-collapse divide-y divide-transparent">
          <THead>
            <Tr class="text-center">
              <Th> Id </Th>
              <Th> Nombre del Permiso </Th>
              <Th> Acciones </Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="(permission, index) in paginatedPermissions" :key="permission.id" class="text-center text-sm h-[40px]">
              <Td class="py-2 px-4 border-0">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</Td>
              <Td class="py-2 px-4 border-0">
                <div class="text-granate dark:text-white font-bold">{{ permission?.name }}</div>
              </Td>
              <Td class="py-2 px-4 border-0">
                <div class="flex gap-2 justify-center">
                  <EditButton @click="showSlider(true, permission)" />
                  <DeleteButton @click="onDelete(permission)" />
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>

      <div class="flex justify-end mt-2">
        <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="handlePageChange" />
      </div>
    </div>

    <PermissionSlider :show="slider" :permission="sliderData" @hide="hideSlider" />
  </AuthorizationFallback>
</template>

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
import Buscador from "../components/buscador/buscador.vue";
import Filter from "../components/buscador/filter.vue";

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

const currentPage = ref(1);
const itemsPerPage = 10;
const searchQuery = ref('');
const filterOption = ref(null);

const { slider, sliderData, showSlider, hideSlider } = useSlider("permission-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deletePermission, deleting } = useHttpRequest("/permissions");
const { isUserAuthenticated } = useAuth();

if (!permissionStore.permissions.length) await permissionStore.loadPermissions();

const filteredPermissions = computed(() => {
  const query = searchQuery.value.toLowerCase();
  return permissionStore.permissions.filter(permission =>
    permission.name.toLowerCase().includes(query)
  );
});

const sortedPermissions = computed(() => {
  const perms = [...filteredPermissions.value];
  if (filterOption.value === 'name-asc') {
    return perms.sort((a, b) => a.name.localeCompare(b.name));
  } else if (filterOption.value === 'name-desc') {
    return perms.sort((a, b) => b.name.localeCompare(a.name));
  }
  return perms;
});

const totalPages = computed(() => Math.ceil(sortedPermissions.value.length / itemsPerPage));

const paginatedPermissions = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return sortedPermissions.value.slice(start, end);
});

const handlePageChange = (page) => {
  currentPage.value = page;
};

const onDelete = (permission) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deletePermission(permission?.id);
    if (isDeleted) {
      showToast(`Permission "${permission?.name}" eliminado correctamente`);
      await permissionStore.loadPermissions();
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();
    }
  });
};

const aplicarFiltro = (opcion) => {
  filterOption.value = opcion;
  currentPage.value = 1;
};
</script>

<style scoped>
/* Todo con Tailwind */
</style>
