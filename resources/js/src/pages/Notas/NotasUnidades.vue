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

const router = useRouter();
const props = defineProps({
  idUnitNote: {
    type: Number,
    default: 0,
  },
});

const userStore = useStudentsStore();
const listUnit = ref([]);
const lengthUnit = ref(0);
const selectUnit = ref(null);

onMounted(async () => {
  if (!userStore.student?.length) {
    await userStore.loadGroupStudentNote(props.idUnitNote);
  }
  listUnit.value = userStore.student?.unidades_didacticas || [];
  lengthUnit.value = userStore.student?.total_unidades;

  if (listUnit.value.length > 0) {
    selectUnit.value = listUnit.value[0].id_unidad_didactica;
  }
  console.log("victor hola",lengthUnit)
  
});

console.log("victor hola fuera",lengthUnit)


watch(() => props.idUnitNote, async (newId) => {
  await userStore.loadGroupStudentNote(newId);
  listUnit.value = userStore.student?.unidades_didacticas || [];

  if (listUnit.value.length > 0) {
    selectUnit.value = listUnit.value[0].id_unidad_didactica;
  }
 
});

const seeNote = () => {
  if (selectUnit.value) {
    router.push({
      name: "notasEstUnidad",
      params: { idgroup: props.idUnitNote, idUnitNote: selectUnit.value  },
    });
  } else {
    console.error("Por favor, seleccione una unidad antes de continuar.");
  }
};
</script>

<template>
  <AuthorizationFallback :permissions="['groups-all', 'groups-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex justify-between">
        <h2 class="text-black font-bold text-2xl dark:text-white">Estudiantes</h2>
      </div>

      <div class="flex justify-between">
        <select
          id="plan-select"
          v-model="selectUnit"
          class="border rounded-md p-2"
        >
          <option value="" disabled>Seleccione una especialidad</option>
          <option
            v-for="unit in listUnit"
            :key="unit?.id_unidad_didactica"
            :value="unit?.id_unidad_didactica"
          >
            {{ unit.nombre_unidad }}
          </option>
        </select>
        <CreateButton @click="seeNote" value="Ver Notas" />
      </div>

      <div class="w-full">
        <!-- Tabla con eliminación de líneas internas -->
        <Table class="border-collapse divide-y divide-transparent">
          <THead>
            <Tr>
              <Th>Id</Th>
              <Th>Nombre</Th>
              <Th>Apellido Paterno</Th>
              <Th>Apellido Materno</Th>
              <Th>DNI</Th>
              <Th v-for="i in lengthUnit" :key="i">Unidad</Th>
            </Tr>
          </THead>
          <TBody>
            <Tr v-for="user in userStore.student.estudiantes" :key="user.id">
              <Td class="py-2 px-4 border-0 text-black dark:text-white">{{ user.estudiante?.id }}</Td>
              <Td class="py-2 px-4 border-0">
                <div class="text-black font-medium dark:text-white">{{ user.estudiante?.name }}</div>
                <div class="text-sm text-gray-500 dark:text-white">{{ user.estudiante?.email }}</div>
              </Td>
              <Td class="py-2 px-4 border-0 text-black">{{ user.estudiante?.apellido_paterno }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ user.estudiante?.apellido_materno }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ user.estudiante?.dni }}</Td>
              <Td
                class="py-2 px-4 border-0 text-black"
                v-for="note in user.estudiante?.notas"
                :key="note.id_nota"
              >
                {{ note.nota }}
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>
  </AuthorizationFallback>
</template>

<style scoped>
/* No se requiere CSS adicional, todo está gestionado con Tailwind */
</style>
