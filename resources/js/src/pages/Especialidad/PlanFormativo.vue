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

const router = useRouter();

const userStore = useUserStore();
const roleStore = useRoleStore();
const planStore = usePlanStore();

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
    if (isDeleted) {
      showToast(`Plan "${plan?.nombre_plan}" eliminado correctamente.`);
      planStore.loadPlans();
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();
    }
  });
};

const SeeMore = (id) => {};
</script>

<template>
  <AuthorizationFallback :permissions="['plan-all', 'plan-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-black font-bold text-2xl">Planes Formativos</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <Table>
          <THead>
            <Tr class="border-b">
              <Th>Id</Th>
              <Th>Plan</Th>
              <Th>Acción</Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="plan in planStore.plans" :key="plan.id_plan" class="border-b">
              <Td class="text-black border-none">{{ plan?.id_plan }}</Td>
              <Td class="text-black border-none">
                <div class="text-black">{{ plan?.nombre_plan }}</div>
              </Td>
              <Td class="border-none">
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
