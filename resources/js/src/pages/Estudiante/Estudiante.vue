<script setup>
import { defineProps, watch, ref, computed } from "vue";

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
import EstudianteSlider from "../../components/page/Estudiante/EstudianteSlider.vue";

import useStudentsStore from "../../store/Estudiante/useStudentStore";
import useRoleStore from "../../store/useRoleStore";

import useSlider from "../../composables/useSlider";
import useModalToast from "../../composables/useModalToast";
import useHttpRequest from "../../composables/useHttpRequest";

import Page from "../../components/pagination/page.vue";

const props = defineProps({
  id: {
    type: Number,
    default: 0,
  },
});

const userStore = useStudentsStore();
const roleStore = useRoleStore();

if (!userStore.students?.length) await userStore.loadStudents(props.id);
if (!roleStore.roles?.length) await roleStore.loadRoles();

const { slider, sliderData, showSlider, hideSlider } = useSlider("user-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteUser, deleting } = useHttpRequest("/users");

const onDelete = (user) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deleteUser(user?.id);
    if (isDeleted) {
      showToast(`"${user?.name}" eliminado correctamente.`);
      userStore.loadStudents(props.id);
    }
  });
};

// Paginación
const currentPage = ref(1);
const itemsPerPage = 10;
const totalPages = computed(() =>
  Math.ceil(userStore.students.length / itemsPerPage)
);

const paginatedStudents = computed(() =>
  userStore.students.slice(
    (currentPage.value - 1) * itemsPerPage,
    currentPage.value * itemsPerPage
  )
);

const changePage = (page) => {
  currentPage.value = page;
};

// Observa cambios en props.id y recarga la lista de estudiantes
watch(() => props.id, (newId) => {
  userStore.loadStudents(newId);
});
</script>

<template>
  <AuthorizationFallback :permissions="['students-all', 'students-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-black dark:text-white font-bold text-2xl">Estudiantes</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <Table class="border-collapse divide-y divide-transparent">
          <THead>
            <Tr>
              <Th>Id</Th>
              <Th>Nombre</Th>
              <Th>Apellido Paterno</Th>
              <Th>Apellido Materno</Th>
              <Th>DNI</Th>
              <Th>Acción</Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="user in paginatedStudents" :key="user.id">
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ user?.id }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                <div>{{ user?.name }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  {{ user?.email }}
                </div>
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ user?.apellido_paterno }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ user?.apellido_materno }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ user?.dni }}
              </Td>
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

      <!-- Componente de paginación -->
      <div class="flex justify-end mt-4">
        <Page
          :current-page="currentPage"
          :total-pages="totalPages"
          @page-changed="changePage"
        />
      </div>
    </div>

    <EstudianteSlider
      :idUser="props.id"
      :show="slider"
      :user="sliderData"
      @hide="hideSlider"
    />
  </AuthorizationFallback>
</template>

<style scoped>
/* No se necesita CSS adicional, todo está gestionado con Tailwind */
</style>
