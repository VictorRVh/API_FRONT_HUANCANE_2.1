<template>
    <AuthorizationFallback :permissions="['roles-all', 'roles-view']">
        <div class="w-full space-y-4 py-4"> <!-- Reducido padding vertical -->
            <div class="flex justify-between">
                <h2 class="text-active font-bold text-xl">Roles</h2> <!-- Ajustado tamaño de texto -->
                <CreateButton @click="showSlider(true)" />
            </div>

            <div class="w-full overflow-auto">
                <Table>
                    <THead>
                        <Tr>
                            <Th> Id </Th>
                            <Th> Role </Th>
                            <Th> Permissions </Th>
                            <Th> Action </Th>
                        </Tr>
                    </THead>

                    <TBody>
                        <Tr v-for="role in paginatedRoles" :key="role.id">
                            <Td class="py-1 px-2">{{ role?.id }}</Td>
                            <Td class="py-1 px-2">
                                <div class="font-bold text-granate dark:text-white">
                                    {{ role?.name }}
                                </div>
                            </Td>
                            <Td class="py-1 px-2">
                                <div class="max-h-[75px] overflow-y-auto"> <!-- Altura limitada de permisos -->
                                    <ul class="list-disc pl-4">
                                        <li v-for="permission in role.permissions" :key="permission.id">
                                            {{ permission?.name }}
                                        </li>
                                    </ul>
                                </div>
                            </Td>
                            <Td class="py-1 px-2">
                                <div class="flex gap-1 justify-center"> <!-- Acción en fila con espacio reducido -->
                                    <EditButton @click="showSlider(true, role)" />
                                    <DeleteButton @click="onDelete(role)" />
                                </div>
                            </Td>
                        </Tr>
                    </TBody>
                </Table>
            </div>

            <!-- Paginación -->
            <div class="flex justify-end mt-2"> <!-- Reducido margen superior -->
                <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="handlePageChange" />
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

import useUserStore from '../store/useUserStore';
import useRoleStore from '../store/useRoleStore';
import usePermissionStore from '../store/usePermissionStore';
import useSlider from '../composables/useSlider';
import useModalToast from '../composables/useModalToast';
import useHttpRequest from '../composables/useHttpRequest';

const userStore = useUserStore();
const roleStore = useRoleStore();
const permissionStore = usePermissionStore();

if (!permissionStore.permissions.length) await permissionStore.loadPermissions();
if (!roleStore.roles?.length) await roleStore.loadRoles();

const { slider, sliderData, showSlider, hideSlider } = useSlider('role-crud');
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteRole, deleting } = useHttpRequest('/roles');

// Variables para paginación
const currentPage = ref(1);
const itemsPerPage = 5;

const totalPages = computed(() => Math.ceil(roleStore.roles.length / itemsPerPage));

const paginatedRoles = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return roleStore.roles.slice(start, end);
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
            showToast(`Role "${role?.name}" deleted successfully...`);
            await roleStore.loadRoles();
            userStore.loadUsers();
        }
    });
};
</script>

<style scoped>
/* Reducir padding para filas */
.py-1 {
    padding: 1rem;

}
</style>
