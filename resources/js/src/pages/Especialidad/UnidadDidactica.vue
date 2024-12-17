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
      <div class="flex-between">
        <h2 class="text-black font-bold text-2xl">Unidades</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <Table>
          <THead>
<<<<<<< HEAD
            <Tr>
              <Th> Id </Th>
              <Th> Unitss </Th>
              <Th> Items </Th>
              <Th> Action </Th>
=======
            <Tr class="border-b">
              <Th>Id</Th>
              <Th>Unidad</Th>
              <Th>Acción</Th>
>>>>>>> c10a7e60f17b05ed1a4643dfe74577221f620b3f
            </Tr>
          </THead>

          <TBody>
            <Tr
              v-for="Units in UnitsStore.Units.unidades_didacticas"
              :key="Units.id_unidad_didactica"
              class="border-b"
            >
              <Td class="text-black border-none">{{ Units?.id_unidad_didactica }}</Td>
              <Td class="text-black border-none">
                <div class="text-black">{{ Units?.nombre_unidad }}</div>
              </Td>
<<<<<<< HEAD
              <Td class="px-4 py-2 text-center">
                <div class="flex items-center justify-center space-x-2">
                  <div @click="SeeMore(Units?.id_unidad_didactica)"
                     class="cursor-pointer text-blue-500 hover:text-blue-400 font-semibold border-b-2 border-transparent hover:border-blue-500">
                    Indicadores
                </div>
                </div>
              </Td>

              <Td class="align-middle">
=======
              <Td class="border-none">
>>>>>>> c10a7e60f17b05ed1a4643dfe74577221f620b3f
                <div class="flex flex-row gap-2 justify-center items-center">
                  <ViewButton />
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
