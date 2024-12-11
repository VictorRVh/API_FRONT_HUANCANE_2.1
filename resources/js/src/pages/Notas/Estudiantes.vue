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
  id: {
    type: Number,
    default: 0,
  },
});

const userStore = useStudentsStore();
const listUnit = ref([]);
let lengthUnit = 0;
const selectUnit = ref(null);

onMounted(async () => {
  if (!userStore.student?.length) {
    await userStore.loadGroupStudentNote(props.id);
  }
  listUnit.value = userStore.student?.unidades_didacticas || [];
  lengthUnit = userStore.student?.total_unidades
  // Seleccionar la primera unidad por defecto si existe
  if (listUnit.value.length > 0) {
    selectUnit.value = listUnit.value[0].id_unidad_didactica;
  }
  //console.log("unidades cada: ",userStore.student?.unidades_didacticas)
  console.log("unidades lonfiugtus: ",lengthUnit)
});

watch(() => props.id, async (newId) => {
  await userStore.loadGroupStudentNote(newId);
  listUnit.value = userStore.student?.unidades_didacticas || [];
  
  if (listUnit.value.length > 0) {
    selectUnit.value = listUnit.value[0].id_unidad_didactica;
  }
 
});

const seeNote = () => {
  if (selectUnit.value) {
    router.push({
      name: "notasEst",
      params: { idgroup: props.id, idunit: selectUnit.value },
    });
  } else {
    console.error("Por favor, seleccione una unidad antes de continuar.");
  }
};




</script>

<template>
  <AuthorizationFallback :permissions="['groups-all', 'groups-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-active font-bold text-2xl">Estudiantes</h2>
      </div>
      <div class="flex justify-between">
        <select
          id="plan-select"
          v-model="selectUnit"
          class="border rounded-md p-2"
        >
          <option value="" disabled>Select a specialty</option>
          <option
            v-for="unit in listUnit"
            :key="unit?.id_unidad_didactica"
            :value="unit?.id_unidad_didactica"
          >
            {{ unit.nombre_unidad }}
          </option>
        </select>
        <CreateButton @click="seeNote" value="Hazlo" />
      </div>
      <div class="w-full">
        <Table>
          <THead>
            <Tr>
              <Th>Id</Th>
              <Th>Nombre</Th>
              <Th>Apellido Paterno</Th>
              <Th>Apellido Materno</Th>
              <Th>DNI</Th>
              <Th v-for="i in 4" :key="i"> unid</Th>
            </Tr>
          </THead>
          <TBody>
            <Tr v-for="user in userStore.student.estudiantes" :key="user.id">
              <Td>{{ user.estudiante?.id }}</Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">{{ user.estudiante?.name }}</div>
                <div class="text-xsm text-[#aaa]">{{ user.estudiante?.email }}</div>
              </Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">{{ user.estudiante?.apellido_paterno }}</div>
              </Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">{{ user.estudiante?.apellido_materno }}</div>
              </Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">{{ user.estudiante?.dni }}</div>
              </Td>
              <Td class="text-emerald-500 dark:text-emerald-200" v-for="note in user.estudiante?.notas" :key="note.id_nota">
                    {{ note.nota  }}
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>
  </AuthorizationFallback>
</template>
