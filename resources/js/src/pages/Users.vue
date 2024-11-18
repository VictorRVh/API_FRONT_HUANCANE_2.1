<template>
    <AuthorizationFallback :permissions="['users-all', 'users-view']">
        <div class="w-full space-y-4 py-4"> <!-- Reducido padding vertical -->
            <div class="flex-between">
                <h2 class="text-active font-bold text-xl">Users</h2> <!-- Ajustado tamaño de texto -->
                <CreateButton @click="showSlider(true)" />
            </div>

            <!-- Contenedor de tabla sin scroll principal -->
            <div class="w-full">
                <Table>
                    <THead>
                        <Tr>
                            <Th> Id </Th>
                            <Th> User </Th>
                            <Th > Roles </Th>
                            <Th> Permissions </Th>
                            <Th> Action </Th>
                        </Tr>
                    </THead>

                    <TBody>
                        <Tr v-for="user in paginatedUsers" :key="user.id">
                            <Td class="table-row-height">{{ user?.id }}</Td>
                            <Td class="table-row-height">
                                <div class="font-bold text-granate dark:text-white">{{ user?.name }}</div>
                                <div class="text-xsm text-[#aaa]">{{ user?.email }}</div>
                            </Td>
                            <Td class="table-row-height">
                                <ul class="list-disc pl-3 max-h-[75px] overflow-y-auto">
                                    <li v-for="role in user.roles" :key="role.id" class="text-black dark:text-white">
                                        {{ role?.name }}
                                    </li>
                                </ul>
                            </Td>
                            <!-- Columna de Permisos con altura limitada -->
                            <Td class="table-row-height">
                                <div class="max-h-[75px] overflow-y-auto">
                                    <ul class="w-max mx-auto list-disc">
                                        <li v-for="permission in user.permissions" :key="permission.id" class="text-left">
                                            {{ permission?.name }}
                                        </li>
                                    </ul>
                                </div>
                            </Td>
                            <Td class="table-row-height">
                                <div class="flex gap-1 justify-center"> <!-- Acción en fila con espacio reducido -->
                                    <EditButton @click="showSlider(true, user)" />
                                    <DeleteButton @click="onDelete(user)" />
                                </div>
                            </Td>
                        </Tr>
                    </TBody>
                </Table>
            </div>

            <!-- Paginación alineada a la derecha -->
            <div class="flex justify-end mt-2"> <!-- Reducido margen superior -->
                <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="handlePageChange" />
            </div>
        </div>

        <UserSlider :show="slider" :user="sliderData" @hide="hideSlider" />
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
import UserSlider from '../components/page/UserSlider.vue';
import Pagination from '../components/pagination/page.vue';

import useUserStore from '../store/useUserStore';
import useRoleStore from '../store/useRoleStore';
import useSlider from '../composables/useSlider';
import useModalToast from '../composables/useModalToast';
import useHttpRequest from '../composables/useHttpRequest';

const userStore = useUserStore();
const roleStore = useRoleStore();
const currentPage = ref(1);
const itemsPerPage = 5;

// Cargar usuarios y roles
await userStore.loadUsers();
await roleStore.loadRoles();

// Slider y modales
const { slider, sliderData, showSlider, hideSlider } = useSlider('user-crud');
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteUser, deleting } = useHttpRequest('/users');

const totalPages = computed(() => Math.ceil(userStore.users.length / itemsPerPage));

const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return userStore.users.slice(start, end);
});

const handlePageChange = (page) => {
    currentPage.value = page;
};

const onDelete = (user) => {
    if (deleting.value) return;
    showConfirmModal(null, async (confirmed) => {
        if (!confirmed) return;
        const isDeleted = await deleteUser(user?.id);
        if (isDeleted) {
            showToast(`"${user?.name}" deleted successfully...`);
            await userStore.loadUsers();
        }
    });
};
</script>

<style scoped>
.table-row-height {
    padding: 1rem; /* Reducido para disminuir la altura de las filas */
    vertical-align: middle;
}
</style>
