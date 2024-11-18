<script setup>
// Importa useRouter de Vue Router
import { useRouter } from "vue-router";

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
import SpecialtySlider from "../../components/page/Especialidad/EspecialidadSlider.vue";

import useSpecialtyStore from "../../store/Especialidad/useEspecialidadStore";
import useSlider from "../../composables/useSlider";
import useModalToast from "../../composables/useModalToast";
import useHttpRequest from "../../composables/useHttpRequest";

import useRoleStore from "../../store/useRoleStore";
import useUserStore from "../../store/useUserStore";
import useAuth from "../../composables/useAuth";

import usePlanStore from "../../store/Especialidad/usePlanFormativoStore";
import { ref } from 'vue'; // Asegúrate de incluir ref

// planStore.plans.plan




const router = useRouter(); // Aquí es donde obtenemos el router

const userStore = useUserStore();
const roleStore = useRoleStore();
const specialtiesStore = useSpecialtyStore();

const planStore = usePlanStore();
const selectedPlan = ref(0);

// specialtyStore.specialties.especialidades

if (!specialtiesStore.specialties.length) await specialtiesStore.loadSpecialties();

const { slider, sliderData, showSlider, hideSlider } = useSlider("specialty-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteSpecialy, deleting } = useHttpRequest("/especialidad");
const { isUserAuthenticated } = useAuth();

const onDelete = (specialty) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deleteSpecialy(specialty?.id_especialidad);
    console.log("pasod eleinar  cosmlas: ", isDeleted);
    if (isDeleted) {
      showToast(`Specialty "${specialty?.nombre_especialidad}" deleted successfully...`);
      specialtiesStore.loadSpecialties();
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();
    }
  });
};

if (!planStore.plans.length) await planStore.loadPlans();

if (planStore.plans.length > 0) {
    selectedPlan.value = planStore.plans[planStore.plans.length - 1].id_plan;
    console.log("Plan seleccionado por defecto:", selectedPlan.value);
  }

const SeeMore = (idr) => {
  console.log("id:", idr, "selectedPlan:", selectedPlan.value);
  if (!selectedPlan.value) {
    showToast("Please select a plan first.");
    return;
  }
  router.push({
    name: "programaFormativo",
    params: { idEspecialidad: idr, idPlan: selectedPlan.value }, // Asegúrate de que estos nombres coincidan
  });
};
</script>

<template>
  <AuthorizationFallback :permissions="['specialties-all', 'specialties-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-active font-bold text-2xl">Specialties</h2>

        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="flex justify-between">

        <select id="plan-select" v-model="selectedPlan" class="border rounded-md p-2">
          <option value="" disabled>Select a plan</option>
          <option
            v-for="plan in planStore.plans"
            :key="plan.id_plan"
            :value="plan.id_plan"
          >
            {{ plan.nombre_plan }}
          </option>
        </select>
      </div>

      <div class="w-full">
        <Table>
          <THead>
            <Tr>
              <Th> Id </Th>
              <Th> Specialties </Th>
              <Th> Action </Th>
            </Tr>
          </THead>

          <TBody>
            <Tr
              v-for="specialty in specialtiesStore.specialties"
              :key="specialty.id_especialidad"
            >
              <Td>{{ specialty?.id_especialidad }}</Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">
                  {{ specialty?.nombre_especialidad }}
                </div>
              </Td>

              <Td class="align-middle">
                <div class="flex flex-row gap-2 justify-center items-center">
                  <ViewButton @click="SeeMore(specialty?.id_especialidad)" />
                  <EditButton @click="showSlider(true, specialty)" />
                  <DeleteButton @click="onDelete(specialty)" />
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>

    <SpecialtySlider :show="slider" :specialty="sliderData" @hide="hideSlider" />
  </AuthorizationFallback>
</template>
