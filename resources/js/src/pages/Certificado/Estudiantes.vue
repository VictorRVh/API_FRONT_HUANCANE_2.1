<script setup>

import { defineProps, watch, ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import Table from "../../components/table/Table.vue";
import THead from "../../components/table/THead.vue";
import TBody from "../../components/table/TBody.vue";
import Tr from "../../components/table/Tr.vue";
import Th from "../../components/table/Th.vue";
import Td from "../../components/table/Td.vue";
import CreateButton from "../../components/ui/CreateButton.vue";
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";
import useStudentsStore from "../../store/Grupo/useGrupoStore";
import { generateCertificate } from "../../components/pdf/CertificadoPDF";
import pdfButton from "../../components/ui/pdfButton.vue";

import useModalToast from "../../composables/useModalToast";
const router = useRouter();
const props = defineProps({
  certifyId: {
    type: Number,
    default: 0,
  },
});

const { showToast } = useModalToast();

const userStore = useStudentsStore();
const selectedStudents = ref([]); // Almacena los IDs de los estudiantes seleccionados
const selectAll = ref(false); // Controla el estado del checkbox "Seleccionar Todo"

onMounted(async () => {
  if (!userStore.student?.length) {
    await userStore.loadGroupStudent(props.certifyId);
  }
});

watch(() => props.certifyId, async (newId) => {

  await userStore.loadGroupStudent(newId);

});


const toggleSelectAll = () => {
  if (selectAll.value) {

    selectedStudents.value = userStore.student?.estudiantes.map((user) => user.estudiante.id); // Solo el ID

  } else {
    selectedStudents.value = [];
  }
};



const dataPDF = {
  logo: "/img/logoTwo.png",
  photo: "/img/user.png",
  photoMinisterio: "/img/logoMin.png",
  name: "",
  especialidad: "PELUQUERIA Y BARBERIA",
  module: "CORTE DE CABELLO, DISEÑO DE BARBA, PEINADO",
  unidades: [
    {
      unidad: "aquietendremos las los zapatos con los mios y otros",
      capacidad:
        "en esta parte de unidad veremos las cosas más simples de la zapatería con lo novedoso",
      hora: "6",
      credito: "17",
    },
  ],
  startDate: "18/03/2024",
  endDate: "19/07/2024",
  credits: 20,
  hours: 528,
  location: "Huancané, 24 de diciembre de 2024",
};




const dataCertificate = ref([]); // Inicializamos como array vacío

const generateSelectedCertificates = async () => {
  if (selectedStudents.value.length === 0) {
    showToast("Por favor, selecciona al menos un estudiante.");
    return;
  }

  // Carga los certificados de los estudiantes seleccionados
  const certificates = await Promise.all(
    selectedStudents.value.map(async (studyId) => {
      await userStore.loadCertificate(studyId, props.certifyId);
      return userStore.certificate; // Retorna el certificado cargado
    })
  );

  dataCertificate.value = certificates; // Asigna los certificados obtenidos

  console.log("Certificados cargados:", dataCertificate.value[0][0]?.experiencias_formativas[0].nombre_experiencia
  );
    // console.log("Primer certificado:", dataCertificate.value[0][0]?.apellidos_nombres);
  
  generateCertificate(dataPDF ,dataCertificate.value);


};
</script>

<template>
  <div class="w-full space-y-4 py-6">
    <!-- Opciones: Seleccionar Todo y Generar PDF -->
    <div class="flex items-center justify-end gap-4">
      <div>
        <label class="flex items-center">
          <input type="checkbox" v-model="selectAll" @change="toggleSelectAll"
            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" />
          <span class="ms-2 text-sm font-medium text-gray-900">Seleccionar Todo</span>
        </label>
      </div>
      <div>
        <pdfButton @click="generateSelectedCertificates" />
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
          <Tr v-for="(user,index) in userStore?.student?.estudiantes" :key="user.estudiante.id">
            <Td class="py-2 px-4 border-0 text-black dark:text-white">
              {{ index+1 }}
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
              <input type="checkbox" :value="user.estudiante.id" v-model="selectedStudents"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" />
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