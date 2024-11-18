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
import ProgramSlider from "../../components/page/Especialidad/ProgramaSlider.vue";

import useProgramStore from "../../store/Especialidad/useProgramaStore";

import useSlider from "../../composables/useSlider";
import useModalToast from "../../composables/useModalToast";
import useHttpRequest from "../../composables/useHttpRequest";

import useRoleStore from "../../store/useRoleStore";
import useUserStore from "../../store/useUserStore";
import useAuth from "../../composables/useAuth";

// Obtener el objeto de la ruta actual
const props = defineProps({
  idEspecialidad: {
    type: Number,
    default: null,
  },
  idPlan: {
    typeof: Number,
    default: null,
  },
});
// Acceder al parámetro de la ruta

// Ahora puedes usar `idEspecialidad` en tu componente
//console.log("ruta s", props.idEspecialidad);

// Cargar la especialidad correspondiente cuando se monta el componente

const router = useRouter(); // Aquí es donde obtenemos el router

const userStore = useUserStore();
const roleStore = useRoleStore();
const ProgramStore = useProgramStore();

// ProgramStore.Programs.Program

if (!ProgramStore.Programs.length)
  await ProgramStore.loadPrograms(props.idEspecialidad, props.idPlan);

const { slider, sliderData, showSlider, hideSlider } = useSlider("program-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteSpecialy, deleting } = useHttpRequest("/programa");
const { isUserAuthenticated } = useAuth();

const onDelete = (Program) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deleteSpecialy(Program?.id_programa);
    console.log("pasod eleinar  cosmlas: ", isDeleted);
    if (isDeleted) {
      showToast(`Program "${Program?.nombre_programa}" deleted successfully...`);
      ProgramStore.loadPrograms(props.idEspecialidad, props.idPlan);
      userStore.loadUsers();
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

const SeeMoreExperiencia = (id) => {
  router.push({
    name: "ExperienciaFormativa",
    params: { idPrograma: id },
  });
};

console.log("nuievos Programes: ", ProgramStore.Programs.programas);

//console.log("El nombre de la especialidad: ", specialtyStore.specialty);
</script>

<template>
  <AuthorizationFallback :permissions="['program-all', 'program-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-active font-bold text-2xl">{{}} / Program</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <Table>
          <THead>
            <Tr>
              <Th> Id </Th>
              <Th> Programs </Th>
              <Th> Action </Th>
            </Tr>
          </THead>

          <TBody>
            <Tr
              v-for="Program in ProgramStore.Programs.programas"
              :key="Program.id_programa"
            >
              <Td>{{ Program?.id_programa }}</Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">
                  {{ Program?.nombre_programa }}
                </div>
              </Td>

              <Td class="align-middle">
                <div class="flex flex-row gap-2 justify-center items-center">
                  <ViewButton @click="SeeMore(Program?.id_programa)" />
                  <ViewButton @click="SeeMoreExperiencia(Program?.id_programa)" />
                  <EditButton @click="showSlider(true, Program)" />
                  <DeleteButton @click="onDelete(Program)" />
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>

    <ProgramSlider
      :specialtyId="props.idEspecialidad"
      :planId="props.idPlan"
      :show="slider"
      :program="sliderData"
      @hide="hideSlider"
    />
  </AuthorizationFallback>
</template>
