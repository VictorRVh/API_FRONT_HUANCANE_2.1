<script setup>
import { ref, onMounted, watch } from "vue";
import Table from "../../components/table/Table.vue";
import THead from "../../components/table/THead.vue";
import TBody from "../../components/table/TBody.vue";
import Tr from "../../components/table/Tr.vue";
import Th from "../../components/table/Th.vue";
import Td from "../../components/table/Td.vue";
import pdfButton from "../../components/ui/pdfButton.vue";
import useStudentsStore from "../../store/Grupo/useGrupoStore";

const props = defineProps({
  id: {
    type: Number,
    default: 0,
  },
});

const userStore = useStudentsStore();
const selectedStudents = ref([]);
const selectAll = ref(false);

onMounted(async () => {
  if (!userStore.student?.length) {
    await userStore.loadGroupStudent(props.id);
  }
});

watch(
  () => props.id,
  async (newId) => {
    await userStore.loadGroupStudent(newId);
  }
);

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedStudents.value = userStore?.student?.estudiantes.map(
      (student) => student.estudiante.id
    );
  } else {
    selectedStudents.value = [];
  }
};

const toggleStudentSelection = (id) => {
  if (selectedStudents.value.includes(id)) {
    selectedStudents.value = selectedStudents.value.filter((studentId) => studentId !== id);
  } else {
    selectedStudents.value.push(id);
  }
};

const generateCertificate = () => {
  const studentsToPrint = userStore?.student?.estudiantes.filter((student) =>
    selectedStudents.value.includes(student.estudiante.id)
  );
  if (studentsToPrint.length === 0) {
    alert("No hay estudiantes seleccionados.");
    return;
  }
  console.log("Generando PDF para los estudiantes:", studentsToPrint);
  // Aquí puedes agregar la lógica para generar los certificados.
};
</script>

<template>
  <div class="w-full space-y-4 py-6">
    <!-- Opciones: Seleccionar Todo y Generar PDF -->
    <div class="flex items-center justify-end gap-4">
      <div>
        <label class="flex items-center">
          <input
            type="checkbox"
            v-model="selectAll"
            @change="toggleSelectAll"
            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
          />
          <span class="ms-2 text-sm font-medium text-gray-900">Seleccionar Todo</span>
        </label>
      </div>
      <div>
        <pdfButton @click="generateCertificate(dataPDF)"/>
      </div>
    </div>

    <!-- Tabla -->
    <div class="w-full">
      <Table class="border-collapse divide-y divide-transparent">
        <THead>
          <Tr>
            <Th>Id</Th>
            <Th>Nombre</Th>
            <Th>Apellido Paterno</Th>
            <Th>Apellido Materno</Th>
            <Th>DNI</Th>
            <Th>Seleccionar</Th>
          </Tr>
        </THead>
        <TBody>
          <Tr
            v-for="user in userStore?.student?.estudiantes"
            :key="user.estudiante.id"
          >
            <Td class="py-2 px-4 border-0 text-black dark:text-white">
              {{ user.estudiante.id }}
            </Td>
            <Td class="py-2 px-4 border-0 text-black dark:text-white">
              {{ user.estudiante.name }}
            </Td>
            <Td class="py-2 px-4 border-0 text-black dark:text-white">
              {{ user.estudiante.apellido_paterno }}
            </Td>
            <Td class="py-2 px-4 border-0 text-black dark:text-white">
              {{ user.estudiante.apellido_materno }}
            </Td>
            <Td class="py-2 px-4 border-0 text-black dark:text-white">
              {{ user.estudiante.dni }}
            </Td>
            <Td class="py-2 px-4 border-0 text-center">
              <input
                type="checkbox"
                :value="user.estudiante.id"
                v-model="selectedStudents"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
              />
            </Td>
          </Tr>
        </TBody>
      </Table>
    </div>
  </div>
</template>

<style scoped>
/* Todo está gestionado con Tailwind, no se necesita CSS adicional */
</style>













