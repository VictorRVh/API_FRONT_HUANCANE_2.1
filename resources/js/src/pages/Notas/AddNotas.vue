<script setup>
import { defineProps, watch, ref, onMounted } from "vue";

// Componentes de tabla y UI
import Table from "../../components/table/Table.vue";
import THead from "../../components/table/THead.vue";
import TBody from "../../components/table/TBody.vue";
import Tr from "../../components/table/Tr.vue";
import Th from "../../components/table/Th.vue";
import Td from "../../components/table/Td.vue";
import CustomInput from "../../components/ui/FormInput.vue";
import SaveButton from "../../components/ui/SaveButton.vue";
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";
import useHttpRequest from "../../composables/useHttpRequest";
import useModalToast from "../../composables/useModalToast";
// Store de estudiantes
import useStudentsStore from "../../store/Grupo/useGrupoStore";

// Definir propiedades
const props = defineProps({
  idgroup: {
    type: Number,
    default: null,
  },
  idunit: {
    type: Number,
    default: null,
  },
  id: {
    type: String,
    default: null,
  },
});

let url = "";
if (props.id == "657870657269656e636961"){
  console.log("consulta a experiencia")
} else {
  url = "/registrar_notas_unidades";
}
const { store: createUnit, saving, update: updateUnit, updating } = useHttpRequest(url);
const { showToast } = useModalToast();

const userStore = useStudentsStore();
const listNotes = ref([]);

// Función para cargar estudiantes y unidades didácticas
const loadGroupData = async () => {
  await userStore.loadGroupStudent(props.idgroup);

  userStore.student.estudiantes.forEach((element) => {
    listNotes.value.push({
      fullName:
        element.estudiante?.name +
        " " +
        element.estudiante?.apellido_paterno +
        " " +
        element.estudiante?.apellido_materno,
      nota: null,
      id_unidad_didactica: props.idunit,
      id_estudiante: element.estudiante?.id,
      id_grupo: props.idgroup,
    });
  });
};

// Llamar a la función en la carga inicial del componente
onMounted(() => {
  loadGroupData();
});
<<<<<<< HEAD
//const onSubmit = async () =>
=======

>>>>>>> c10a7e60f17b05ed1a4643dfe74577221f620b3f
async function submitNote() {
  // Validación de notas
  listNotes.value.forEach((note) => {
    if (note.nota !== null) {
      const parsedNote = parseFloat(note.nota);
      if (isNaN(parsedNote) || parsedNote < 0 || parsedNote > 20) {
        showToast(
          `La nota para ${note.fullName} debe ser un número entre 0 y 20.`,
          "error"
        );
        return;
      }
    }
  });

  const response = await createUnit({
<<<<<<< HEAD
    notas: listNotes.value,
  });

  // Si la respuesta es exitosa

  console.log("response: ", response);

=======
    "notas": listNotes.value
  });

>>>>>>> c10a7e60f17b05ed1a4643dfe74577221f620b3f
  if (response.status === 201) {
    showToast(`Notas guardadas exitosamente`);
  } else {
    showToast("Error al guardar la unidad didáctica. Inténtalo de nuevo.", "error");
  }
}

// Observar cambios en props.idgroup y recargar estudiantes
watch(
  () => props.idgroup,
  () => {
    loadGroupData();
  }
);
</script>

<template>
  <AuthorizationFallback :permissions="['groups-all', 'groups-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex justify-between">
        <h2 class="text-black font-bold text-2xl">Estudiantes</h2>
      </div>

      <div class="w-full">
        <!-- Tabla sin líneas internas -->
        <Table class="border-collapse divide-y divide-transparent">
          <THead>
            <Tr>
              <Th>Id</Th>
              <Th>Nombre Completo</Th>
              <Th>Nota</Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="user in listNotes" :key="user.id_estudiante">
              <Td class="py-2 px-4 border-0 text-black">{{ user?.id_estudiante }}</Td>
              <Td class="py-2 px-4 border-0">
                <div class="text-black font-medium">
                  {{ user?.fullName }}
                </div>
              </Td>
              <Td class="py-2 px-4 border-0">
                <CustomInput
                  v-model="user.nota"
                  label=""
                  placeholder=""
                  :error="null"
                  input-class="w-[50px] m-auto text-center"
                  :style="{
                    color:
                      user.nota !== null && parseFloat(user.nota) <= 10
                        ? 'red'
                        : 'black',
                  }"
                />
              </Td>
            </Tr>
          </TBody>
        </Table>
        <div class="flex justify-end w-full">
          <SaveButton @click="submitNote()" class="w-[150px] m-5 p-5" />
        </div>
      </div>
    </div>
  </AuthorizationFallback>
</template>

<style scoped>
/* No se necesita CSS adicional, todo está manejado con Tailwind */
</style>
