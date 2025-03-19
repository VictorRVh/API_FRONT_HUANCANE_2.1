<script setup>
import { defineProps, watch, ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import Table from "../../components/table/Table.vue";
import THead from "../../components/table/THead.vue";
import TBody from "../../components/table/TBody.vue";
import Tr from "../../components/table/Tr.vue";
import Th from "../../components/table/Th.vue";
import Td from "../../components/table/Td.vue";
import ViewButton from "../../components/ui/ViewButton.vue";
import CreateButton from "../../components/ui/CustomButton.vue";
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";
import useStudentsStore from "../../store/Grupo/useGrupoStore";
import { generateNominaPDF, reporteNominaUgel } from "../../components/pdf/generatePdf";

import { exportToExcel } from '../../components/Exel/alumnosXlsx';

const router = useRouter();
const props = defineProps({
  idGroupAll: {
    type: Number,
    default: 0,
  },
});

const userStore = useStudentsStore();
const listUnit = ref([]);
const selectUnit = ref(null);
const dataExel = ref([]);
onMounted(async () => {
  if (!userStore.student?.length) {
    await userStore.loadGroupStudent(props.idGroupAll);

    dataExel.value = userStore.student.estudiantes.map(item => ({
      Nombre: item.estudiante.name,
      ApellidoPaterno: item.estudiante.apellido_paterno,
      ApellidoMaterno: item.estudiante.apellido_materno,
      DNI: item.estudiante.dni,
      Email: item.estudiante.email,
      Celular: item.estudiante.celular,
      Sexo: item.estudiante.sexo
    }));

  }
});

watch(() => props.idGroupAll, async (newId) => {
  await userStore.loadGroupStudent(newId);
});


const seeNote = () => { };
const nominaNormal = async (idGrupo) => {
  try {
    const response = await fetch(`/api/grupoDocenteTwo/${idGrupo}`);
    if (!response.ok) {
      throw new Error('Error al obtener los datos del grupo');
    }
    const data = await response.json();
    generateNominaPDF(data);
    console.log(data)
  } catch (error) {
    console.error('Error en la consulta de matrícula:', error);
  }
}
const nominaUgel = async (idGrupo) => {
  try {
    const response = await fetch(`/api/grupoDocenteTwo/${idGrupo}`);
    if (!response.ok) {
      throw new Error('Error al obtener los datos del grupo');
    }
    const data = await response.json();
    reporteNominaUgel(data);
    // console.log(data)
  } catch (error) {
    console.error('Error en la consulta de matrícula:', error);
  }
}

console.log("EXEL: ", userStore.student.estudiantes)


// Función para exportar
const exportData = () => {
  exportToExcel(dataExel.value, 'alumnos.xlsx', 'Hoja1');
};
// @click="nominaNormal(idGroupAll)"
// @click="nominaUgel(idGroupAll)" 
const pdfProfe = "Nomina Docente";
const pdfUgel = "Nomina UGEL";
</script>

<template>
  <AuthorizationFallback :permissions="['groups-all', 'groups-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-black dark:text-white font-bold text-2xl">Estudiantes</h2>
        <div class="flex gap-4 items-center">
          <!-- Primer botón con ícono SVG -->
          <div class="flex items-center gap-1">
            <!-- Ícono PDF -->

            <!-- Botón -->
            <CreateButton :name="'Alumnos'" @click="exportData" :color="'#155724'" />
            <CreateButton :name="pdfProfe" @click="nominaNormal(props.idGroupAll)" />
            <CreateButton :name="pdfUgel" @click="nominaUgel(props.idGroupAll)" />

          </div>
        </div>

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
              <Th>Aciones</Th>
            </Tr>
          </THead>
          <TBody>
            <Tr v-for="(user,index) in userStore.student.estudiantes" :key="user.id">
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ index+1 }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                <div>{{ user.estudiante?.name }}</div>
                <div class="text-sm text-gray-500 dark:text-white">
                  {{ user.estudiante?.email }}
                </div>
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ user.estudiante?.apellido_paterno }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ user.estudiante?.apellido_materno }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ user.estudiante?.dni }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">

                <ViewButton />

              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>
  </AuthorizationFallback>
</template>

<style scoped>
/* No se necesita CSS adicional, todo está gestionado con Tailwind */
</style>
