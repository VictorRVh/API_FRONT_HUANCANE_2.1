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
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";
import ExperienciaSlider from "../../components/page/Especialidad/ExperienciaSlider.vue";

import useExperienciasStore from "../../store/Especialidad/useExperienciaFormativa";

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
    if (isDeleted) {
      showToast(`Experiencia "${experiencias?.nombre_experiencia}" eliminada correctamente.`);
      experienciasStore.loadExperiencias(props.idPrograma);
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();
    }
  });
};
</script>

<template>
  <AuthorizationFallback :permissions="['units-all', 'units-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex justify-between">
        <h2 class="text-black dark:text-white font-bold text-2xl">
          Experiencias Formativas
        </h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <Table class="border-collapse divide-y divide-transparent">
          <THead>
            <Tr>
              <Th>Id</Th>
              <Th>Experiencia</Th>
              <Th>Acción</Th>
            </Tr>
          </THead>

          <TBody>
            <Tr
              v-for="(experiencias,index) in experienciasStore.experiencias.experiencias_formativas"
              :key="experiencias.id_experiencia_formativa"
            >
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ index+1 }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ experiencias?.nombre_experiencia }}
              </Td>
              <Td class="py-2 px-4 border-0">
                <div class="flex gap-2 justify-center items-center">
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

<style scoped>
/* No se necesita CSS adicional, todo está gestionado con Tailwind */
</style>
