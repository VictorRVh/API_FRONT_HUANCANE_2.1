<script setup>
import { defineProps, ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";
import Table from "../../components/Table/Table.vue";
import THead from "../../components/Table/THead.vue";
import TBody from "../../components/Table/TBody.vue";
import Tr from "../../components/Table/Tr.vue";
import Th from "../../components/Table/Th.vue";
import Td from "../../components/Table/Td.vue";
import useStudentsStore from "../../store/Grupo/useGrupoStore";
import useModalToast from "../../composables/useModalToast";

const router = useRouter();
const props = defineProps({
  idNoteStudent: {
    type: Number,
    default: 0,
  },
});

const { showToast } = useModalToast();
const userStore = useStudentsStore();
const dataCertificate = ref([]);


onMounted(async () => {
  try {
    await userStore.loadNotas(props.idNoteStudent);
    dataCertificate.value = userStore.certificate;
  } catch (error) {
    showToast("Error al cargar los datos", "error");
    console.error("Error al cargar los certificados:", error);
  }
});

console.log("datos de la vida: ", dataCertificate.value)

// Función para manejar las notas de cada grupo

</script>

<template>
  <AuthorizationFallback :permissions="['groups-all', 'groups-view']">

    <div class="w-full space-y-4 py-6">
      <h2 class="text-granate dark:text-granateLight font-black text-4xl mb-4">Datos del Programa</h2>

      <Table class="w-full border-collapse border border-gray-300">
        <THead>
          <Tr class="bg-gray-100">
            <Th class="border p-2">Especialidad</Th>
            <Th class="border p-2">Periodo Académico</Th>
            <Th class="border p-2">Programa</Th>
            <Th class="border p-2">Unidad de Competencia</Th>
            <Th class="border p-2">Fecha Inicio</Th>
            <Th class="border p-2">Fecha Final</Th>
          </Tr>
        </THead>
        <TBody>
          <tr v-for="(dato, index) in dataCertificate" :key="index">
            <Td class="border p-2">{{ dato.especialidad }}</Td>
            <Td class="border p-2">{{ dato.periodo_academico }}</Td>
            <Td class="border p-2">{{ dato.programa }}</Td>
            <Td class="border p-2">{{ dato.unidad_competencia }}</Td>
            <Td class="border p-2">{{ dato.fecha_inicio }}</Td>
            <Td class="border p-2">{{ dato.fecha_fin }} </Td>
          </tr>
        </TBody>
      </Table>

      <h2 class="text-granate dark:text-granateLight font-black text-4xl mb-4">Unidades Didácticas</h2>
      <Table class="w-full border-collapse border border-gray-300">
        <THead>
          <Tr class="bg-gray-100">
            <Th class="border p-1">N°</Th>
            <Th class="border p-2">Nombre de la Unidad</Th>
            <Th class="border p-2">Capacidad</Th>
            <Th class="border p-2">Créditos</Th>
            <Th class="border p-2">Horas</Th>
            <Th class="border p-2">Condición</Th>
            <Th class="border p-2">Nota</Th>

          </Tr>
        </THead>
        <TBody>
          <Tr v-for="(unidad, i) in dataCertificate[0]?.unidades_didacticas" :key="i">
            <Td class="border p-1">{{ unidad.numero }}</Td>
            <Td class="border p-2">{{ unidad.nombre_unidad }}</Td>
            <Td class="border p-2">{{ unidad.capacidad }}</Td>
            <Td class="border p-2">{{ unidad.credito }}</Td>
            <Td class="border p-2">{{ unidad.hora }}</Td>
            <Td class="border p-2">{{ unidad.condicion }}</Td>

            <Td class="border p-2">
            <span :class="[
              'px-2 py-1 rounded-full',
              unidad.nota <= 10
                ? ' text-red-600  dark:text-red-500 font-bold'
                : ' text-green-600  dark:text-green-300 font-bold'
            ]">
                {{ unidad.nota }}
              </span>
            </Td>

          </Tr>
        </TBody>
      </Table>

      <h2 class="text-granate dark:text-granateLight font-black text-4xl mb-4">Experiencias Formativas</h2>
      <Table class="w-full border-collapse border border-gray-300">
        <THead>
          <Tr class="bg-gray-100">
            <Th class="border p-2">Nombre de la Experiencia</Th>
            <Th class="border p-2">Créditos</Th>
            <Th class="border p-2">Horas</Th>
            <Th class="border p-2">Nota</Th>
          </Tr>
        </THead>
        <TBody>
          <Tr v-for="(exp, j) in dataCertificate[0]?.experiencias_formativas" :key="j">
            <Td class="border p-2">{{ exp.nombre_experiencia }}</Td>
            <Td class="border p-2">{{ exp.creditos_exp }}</Td>
            <Td class="border p-2">{{ exp.horas_exp }}</Td>
            
            <Td class="border p-2"><span :class="[
              'px-2 py-1 rounded-full',
              exp.nota <= 10
                ? ' text-red-600  dark:text-red-500 font-bold'
                : ' text-green-600  dark:text-green-300 font-bold'
            ]">
                {{ exp.nota }}
              </span>
              </Td>
          </Tr>
        </TBody>
      </Table>
    </div>

  </AuthorizationFallback>
</template>
