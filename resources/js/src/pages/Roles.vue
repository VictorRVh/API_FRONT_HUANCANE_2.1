<template>
    <AuthorizationFallback :permissions="['roles-all', 'roles-view']">
        <div class="w-full space-y-4 py-4">
            <div class="flex justify-between">
                <h2 class="text-active font-bold text-xl">Roles</h2>
                <CreateButton @click="showSlider(true)" />
            </div>

            <div class="w-full overflow-auto">
                <!-- Aplicación de Tailwind para eliminar líneas internas -->
                <Table class="border-collapse divide-y divide-transparent">
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
                            <Td class="py-2 px-4 border-0">{{ role?.id }}</Td>
                            <Td class="py-2 px-4 border-0">
                                <div class="font-bold text-granate dark:text-white">
                                    {{ role?.name }}
                                </div>
                            </Td>
                            <Td class="py-2 px-4 border-0">
                                <div class="max-h-[75px] overflow-y-auto">
                                    <ul class="list-disc pl-4">
                                        <li v-for="permission in role.permissions" :key="permission.id">
                                            {{ permission?.name }}
                                        </li>
                                    </ul>
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
/* No se necesita CSS adicional: Todo se realiza con clases de Tailwind */
</style>
