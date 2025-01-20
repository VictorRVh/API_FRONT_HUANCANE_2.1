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
import eyeButton from "../../components/ui/eyeButton.vue";
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";
import UnitsSlider from "../../components/page/Especialidad/UnidadeSlider.vue";

import useUnitsStore from "../../store/Especialidad/useUnidadesStore";

import useSlider from "../../composables/useSlider";
import useModalToast from "../../composables/useModalToast";
import useHttpRequest from "../../composables/useHttpRequest";

import useRoleStore from "../../store/useRoleStore";
import useUserStore from "../../store/useUserStore";
import useAuth from "../../composables/useAuth";

const props = defineProps({
  idPrograma: {
    type: Number,
    default: null,
  },
});

const router = useRouter();

const userStore = useUserStore();
const roleStore = useRoleStore();
const UnitsStore = useUnitsStore();

if (!UnitsStore.Units.length) await UnitsStore.loadUnits(props.idPrograma);

const { slider, sliderData, showSlider, hideSlider } = useSlider("Units-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteSpecialy, deleting } = useHttpRequest("/unidad_didactica");
const { isUserAuthenticated } = useAuth();

const onDelete = (Units) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deleteSpecialy(Units?.id_unidad_didactica);
    if (isDeleted) {
      showToast(`Unidad "${Units?.nombre_unidad}" eliminada correctamente.`);
      UnitsStore.loadUnits(props.idPrograma);
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();
    }
  });
};

const SeeMore = (id) => {
  router.push({
    name: "IndicadorLogro",
    params: { idUnidad: id },
  });
};
</script>

<template>
  <AuthorizationFallback :permissions="['units-all', 'units-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex justify-between">
        <h2 class="text-black dark:text-white font-bold text-2xl">Unidades</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <Table class="border-collapse divide-y divide-transparent">
          <THead>
            <Tr>
              <Th>Id</Th>
              <Th>Unidad</Th>
              <Th>Fecha Inicio</Th>
              <Th>Fecha Final</Th>
              <Th>Créditos</Th>
              <Th>Días</Th>
              <Th>Horas</Th>
              <Th>Acción</Th>
            </Tr>
          </THead>

          <TBody>
            <Tr
              v-for="Units in UnitsStore.Units.unidades_didacticas"
              :key="Units.id_unidad_didactica"
            >
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ Units?.id_unidad_didactica }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ Units?.nombre_unidad }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ Units?.fecha_inicio }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ Units?.fecha_fin }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ Units?.creditos }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ Units?.dias }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ Units?.horas }}
              </Td>
              <Td class="py-2 px-4 border-0">
                <div class="flex gap-2 justify-center items-center">
                  <eye-button @click="SeeMore(Units?.id_unidad_didactica)" />
                  <EditButton @click="showSlider(true, Units)" />
                  <DeleteButton @click="onDelete(Units)" />
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>

    <UnitsSlider
      :ProgramId="props.idPrograma"
      :show="slider"
      :Unit="sliderData"
      @hide="hideSlider"
    />
  </AuthorizationFallback>
</template>

<style scoped>
/* No se necesita CSS adicional, todo está gestionado con Tailwind */
</style>
