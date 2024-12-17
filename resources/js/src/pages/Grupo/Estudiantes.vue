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
  },
});

const userStore = useStudentsStore();
const listUnit = ref([]);
const selectUnit = ref(null);

onMounted(async () => {
  if (!userStore.student?.length) {
    await userStore.loadGroupStudent(props.id);
  }
});

watch(() => props.id, async (newId) => {
  await userStore.loadGroupStudent(newId);
});

const seeNote = () => {};
</script>

<template>
  <AuthorizationFallback :permissions="['groups-all', 'groups-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-black font-bold text-2xl">Estudiantes</h2>
      </div>
      <div class="w-full">
        <Table>
          <THead>
            <Tr class="border-b">
              <Th>Id</Th>
              <Th>Nombre</Th>
              <Th>Apellido Paterno</Th>
              <Th>Apellido Materno</Th>
              <Th>DNI</Th>
            </Tr>
          </THead>
          <TBody>
            <Tr
              v-for="user in userStore.student.estudiantes"
              :key="user.id"
              class="border-b"
            >
              <Td class="text-black border-none">{{ user.estudiante?.id }}</Td>
              <Td class="text-black border-none">
                <div class="text-black">{{ user.estudiante?.name }}</div>
                <div class="text-xsm text-[#aaa]">{{ user.estudiante?.email }}</div>
              </Td>
              <Td class="text-black border-none">
                <div class="text-black">{{ user.estudiante?.apellido_paterno }}</div>
              </Td>
              <Td class="text-black border-none">
                <div class="text-black">{{ user.estudiante?.apellido_materno }}</div>
              </Td>
              <Td class="text-black border-none">
                <div class="text-black">{{ user.estudiante?.dni }}</div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>
  </AuthorizationFallback>
</template>
