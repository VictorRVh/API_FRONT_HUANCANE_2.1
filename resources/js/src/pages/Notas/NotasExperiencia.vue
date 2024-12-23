<script setup>
import { defineProps, watch, ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import Table from "../../components/table/Table.vue";
import THead from "../../components/table/THead.vue";
import TBody from "../../components/table/TBody.vue";
import Tr from "../../components/table/Tr.vue";
import Th from "../../components/table/Th.vue";
import Td from "../../components/table/Td.vue";
import CreateButton from "../../components/ui/CreateButton.vue";
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";
import useStudentsStore from "../../store/Grupo/useGrupoStore";

const router = useRouter();
const props = defineProps({
  id: {
    type: Number,
    default: 0,
  }
});

const userStore = useStudentsStore()
const idExp = ref(null);


onMounted(async () => {
  if (!userStore.student?.length) {
    await userStore.loadGroupStudentExperience(props.id);
  }
 // idExp.value = userStore.experiencia_formativa
 // console.log( "experiencia",idExp)

});

const seeNote = () => {
  if (selectUnit.value) {
    router.push({
      name: "notasEst",
      params: { idgroup: props.id, idexp: 23 ,id:"657870657269656e636961" },
    });
  } else {
    console.error("Por favor, seleccione una unidad antes de continuar.");
  }
};




</script>

<template>
  <AuthorizationFallback :permissions="['groups-all', 'groups-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-active font-bold text-2xl">Estudiantes</h2>
      </div>
      <div class="flex justify-between">
        <div></div> 
        <CreateButton @click="seeNote" value="Hazlo" />
      </div>
      <div class="w-full">
        <Table>
          <THead>
            <Tr>
              <Th>Id</Th>
              <Th>Nombre</Th>
              <Th>Apellido Paterno</Th>
              <Th>Apellido Materno</Th>
              <Th>DNI</Th>
              <Th>Nota</Th>
            </Tr>
          </THead>
          <TBody>
            <Tr v-for="user in userStore.student.estudiantes" :key="user.id">
              <Td>{{ user.estudiante?.id }}</Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">{{ user.estudiante?.name }}</div>
                <div class="text-xsm text-[#aaa]">{{ user.estudiante?.email }}</div>
              </Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">{{ user.estudiante?.apellido_paterno }}</div>
              </Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">{{ user.estudiante?.apellido_materno }}</div>
              </Td>
              <Td>
                <div class="text-emerald-500 dark:text-emerald-200">{{ user.estudiante?.dni }}</div>
              </Td>
              <Td class="text-emerald-500 dark:text-emerald-200" >
                    -
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>
  </AuthorizationFallback>
</template>
