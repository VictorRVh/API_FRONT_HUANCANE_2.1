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
import ExperienciaSlider from "../../components/page/Especialidad/ExperienciaSlider.vue";

import useExperienciasStore from "../../store/Especialidad/useExperienciaFormativa";

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
const experienciasStore = useExperienciasStore();


if (!experienciasStore.experiencias.length) await experienciasStore.loadExperiencias(props.idPrograma);

const { slider, sliderData, showSlider, hideSlider } = useSlider("experiencias-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteSpecialy, deleting } = useHttpRequest("/experiencia_formativa");
const { isUserAuthenticated } = useAuth();

const onDelete = (experiencias) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deleteSpecialy(experiencias?.id_experiencia_formativa);
    console.log("pasod eleinar  cosmlas: ", isDeleted);
    if (isDeleted) {
      showToast(`Experiencias "${experiencias?.nombre_unidad}" deleted successfully...`);
      experienciasStore.loadExperiencias(props.idPrograma);
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();
    }
  });
};

console.log("nuievos Unitses: ", experienciasStore.experiencias);

//console.log("El nombre de la especialidad: ", specialtyStore.specialty);
</script>

<template>
  <AuthorizationFallback :permissions="['units-all', 'units-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-active font-bold text-2xl">{{}} / Experiencias</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <Table>
          <THead>
            <Tr>
              <Th> Id </Th>
              <Th> Experiencia </Th>
              <Th> Action </Th>
            </Tr>
          </THead>

          <TBody>
            <Tr
              v-for="experiencias in experienciasStore.experiencias.experiencias_formativas"
              :key="experiencias.id_experiencia_formativa"
            >
              <Td>{{ experiencias?.id_experiencia_formativa }}</Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">
                  {{ experiencias?.nombre_experiencia }}
                </div>
              </Td>

              <Td class="align-middle">
                <div class="flex flex-row gap-2 justify-center items-center">
                  <EditButton @click="showSlider(true, experiencias)" />
                  <DeleteButton @click="onDelete(experiencias)" />
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>

    <ExperienciaSlider
      :ProgramId="props.idPrograma"
      :show="slider"
      :experiencia="sliderData"
      @hide="hideSlider"
    />
  </AuthorizationFallback>
</template>
