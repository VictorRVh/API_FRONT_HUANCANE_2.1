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
import ProgramSlider from "../../components/page/Especialidad/ProgramaSlider.vue";

import useProgramStore from "../../store/Especialidad/useProgramaStore";

import useSlider from "../../composables/useSlider";
import useModalToast from "../../composables/useModalToast";
import useHttpRequest from "../../composables/useHttpRequest";

import useRoleStore from "../../store/useRoleStore";
import useUserStore from "../../store/useUserStore";
import useAuth from "../../composables/useAuth";

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

const router = useRouter();

const userStore = useUserStore();
const roleStore = useRoleStore();
const ProgramStore = useProgramStore();

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
    if (isDeleted) {
      showToast(`Programa "${Program?.nombre_programa}" eliminado correctamente.`);
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
</script>

<template>
  <AuthorizationFallback :permissions="['program-all', 'program-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex justify-between">
        <h2 class="text-black dark:text-white font-bold text-2xl">Programas</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <Table class="border-collapse divide-y divide-transparent">
          <THead>
            <Tr>
              <Th>Id</Th>
              <Th>Programa</Th>
              <Th>Items</Th>
              <Th>Acción</Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="Program in ProgramStore.Programs.programas" :key="Program.id_programa">
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ Program?.id_programa }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ Program?.nombre_programa }}
              </Td>
              <div class="flex items-center justify-center space-x-2">
                  <div @click="SeeMoreExperiencia(Program?.id_programa)"
                   
                    class="text-blue-500 hover:text-blue-700 font-semibold cursor-pointer border-b-2 border-transparent hover:border-blue-500"
                  >
                    Experiencia
                  </div>
                  <span>|</span>
                  <div @click="SeeMore(grupo?.id_grupo)"
    
                    class="text-blue-500 hover:text-blue-700 font-semibold cursor-pointer border-b-2 border-transparent hover:border-blue-500"
                  >
                    Unidades
                  </div>
                </div>
              <Td class="py-2 px-4 border-0">
                <div class="flex gap-2 justify-center items-center">
                  <ViewButton />
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

<style scoped>
/* No se necesita CSS adicional, todo está gestionado con Tailwind */
</style>
