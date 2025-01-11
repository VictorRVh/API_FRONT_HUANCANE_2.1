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

const router = useRouter();
const props = defineProps({
  id: {
    type: Number,
    default: 0,
  },
});

const userStore = useStudentsStore();
const listUnit = ref([]);
const selectUnit = ref(null);

onMounted(async () => {
  if (!userStore.student?.length) {
    await userStore.loadGroupStudent(props.id);
  }
});

watch(() => props.id, async (newId) => {
  await userStore.loadGroupStudent(newId);
});

const dataPDF = {
  logo: "/img/logoTwo.png", // Ruta a la imagen del logo
  photo: "/img/user.png", // Ruta a la imagen de la foto
  photoMinisterio: "/img/logoMin.png",
  name: "ANDRADE MARICHIN, Azucena Lisbeth",
  especialidad: "PELUQUERIA Y BARBERIA",
  module: "CORTE DE CABELLO, DISEÑO DE BARBA, PEINADO",
  unidades:[
    {unidad:"aquietendremos las los zapatos con los mios y otros",capacidad:"en esta partede de uniad veremos las cpasa mas simples de la zapateri con la lo novedoso ya que son mejores pepepe",hora:"6",credito:"17"},
    {unidad:"aquietendremos las los zapatos con los mios y otros",capacidad:"en esta partede de uniad veremos las cpasa mas simples de la zapateri con la lo novedoso ya que son mejores pepepe",hora:"5",credito:"12"},
    {unidad:"aquietendremos las los zapatos con los mios y otros",capacidad:"en esta partede de uniad veremos las cpasa mas simples de la zapateri con la lo novedoso ya que son mejores pepepe",hora:"4",credito:"15"},
    {unidad:"aquietendremos las los zapatos con los mios y otros",capacidad:"en esta partede de uniad veremos las cpasa mas simples de la zapateri con la lo novedoso ya que son mejores pepepe",hora:"7",credito:"12"},
    {unidad:"aquietendremos las los zapatos con los mios y otros",capacidad:"en esta partede de uniad veremos las cpasa mas simples de la zapateri con la lo novedoso ya que son mejores pepepe",hora:"4",credito:"15"},
    {unidad:"aquietendremos las los zapatos con los mios y otros",capacidad:"en esta partede de uniad veremos las cpasa mas simples de la zapateri con la lo novedoso ya que son mejores pepepe",hora:"2",credito:"12"},
  ],
  startDate: "18/03/2024",
  endDate: "19/07/2024",
  credits: 20,
  hours: 528,
  location: "Huancané, 24 de diciembre de 2024"
}

const seeNote = () => {};
</script>

<template>
  <AuthorizationFallback :permissions="['groups-all', 'groups-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-black dark:text-white font-bold text-2xl">Estudiantes</h2>
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
              <Th>PDF</Th>
            </Tr>
          </THead>
          <TBody>
            <Tr
              v-for="user in userStore?.student?.estudiantes"
              :key="user.id"
            >
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ user.estudiante?.id }}
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
                   <Button @click="generateCertificate(dataPDF)">Certificado</Button>
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
