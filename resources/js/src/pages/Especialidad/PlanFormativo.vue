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
import PlanSlider from "../../components/page/Especialidad/PlanFormativaSlider.vue";

import usePlanStore from "../../store/Especialidad/usePlanFormativoStore";

import useSlider from "../../composables/useSlider";
import useModalToast from "../../composables/useModalToast";
import useHttpRequest from "../../composables/useHttpRequest";

import useRoleStore from "../../store/useRoleStore";
import useUserStore from "../../store/useUserStore";
import useAuth from "../../composables/useAuth";

// Obtener el objeto de la ruta actual

// Cargar la especialidad correspondiente cuando se monta el componente

const router = useRouter(); // Aquí es donde obtenemos el router

const userStore = useUserStore();
const roleStore = useRoleStore();
const planStore = usePlanStore();

// planStore.plans.plan

if (!planStore.plans.length) await planStore.loadPlans();

const { slider, sliderData, showSlider, hideSlider } = useSlider("plan-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteSpecialy, deleting } = useHttpRequest("/plan");
const { isUserAuthenticated } = useAuth();

const onDelete = (plan) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deleteSpecialy(plan?.id_plan);
    console.log("pasod eleinar  cosmlas: ", isDeleted);
    if (isDeleted) {
      showToast(`plan "${plan?.nombre_plan}" deleted successfully...`);
      planStore.loadPlans();
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();
    }
  });
};

const SeeMore = (id) => {
 
};

//console.log("El nombre de la especialidad: ", specialtyStore.specialty);
</script>

<template>
  <AuthorizationFallback :permissions="['plan-all', 'plan-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-active font-bold text-2xl">{{}} / Plan</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <Table>
          <THead>
            <Tr>
              <Th> Id </Th>
              <Th> plans </Th>
              <Th> Action </Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="plan in planStore.plans" :key="plan.id_plan">
              <Td>{{ plan?.id_plan }}</Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">
                  {{ plan?.nombre_plan }}
                </div>
              </Td>

              <Td class="align-middle">
                <div class="flex flex-row gap-2 justify-center items-center">
                  <ViewButton @click="SeeMore(plan?.id_plan)" />
                  <EditButton @click="showSlider(true, plan)" />
                  <DeleteButton @click="onDelete(plan)" />
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>

    <PlanSlider :show="slider" :plan="sliderData" @hide="hideSlider" />
  </AuthorizationFallback>
</template>
