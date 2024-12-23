<script setup>
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
import IndicatorSlider from "../../components/page/Especialidad/IndicadorLogroSlider.vue";

import useIndicatorStore from "../../store/Especialidad/useIndicaroLogroStore";

import useSlider from "../../composables/useSlider";
import useModalToast from "../../composables/useModalToast";
import useHttpRequest from "../../composables/useHttpRequest";

import useRoleStore from "../../store/useRoleStore";
import useUserStore from "../../store/useUserStore";
import useAuth from "../../composables/useAuth";

const props = defineProps({
  idUnidad: {
    type: Number,
    default: null,
  },
});

const router = useRouter();

const userStore = useUserStore();
const roleStore = useRoleStore();
const IndicatorStore = useIndicatorStore();

if (!IndicatorStore.Indicators.length)
  await IndicatorStore.loadIndicators(props.idUnidad);

const { slider, sliderData, showSlider, hideSlider } = useSlider("indicators-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteSpecialy, deleting } = useHttpRequest("/indicador_logro");
const { isUserAuthenticated } = useAuth();

const onDelete = (Indicator) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deleteSpecialy(Indicator?.id_indicador);
    if (isDeleted) {
      showToast(`Indicador "${Indicator?.descripcion}" eliminado correctamente.`);
      IndicatorStore.loadIndicators(props.idUnidad);
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();
    }
  });
};

const SeeMore = (id) => {
  router.push({
    name: "programaFormativo",
    params: { idIndicator: id },
  });
};
</script>

<template>
  <AuthorizationFallback :permissions="['indicators-all', 'indicators-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex justify-between">
        <h2 class="text-black dark:text-white font-bold text-2xl">
          Indicadores de Logro
        </h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <Table class="border-collapse divide-y divide-transparent">
          <THead>
            <Tr>
              <Th>Id</Th>
              <Th>Indicador</Th>
              <Th>Acción</Th>
            </Tr>
          </THead>

          <TBody>
            <Tr
              v-for="Indicator in IndicatorStore.Indicators.unidad_didactica?.indicadores_logro"
              :key="Indicator.id_indicador"
            >
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ Indicator?.id_indicador }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ Indicator?.descripcion }}
              </Td>
              <Td class="py-2 px-4 border-0">
                <div class="flex gap-2 justify-center items-center">
                  <ViewButton @click="SeeMore(Indicator?.id_indicador)" />
                  <EditButton @click="showSlider(true, Indicator)" />
                  <DeleteButton @click="onDelete(Indicator)" />
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>

    <IndicatorSlider
      :UnidadId="props.idUnidad"
      :show="slider"
      :Indicator="sliderData"
      @hide="hideSlider"
    />
  </AuthorizationFallback>
</template>

<style scoped>
/* No se necesita CSS adicional, todo está gestionado con Tailwind */
</style>
