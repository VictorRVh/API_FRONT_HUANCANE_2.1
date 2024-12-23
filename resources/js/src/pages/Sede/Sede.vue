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
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";
import PlaceSlider from "../../components/page/Sede/SedeSlider.vue";

import usePlaceStore from "../../store/Sede/useSedeStore";
import useSlider from "../../composables/useSlider";
import useModalToast from "../../composables/useModalToast";
import useHttpRequest from "../../composables/useHttpRequest";

import useRoleStore from "../../store/useRoleStore";
import useUserStore from "../../store/useUserStore";
import useAuth from "../../composables/useAuth";

import { ref } from 'vue';

const router = useRouter();
const userStore = useUserStore();
const roleStore = useRoleStore();
const placesStore = usePlaceStore();

if (!placesStore.Places.length) await placesStore.loadPlaces();

const { slider, sliderData, showSlider, hideSlider } = useSlider("Place-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteSpecialy, deleting } = useHttpRequest("/sedes");
const { isUserAuthenticated } = useAuth();

const onDelete = (Place) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deleteSpecialy(Place?.id_sede);
    if (isDeleted) {
      showToast(`Place "${Place?.nombre_sede}" deleted successfully...`);
      placesStore.loadPlaces();
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();
    }
  });
};

const SeeMore = (idr) => {
  console.log(`Viewing details for Place ID: ${idr}`);
};
</script>

<template>
  <AuthorizationFallback :permissions="['places-all', 'places-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex justify-between">
        <h2 class="text-black font-bold text-2xl dark:text-white">Sede</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="w-full">
        <!-- Aplicación de Tailwind para eliminar líneas internas -->
        <Table class="border-collapse divide-y divide-transparent">
          <THead>
            <Tr>
              <Th> Id </Th>
              <Th> Place </Th>
              <Th> Ubicación </Th>
              <Th> Action </Th>
            </Tr>
          </THead>

          <TBody>
            <Tr
              v-for="Place in placesStore.Places.sedes"
              :key="Place.id_sede"
            >
              <Td class="py-2 px-4 border-0 text-black">{{ Place?.id_sede }}</Td>
              <Td class="py-2 px-4 border-0">
                <div class="text-black font-medium dark:text-white">
                  {{ Place?.nombre_sede }}
                </div>
              </Td>
              <Td class="py-2 px-4 border-0">
                <div class="text-black font-medium dark:text-white">
                  {{ Place?.ubicacion }}
                </div>
              </Td>
              <Td class="py-2 px-4 border-0">
                <div class="flex flex-row gap-2 justify-center items-center ">
                  <ViewButton @click="SeeMore(Place?.id_sede)" />
                  <EditButton @click="showSlider(true, Place)" />
                  <DeleteButton @click="onDelete(Place)" />
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>

    <PlaceSlider :show="slider" :place="sliderData" @hide="hideSlider" />
  </AuthorizationFallback>
</template>

<style scoped>
/* No se necesita CSS adicional, se utiliza Tailwind */
</style>
