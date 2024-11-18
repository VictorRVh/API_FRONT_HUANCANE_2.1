<script setup>
// Importa useRouter de Vue Router
import { useRouter } from "vue-router";
import { onMounted } from "vue";
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

import { ref } from "vue"; // Asegúrate de incluir ref



const roleStore = useRoleStore();
console.log("Roles victor: ",roleStore.role[0].id)

let specialtiesStore;
let userStore;
let placesStore;

const selectSpecialties = ref(0);


if (roleStore.role[0].id) {
  specialtiesStore = useSpecialtyStore();
  if (!specialtiesStore.specialties?.length) await specialtiesStore.loadSpecialties();
  placesStore = usePlaceStore();
  if (!placesStore.Places?.length) await placesStore.loadPlaces();
  userStore = useStudentsStore();
  if (!userStore.students?.length) await userStore.loadStudents("7");

  if (specialtiesStore.specialties.length > 0) {
    selectSpecialties.value =
      specialtiesStore.specialties[
        specialtiesStore.specialties.length - 1
      ].id_especialidad;
    // console.log("Especilidad seleccionado por defecto:", selectSpecialties.value);
  }
  // Cargar la especialidad correspondiente cuando se monta el componente
}

const planStore = usePlanStore();
// planStore.plans.plan
if (!planStore.plans?.length) await planStore.loadPlans();

// pruebas de consulta

const selectedPlan = ref(0);
if (planStore.plans.length > 0) {
  selectedPlan.value = planStore.plans[planStore.plans.length - 1].id_plan;
  //console.log("Plan seleccionado por defecto:", selectedPlan.value);
}

const router = useRouter(); // Aquí es donde obtenemos el router

const groupStore = useGroupsStore();


if (!groupStore.groups?.length)
  await groupStore.loadGroups(selectedPlan.value, selectSpecialties.value);

const { slider, sliderData, showSlider, hideSlider } = useSlider("group-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteSpecialy, deleting } = useHttpRequest("/grupo");
const { isUserAuthenticated } = useAuth();

const onDelete = (group) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deleteSpecialy(group?.id_grupo);
    // console.log("pasod eleinar  cosmlas: ", isDeleted);
    if (isDeleted) {
      showToast(`Grupo "${group?.nombre_grupo}" deleted successfully...`);
      groupStore.loadGroups(selectedPlan.value, selectSpecialties.value);
      roleStore.loadRoles();
      isUserAuthenticated();
    }
  });
};

const SeeMore = (id) => {
  router.push({
    name: "UnidadDidactica",
    params: { idPrograma: id },
  });
};

//console.log(groupStore.groups);

//console.log("nuievos Programes: ", groupStore);

//console.log("El nombre de la especialidad: ", specialtyStore.specialty);
const changePlan = () => {
  groupStore.loadGroups(selectedPlan.value, selectSpecialties.value);
  //console.log("usuario rol : ", userStoreOne.user.roles[0].id);
};
</script>

<template>
  <AuthorizationFallback :permissions="['groups-all', 'groups-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-active font-bold text-2xl">{{}} / Grupos</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <!-- selecionar Periodo Academico-->
      <div class="flex justify-between">
        <select
          id="plan-select"
          @change="changePlan()"
          v-model="selectedPlan"
          class="border rounded-md p-2"
        >
          <option value="" disabled>Select a plan</option>
          <option
            v-for="plan in planStore.plans"
            :key="plan.id_plan"
            :value="plan.id_plan"
          >
            {{ plan.nombre_plan }}
          </option>
        </select>

        <!-- selecionar especialidad v-if="userStoreOne.user.roles.id===7" -->

        <select
          id="plan-select"
          @change="changePlan()"
          v-model="selectSpecialties"
          class="border rounded-md p-2"
        >
          <option value="" disabled>Select a specialties</option>
          <option
            v-for="specialty in specialtiesStore.specialties"
            :key="specialty.id_especialidad"
            :value="specialty.id_especialidad"
          >
            {{ specialty.nombre_especialidad }}
          </option>
        </select>
      </div>

      <div class="w-full">
        <Table>
          <THead>
            <Tr>
              <Th> Id </Th>
              <Th> Grupos </Th>
              <Th> Sede </Th>
              <Th> Plan </Th>
              <Th> Especialidad </Th>
              <Th> Turno </Th>
              <Th> Docente </Th>
              <Th> Action </Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="grupo in groupStore.groups" :key="grupo.id_grupo">
              <Td>{{ grupo?.id_grupo }}</Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">
                  {{ grupo?.nombre_grupo }}
                </div>
              </Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">
                  {{ grupo?.sede.nombre_sede }}
                </div>
              </Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">
                  {{ grupo?.plan.nombre_plan }}
                </div>
              </Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">
                  {{ grupo?.especialidad.nombre_especialidad }}
                </div>
              </Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">
                  {{ grupo?.turno.nombre_turno }}
                </div>
              </Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">
                  {{ grupo?.docente.name }}
                </div>
              </Td>

              <Td class="align-middle">
                <div class="flex flex-row gap-2 justify-center items-center">
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
      :sedeId="placesStore.Places.sedes"
      :turnoId="['M', 'T', 'N']"
      :specialtyId="specialtiesStore.specialties"
      :planId="planStore.plans"
      :docenteId="userStore.students"
      :show="slider"
      :group="sliderData"
      :searchId="[selectedPlan, selectSpecialties]"
      @hide="hideSlider"
    />
  </AuthorizationFallback>
</template>
