<script setup>
import { defineProps, watch } from "vue";

// Componentes de tabla y UI
import Table from "../../components/table/Table.vue";
import THead from "../../components/table/THead.vue";
import TBody from "../../components/table/TBody.vue";
import Tr from "../../components/table/Tr.vue";
import Th from "../../components/table/Th.vue";
import Td from "../../components/table/Td.vue";
import CreateButton from "../../components/ui/CreateButton.vue";
import EditButton from "../../components/ui/EditButton.vue";
import ViewButton from "../../components/ui/ViewButton.vue";
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";

// Store de estudiantes
import useStudentsStore from "../../store/Grupo/useGrupoStore";

// Definir propiedades
const props = defineProps({
  id: {
    type: Number,
    default: 0,
  },
});

const userStore = useStudentsStore();

// Cargar estudiantes si aún no se han cargado
if (!userStore.student?.length) await userStore.loadGroupStudent(props.id);


console.log(userStore.student)

// Observar cambios en props.id y recargar estudiantes
watch(() => props.id, (newId) => {
  userStore.loadStudents(newId);
});
</script>

<template>
  <AuthorizationFallback :permissions="['groups-all', 'groups-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex-between">
        <h2 class="text-active font-bold text-2xl">
          Estudiantes
        </h2>
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
              <Th>Acción</Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="user in userStore.student" :key="user.id">
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
              <Td class="align-middle">
                <div class="flex flex-row gap-2 justify-center items-center">
                  <ViewButton />
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>
  </AuthorizationFallback>
</template>
