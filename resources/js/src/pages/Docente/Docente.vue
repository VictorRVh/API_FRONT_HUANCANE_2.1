<template>
  <AuthorizationFallback :permissions="['teachers-all', 'teachers-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex justify-between items-center">
        <h2 class="text-black dark:text-white font-bold text-2xl">Docentes</h2>
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
              <Th>Id</Th>
              <Th>Nombre</Th>
              <Th>Apellido Paterno</Th>
              <Th>Apellido Materno</Th>
              <Th>DNI</Th>
              <Th>Acción</Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="(user, index) in paginatedTeachers" :key="user.id" class="text-center text-sm h-[40px]">
              <Td class="py-2 px-4 border-0">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</Td>
              <Td class="py-2 px-4 border-0">
                <div class="text-black dark:text-white font-bold">{{ user?.name }}</div>
                <div class="text-xs text-gray-500 relative group cursor-pointer" @click="copiarAlPortapapeles(user?.email)">
                  {{ user?.email }}
                  <span class="absolute top-[-24px] left-1/2 -translate-x-1/2 text-[10px] text-white bg-gray-800 px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition duration-200 z-10">
                    Copiar
                  </span>
                </div>
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">{{ user?.apellido_paterno }}</Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">{{ user?.apellido_materno }}</Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">{{ user?.dni }}</Td>
              <Td class="py-2 px-4 border-0">
                <div class="flex gap-2 justify-center items-center">
                  <ViewButton />
                  <EditButton @click="showSlider(true, user)" />
                  <DeleteButton @click="onDelete(user)" />
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>

      <div class="flex justify-end mt-4">
        <Page :current-page="currentPage" :total-pages="totalPages" @page-changed="changePage" />
      </div>
    </div>

    <DocenteSlider :show="slider" :user="sliderData" @hide="hideSlider" />
  </AuthorizationFallback>
</template>

<script setup>
import { ref, computed } from "vue";

import Table from "../../components/table/Table.vue";
import THead from "../../components/table/THead.vue";
import TBody from "../../components/table/TBody.vue";
import Tr from "../../components/table/Tr.vue";
import Th from "../../components/table/Th.vue";
import Td from "../../components/table/Td.vue";
import CreateButton from "../../components/ui/CreateButton.vue";
import EditButton from "../../components/ui/EditButton.vue";
import ViewButton from "../../components/ui/ViewButton.vue";
import DeleteButton from "../../components/ui/DeleteButton.vue";
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";
import DocenteSlider from "../../components/page/Docente/DocenteSlider.vue";
import Page from "../../components/pagination/page.vue";
import Buscador from "../../components/buscador/buscador.vue";
import Filter from "../../components/buscador/filter.vue";

import useStudentsStore from "../../store/Estudiante/useStudentStore";
import useSlider from "../../composables/useSlider";
import useModalToast from "../../composables/useModalToast";
import useHttpRequest from "../../composables/useHttpRequest";

const userStore = useStudentsStore();
const currentPage = ref(1);
const itemsPerPage = 10;
const searchQuery = ref('');
const filterOption = ref(null);

const { slider, sliderData, showSlider, hideSlider } = useSlider("user-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteUser, deleting } = useHttpRequest("/users");

if (!userStore.teachers?.length) await userStore.loadTeacher();

const filteredTeachers = computed(() => {
  const query = searchQuery.value.toLowerCase();
  return userStore.teachers.filter(user =>
    user.name?.toLowerCase().includes(query) ||
    user.apellido_paterno?.toLowerCase().includes(query) ||
    user.apellido_materno?.toLowerCase().includes(query) ||
    user.dni?.toLowerCase().includes(query) ||
    user.email?.toLowerCase().includes(query)
  );
});

const sortedTeachers = computed(() => {
  const users = [...filteredTeachers.value];
  if (filterOption.value === 'name-asc') {
    return users.sort((a, b) => a.name.localeCompare(b.name));
  } else if (filterOption.value === 'name-desc') {
    return users.sort((a, b) => b.name.localeCompare(a.name));
  }
  return users;
});

const totalPages = computed(() => Math.ceil(sortedTeachers.value.length / itemsPerPage));

const paginatedTeachers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return sortedTeachers.value.slice(start, end);
});

const changePage = (page) => {
  currentPage.value = page;
};

const onDelete = (user) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deleteUser(user?.id);
    if (isDeleted) {
      showToast(`"${user?.name}" eliminado correctamente.`);
      userStore.loadTeacher();
    }
  });
};

const aplicarFiltro = (opcion) => {
  filterOption.value = opcion;
  currentPage.value = 1;
};

const copiarAlPortapapeles = (texto) => {
  if (!texto) return;
  navigator.clipboard.writeText(texto)
    .then(() => showToast('Correo copiado al portapapeles'))
    .catch(() => showToast('Error al copiar'));
};
</script>

<style scoped>
/* Todo en Tailwind */
</style>
