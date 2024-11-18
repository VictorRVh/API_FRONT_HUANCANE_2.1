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
import IndicatorSlider from "../../components/page/Especialidad/IndicadorLogroSlider.vue";

import useIndicatorStore from "../../store/Especialidad/useIndicaroLogroStore";

import useSlider from "../../composables/useSlider";
import useModalToast from "../../composables/useModalToast";
import useHttpRequest from "../../composables/useHttpRequest";

import useRoleStore from "../../store/useRoleStore";
import useUserStore from "../../store/useUserStore";
import useAuth from "../../composables/useAuth";

// Obtener el objeto de la ruta actual
const props = defineProps({
  idUnidad: {
    type: Number,
    default: null,
  },
});
// Acceder al parámetro de la ruta

// Ahora puedes usar `idUnidad` en tu componente
console.log("ruta  Unidad", props.idUnidad);

// Cargar la especialidad correspondiente cuando se monta el componente

const router = useRouter(); // Aquí es donde obtenemos el router

const userStore = useUserStore();
const roleStore = useRoleStore();
const IndicatorStore = useIndicatorStore();

// IndicatorStore.Indicators.Indicator

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
    //console.log("pasod eleinar  cosmlas: ", isDeleted);
    if (isDeleted) {
      showToast(`Indicator "${Indicator?.descripcion}" deleted successfully...`);
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

console.log(
  "nuevos Indicatores: ",
  IndicatorStore.Indicators.unidad_didactica?.indicadores_logro
);
//console.log("El nombre de la especialidad: ", specialtyStore.specialty);
</script>

<template>
  <AuthorizationFallback :permissions="['indicators-all', 'indicators-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-active font-bold text-2xl">{{}} / Indicator</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <Table>
          <THead>
            <Tr>
              <Th> Id </Th>
              <Th> Indicators </Th>
              <Th> Action </Th>
            </Tr>
          </THead>

          <TBody>
            <Tr
              v-for="Indicator in IndicatorStore.Indicators.unidad_didactica
                ?.indicadores_logro"
              :key="Indicator.id_indicador"
            >
              <Td>{{ Indicator?.id_indicador }}</Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">
                  {{ Indicator?.descripcion }}
                </div>
              </Td>

              <Td class="align-middle">
                <div class="flex flex-row gap-2 justify-center items-center">
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
