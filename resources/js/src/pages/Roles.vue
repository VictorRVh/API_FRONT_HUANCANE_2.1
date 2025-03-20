<template>
    <AuthorizationFallback :permissions="['roles-all', 'roles-view']">
      <div class="w-full space-y-4 py-4">
        <div class="flex justify-between items-center">
          <h2 class="text-active font-bold text-xl">Roles</h2>
          <div class="flex items-center gap-4 ml-auto">
            <Filter @filter="aplicarFiltro" />
            <CreateButton @click="showSlider(true)" />
            <Buscador v-model="searchQuery" />
          </div>
        </div>
  
        <div class="w-full overflow-auto">
          <Table class="border-collapse divide-y divide-transparent">
            <THead>
              <Tr class="text-center">
                <Th> Id </Th>
                <Th> Rol </Th>
                <Th class="w-[250px]"> Permisos </Th>
                <Th> Acciones </Th>
              </Tr>
            </THead>
  
            <TBody>
              <Tr v-for="(role, index) in paginatedRoles" :key="role.id" class="text-center text-sm h-[40px]">
                <Td class="py-2 px-4 border-0">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</Td>
                <Td class="py-2 px-4 border-0">
                  <div class="font-bold text-granate dark:text-white">
                    {{ role?.name }}
                  </div>
                </Td>
                <Td class="py-2 px-4 border-0">
                  <div class="px-2 text-sm text-gray-800 dark:text-gray-200">
                    <span>
                      {{
                        role.permissions
                          .slice(0, 3)
                          .map(p => p?.name)
                          .join(', ')
                      }}
                    </span>
                    <template v-if="role.permissions.length > 3">
                      <span class="text-blue-500 cursor-pointer hover:underline ml-1" @click="openPermissionModal(role)">
                        ... más
                      </span>
                    </template>
                  </div>
                </Td>
                <Td class="py-2 px-4 border-0">
                  <div class="flex gap-2 justify-center">
                    <EditButton @click="showSlider(true, role)" />
                    <DeleteButton @click="onDelete(role)" />
                  </div>
                </Td>
              </Tr>
            </TBody>
          </Table>
        </div>
  
        <div class="flex justify-end mt-2">
          <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="handlePageChange" />
        </div>
  
        <!-- Modal Permisos -->
        <div v-if="showPermissionModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50" @click.self="closePermissionModal">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto p-6 relative">
            <button @click="closePermissionModal"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 dark:hover:text-white">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                   class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
              </svg>
            </button>
  
            <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white text-center">Permisos del Rol</h3>
  
            <table class="w-full text-sm text-left border-collapse">
              <thead>
                <tr class="bg-gray-100 dark:bg-gray-700">
                  <th class="px-3 py-2 border-b text-center text-gray-700 dark:text-gray-200" colspan="3">Nombre del Permiso</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(chunk, rowIndex) in permissionChunks" :key="rowIndex" class="text-sm h-[40px]">
                  <td v-for="(perm, colIndex) in chunk" :key="colIndex" class="px-3 py-1 text-left border-b text-gray-800 dark:text-gray-200">
                    <span class="font-semibold text-gray-500 dark:text-gray-400">{{ rowIndex * 3 + colIndex + 1 }}.-</span>
                    {{ perm.name }}
                  </td>
                  <td v-for="empty in 3 - chunk.length" :key="'empty-'+empty" class="px-3 py-1 text-left border-b"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
  
      <RoleSlider :show="slider" :role="sliderData" @hide="hideSlider" />
    </AuthorizationFallback>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue';
  import Table from '../components/table/Table.vue';
  import THead from '../components/table/THead.vue';
  import TBody from '../components/table/TBody.vue';
  import Tr from '../components/table/Tr.vue';
  import Th from '../components/table/Th.vue';
  import Td from '../components/table/Td.vue';
  import CreateButton from '../components/ui/CreateButton.vue';
  import EditButton from '../components/ui/EditButton.vue';
  import DeleteButton from '../components/ui/DeleteButton.vue';
  import AuthorizationFallback from '../components/page/AuthorizationFallback.vue';
  import RoleSlider from '../components/page/RoleSlider.vue';
  import Pagination from '../components/pagination/page.vue';
  import Buscador from '../components/buscador/buscador.vue';
  import Filter from '../components/buscador/filter.vue';
  
  import useUserStore from '../store/useUserStore';
  import useRoleStore from '../store/useRoleStore';
  import usePermissionStore from '../store/usePermissionStore';
  import useSlider from '../composables/useSlider';
  import useModalToast from '../composables/useModalToast';
  import useHttpRequest from '../composables/useHttpRequest';
  
  const userStore = useUserStore();
  const roleStore = useRoleStore();
  const permissionStore = usePermissionStore();
  
  const currentPage = ref(1);
  const itemsPerPage = 10;
  const searchQuery = ref('');
  const filterOption = ref(null);
  const showPermissionModal = ref(false);
  const selectedPermissions = ref([]);
  
  if (!permissionStore.permissions.length) await permissionStore.loadPermissions();
  if (!roleStore.roles?.length) await roleStore.loadRoles();
  
  const { slider, sliderData, showSlider, hideSlider } = useSlider('role-crud');
  const { showConfirmModal, showToast } = useModalToast();
  const { destroy: deleteRole, deleting } = useHttpRequest('/roles');
  
  const filteredRoles = computed(() => {
    const query = searchQuery.value.toLowerCase();
    return roleStore.roles.filter(role =>
      role.name.toLowerCase().includes(query)
    );
  });
  
  const sortedRoles = computed(() => {
    const roles = [...filteredRoles.value];
    if (filterOption.value === 'name-asc') {
      return roles.sort((a, b) => a.name.localeCompare(b.name));
    } else if (filterOption.value === 'name-desc') {
      return roles.sort((a, b) => b.name.localeCompare(a.name));
    }
    return roles;
  });
  
  const totalPages = computed(() => Math.ceil(sortedRoles.value.length / itemsPerPage));
  
  const paginatedRoles = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return sortedRoles.value.slice(start, end);
  });
  
  const handlePageChange = (page) => {
    currentPage.value = page;
  };
  
  const onDelete = (role) => {
    if (deleting.value) return;
  
    showConfirmModal(null, async (confirmed) => {
      if (!confirmed) return;
  
      const isDeleted = await deleteRole(role?.id);
      if (isDeleted) {
        showToast(`Role "${role?.name}" eliminado correctamente`);
        await roleStore.loadRoles();
        userStore.loadUsers();
      }
    });
  };
  
  const openPermissionModal = (role) => {
    selectedPermissions.value = role.permissions;
    showPermissionModal.value = true;
  };
  
  const closePermissionModal = () => {
    showPermissionModal.value = false;
  };
  
  const permissionChunks = computed(() => {
    const chunkSize = 3;
    const chunks = [];
    for (let i = 0; i < selectedPermissions.value.length; i += chunkSize) {
      chunks.push(selectedPermissions.value.slice(i, i + chunkSize));
    }
    return chunks;
  });
  
  const aplicarFiltro = (opcion) => {
    filterOption.value = opcion;
    currentPage.value = 1;
  };
  </script>
  
  <style scoped>
  /* Todo en Tailwind */
  </style>
  