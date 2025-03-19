<script setup>
import { useRouter } from "vue-router";
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
import SpecialtySlider from "../../components/page/Especialidad/EspecialidadSlider.vue";

import useSpecialtyStore from "../../store/Especialidad/useEspecialidadStore";
import useSlider from "../../composables/useSlider";
import useModalToast from "../../composables/useModalToast";
import useHttpRequest from "../../composables/useHttpRequest";
import useRoleStore from "../../store/useRoleStore";
import useUserStore from "../../store/useUserStore";
import useAuth from "../../composables/useAuth";
import EyeButton from "../../components/ui/eyeButton.vue";




const router = useRouter();

const specialtiesStore = useSpecialtyStore();




if (!specialtiesStore.specialties.length) await specialtiesStore.loadSpecialties();

const { slider, sliderData, showSlider, hideSlider } = useSlider("specialty-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteSpecialy, deleting } = useHttpRequest("/especialidad");
const { isUserAuthenticated } = useAuth();

const onDelete = (specialty) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deleteSpecialy(specialty?.id_especialidad);
    if (isDeleted) {
      showToast(`Especialidad "${specialty?.nombre_especialidad}" eliminada correctamente.`);
      specialtiesStore.loadSpecialties();

      isUserAuthenticated();
    }
  });
};



const SeeMore = (idr) => {

  router.push({
    name: "programaFormativo",
    params: { idEspecialidad: idr},
  });
};
</script>

<template>
  <AuthorizationFallback :permissions="['specialties-all', 'specialties-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex justify-between">
        <h2 class="text-black dark:text-white font-bold text-2xl">Especialidades</h2>
        <CreateButton @click="showSlider(true)" />
      </div>



      <div class="w-full">
        <Table class="border-collapse divide-y divide-transparent">
          <THead>
            <Tr>
              <Th>Id</Th>
              <Th>Especialidad</Th>
              <Th>Items</Th>
              <Th>Acción</Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="(specialty , index) in specialtiesStore.specialties" :key="specialty.id_especialidad">
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ index+1 }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
                {{ specialty?.nombre_especialidad }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black dark:text-white">
              <div class="flex items-center justify-center space-x-2">
                  <div @click="SeeMore(specialty?.id_especialidad)"

                    class="text-blue-500 hover:text-blue-700 font-semibold cursor-pointer border-b-2 border-transparent hover:border-blue-500"
                  >
                    Programas
                  </div>
              
                </div>
              </Td>
              <Td class="py-2 px-4 border-0">
                <div class="flex gap-2 justify-center items-center">
              
                  <EditButton @click="showSlider(true, specialty)" />
                  <DeleteButton @click="onDelete(specialty)" />
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>

    <SpecialtySlider :show="slider" :specialty="sliderData" @hide="hideSlider" />
  </AuthorizationFallback>
</template>

<style scoped>
/* No se necesita CSS adicional, todo está gestionado con Tailwind */
</style>
