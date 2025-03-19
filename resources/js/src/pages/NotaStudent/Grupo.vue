<script setup>
import { useRouter } from "vue-router";
import { onMounted, ref } from "vue";
import Table from "../../components/table/Table.vue";
import THead from "../../components/table/THead.vue";
import TBody from "../../components/table/TBody.vue";
import Tr from "../../components/table/Tr.vue";
import Th from "../../components/table/Th.vue";
import Td from "../../components/table/Td.vue";
import CreateButton from "../../components/ui/CreateButton.vue";
import EditButton from "../../components/ui/EditButton.vue";
import DeleteButton from "../../components/ui/DeleteButton.vue";
import ViewButton from "../../components/ui/ViewButton.vue";
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";

import useGroupsStore from "../../store/Grupo/useGrupoStore";
import useModalToast from "../../composables/useModalToast";
import useRoleStore from "../../store/useRoleStore";
import usePlanStore from "../../store/Especialidad/usePlanFormativoStore";



const roleStore = useRoleStore();
let specialtiesStore = ref(null);
let userStore = ref(null);
let placesStore = ref(null);
const selectSpecialties = ref(0);
//roleStore.role[0].id

const planStore = usePlanStore();
if (!planStore.plans?.length) await planStore.loadPlans();

const selectedPlan = ref(0);
if (planStore.plans.length > 0) {
  selectedPlan.value = planStore.plans[planStore.plans.length - 1].id_plan;
}

const router = useRouter();
const groupStore = useGroupsStore();

groupStore.resetStore();


if (!groupStore.groups?.length) {
  await groupStore.loadGroups(selectedPlan.value, selectSpecialties.value);
}




const noteAll = (id) => {
  router.push({
    name: "noteByStudent",
    params: { idNoteStudent: id },
  });
};

const changePlan = () => {
  groupStore.loadGroups(selectedPlan.value, selectSpecialties.value);
};
</script>

<template>
  <AuthorizationFallback :permissions="['groups-all', 'groups-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex justify-between">
        <h2 class="text-black font-bold text-2xl">Grupos</h2>
        
      </div>

      <!-- Selector de plan y especialidad -->
      <div class="flex justify-between">
        <select
          id="plan-select"
          @change="changePlan()"
          v-model="selectedPlan"
          class="border rounded-md p-2"
        >
          <option value="" disabled>Seleccione un plan</option>
          <option
            v-for="plan in planStore.plans"
            :key="plan.id_plan"
            :value="plan.id_plan"
          >
            {{ plan.nombre_plan }}
          </option>
        </select>

  
      </div>

      <!-- Tabla -->
      <div class="w-full">
        <Table class="border-collapse divide-y divide-transparent">
          <THead>
            <Tr>
              <Th>Id</Th>
              <Th>Grupos</Th>
              <Th>Sede</Th>
              <Th>Plan</Th>
              <Th>Especialidad</Th>
              <Th>Turno</Th>
              <Th>Docente</Th>
              <Th>Notas</Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="grupo in groupStore.groups" :key="grupo.id_grupo">
              <Td class="py-2 px-4 border-0 text-black">{{ grupo?.id_grupo }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ grupo?.nombre_grupo }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ grupo?.sede.nombre_sede }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ grupo?.plan.nombre_plan }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{
                grupo?.especialidad.nombre_especialidad
              }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{
                grupo?.turno.nombre_turno
              }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ grupo?.docente.name }}</Td>

              <Td class="px-4 py-2 text-center">
                <div class="flex items-center justify-center space-x-2">
                  <div @click="noteAll(grupo?.id_grupo)"
                   
                    class="text-blue-500 hover:text-blue-700 font-semibold cursor-pointer border-b-2 border-transparent hover:border-blue-500"
                  >
                    Nota
                  </div>
                
              
                </div>
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
