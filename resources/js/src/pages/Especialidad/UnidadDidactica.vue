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

// Obtener el objeto de la ruta actual
const props = defineProps({
  idPrograma: {
    type: Number,
    default: null,
  },
});
// Acceder al parámetro de la ruta

// Ahora puedes usar `idPrograma` en tu componente
console.log("ruta s", props.idPrograma);

// Cargar la especialidad correspondiente cuando se monta el componente

const router = useRouter(); // Aquí es donde obtenemos el router

const userStore = useUserStore();
const roleStore = useRoleStore();
const UnitsStore = useUnitsStore();

// UnitsStore.Units.Units

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
    console.log("pasod eleinar  cosmlas: ", isDeleted);
    if (isDeleted) {
      showToast(`Units "${Units?.nombre_unidad}" deleted successfully...`);
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

console.log("nuievos Unitses: ", UnitsStore.Units.unidades_didacticas);

//console.log("El nombre de la especialidad: ", specialtyStore.specialty);
</script>

<template>
  <AuthorizationFallback :permissions="['units-all', 'units-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-active font-bold text-2xl">{{}} / Units</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <Table>
          <THead>
            <Tr>
              <Th> Id </Th>
              <Th> Unitss </Th>
              <Th> Action </Th>
            </Tr>
          </THead>

          <TBody>
            <Tr
              v-for="Units in UnitsStore.Units.unidades_didacticas"
              :key="Units.id_unidad_didactica"
            >
              <Td>{{ Units?.id_unidad_didactica }}</Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">
                  {{ Units?.nombre_unidad }}
                </div>
              </Td>

              <Td class="align-middle">
                <div class="flex flex-row gap-2 justify-center items-center">
                  <ViewButton @click="SeeMore(Units?.id_unidad_didactica)" />
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
