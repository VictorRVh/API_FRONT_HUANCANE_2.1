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
  idExperiencie: {
    type: String,
    default: 0,
  }
});

const userStore = useStudentsStore()
const idExp = ref(null);
const showBtn = ref(null);

onMounted(async () => {
  if (!userStore.student?.length) {
    await userStore.loadGroupStudentExperience(props.idExperiencie);
  }
  idExp.value = userStore.student?.experiencia_formativa[0]?.id_experiencia
  showBtn.value = userStore.student?.experiencia_formativa[0].nota_asignada;
  //console.log("hola : sacateañl ",userStore.student?.experiencia_formativa[0].nota_asignada  )
  console.log("hola : sacateañl ", userStore?.student?.experiencia_formativa)

});

const seeNote = () => {
  if (idExp.value) {
    router.push({
      name: "notasEst",
      params: { idgroup: props.idExperiencie, idExperiencie: idExp.value, idType: "657870657269656e636961" },
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
        <CreateButton v-if="!showBtn" @click="seeNote" value="Hazlo" />
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
            <Tr v-for="(user, index) in userStore.student.estudiantes" :key="user.id">
              <Td class="py-2 px-4 border-0 text-black dark:text-white">{{ index + 1 }}</Td>
              <Td class="py-2 px-4 border-0">
                <div class="text-black font-medium dark:text-white">{{ user.estudiante?.name }}</div>
                <div class="text-sm text-gray-500 dark:text-white">{{ user.estudiante?.email }}</div>
              </Td>
              <Td class="py-2 px-4 border-0 text-black">{{ user.estudiante?.apellido_paterno }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ user.estudiante?.apellido_materno }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ user.estudiante?.dni }}</Td>
              <Td class="py-2 px-4 border-0 text-black" v-for="note in user.estudiante?.notas_experiencia_formativa"
                :key="note.id_nota_experiencia">
                <span :class="[
                  'px-2 py-1 rounded-full',
                  note.nota_experiencia <= 10
                    ? ' text-red-600  dark:text-red-500 font-bold'
                    : ' text-green-600  dark:text-green-300 font-bold'
                ]">
                  {{ note.nota_experiencia }}
                </span>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>
  </AuthorizationFallback>
</template>
