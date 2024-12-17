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
import useSlider from "../../composables/useSlider";
import useModalToast from "../../composables/useModalToast";
import useHttpRequest from "../../composables/useHttpRequest";
import useRoleStore from "../../store/useRoleStore";
import useAuth from "../../composables/useAuth";
import GrupoSlider from "../../components/page/Grupo/GrupoSlider.vue";

import useSpecialtyStore from "../../store/Especialidad/useEspecialidadStore";
import usePlanStore from "../../store/Especialidad/usePlanFormativoStore";
import usePlaceStore from "../../store/Sede/useSedeStore";
import useStudentsStore from "../../store/Estudiante/useStudentStore";

const roleStore = useRoleStore();
let specialtiesStore = ref(null);
let userStore = ref(null);
let placesStore = ref(null);
const selectSpecialties = ref(0);

if (roleStore.role[0].id != 7) {
  specialtiesStore = useSpecialtyStore();
  if (!specialtiesStore.specialties?.length) await specialtiesStore.loadSpecialties();

  placesStore = usePlaceStore();
  if (!placesStore.Places?.length) await placesStore.loadPlaces();

  userStore = useStudentsStore();
  if (!userStore.students?.length) await userStore.loadStudents(7);

  if (specialtiesStore.specialties.length > 0) {
    selectSpecialties.value =
      specialtiesStore.specialties[specialtiesStore.specialties.length - 1]
        .id_especialidad;
  }
}

const planStore = usePlanStore();
if (!planStore.plans?.length) await planStore.loadPlans();

const selectedPlan = ref(0);
if (planStore.plans.length > 0) {
  selectedPlan.value = planStore.plans[planStore.plans.length - 1].id_plan;
}

const router = useRouter();
const groupStore = useGroupsStore();
if (!groupStore.groups?.length) {
  await groupStore.loadGroups(selectedPlan.value, selectSpecialties.value);
}

const { slider, sliderData, showSlider, hideSlider } = useSlider("group-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteSpecialy, deleting } = useHttpRequest("/grupo");
const { isUserAuthenticated } = useAuth();

const onDelete = (group) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deleteSpecialy(group?.id_grupo);
    if (isDeleted) {
      showToast(`Grupo "${group?.nombre_grupo}" eliminado con éxito.`);
      groupStore.loadGroups(selectedPlan.value, selectSpecialties.value);
      roleStore.loadRoles();
      isUserAuthenticated();
    }
  });
};

const SeeMore = (id) => {
  router.push({
    name: "notasGroupEst",
    params: { id: id },
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
        <CreateButton v-if="roleStore.role[0].id != 7" @click="showSlider(true)" />
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

        <select
          id="specialty-select"
          v-if="roleStore.role[0].id != 7"
          @change="changePlan()"
          v-model="selectSpecialties"
          class="border rounded-md p-2"
        >
          <option value="" disabled>Seleccione una especialidad</option>
          <option
            v-for="specialty in specialtiesStore?.specialties"
            :key="specialty?.id_especialidad"
            :value="specialty?.id_especialidad"
          >
            {{ specialty.nombre_especialidad }}
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
              <Th>Acciones</Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="grupo in groupStore.groups" :key="grupo.id_grupo">
              <Td class="py-2 px-4 border-0 text-black">{{ grupo?.id_grupo }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ grupo?.nombre_grupo }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ grupo?.sede.nombre_sede }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ grupo?.plan.nombre_plan }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ grupo?.especialidad.nombre_especialidad }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ grupo?.turno.nombre_turno }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ grupo?.docente.name }}</Td>
              <Td class="py-2 px-4 border-0">
                <div class="flex gap-2 justify-center">
                  <ViewButton @click="SeeMore(grupo?.id_grupo)" />
                  <EditButton @click="showSlider(true, grupo)" />
                  <DeleteButton @click="onDelete(grupo)" />
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>

    <GrupoSlider
      :sedeId="placesStore?.Places?.sedes"
      :turnoId="['M', 'T', 'N']"
      :specialtyId="specialtiesStore?.specialties"
      :planId="planStore.plans"
      :docenteId="userStore?.students"
      :show="slider"
      :group="sliderData"
      :searchId="[selectedPlan, selectSpecialties]"
      @hide="hideSlider"
    />
  </AuthorizationFallback>
</template>

<style scoped>
/* No se requiere CSS adicional, todo está gestionado con Tailwind */
</style>
