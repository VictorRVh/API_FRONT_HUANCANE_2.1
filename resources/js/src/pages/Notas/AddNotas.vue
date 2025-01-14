<script setup>
import { defineProps, ref, onMounted, watch } from "vue";
import { useRouter } from "vue-router";

// Componentes de tabla y UI
import Table from "../../components/table/Table.vue";
import THead from "../../components/table/THead.vue";
import TBody from "../../components/table/TBody.vue";
import Tr from "../../components/table/Tr.vue";
import Th from "../../components/table/Th.vue";
import Td from "../../components/table/Td.vue";
import CustomInput from "../../components/ui/FormInput.vue";
import Button from "../../components/ui/Button.vue";
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";

// Composables
import useHttpRequest from "../../composables/useHttpRequest";
import useModalToast from "../../composables/useModalToast";
import useStudentsStore from "../../store/Grupo/useGrupoStore";

// Definir propiedades
const props = defineProps({
  idgroup: { type: Number, default: null },
  idexp: { type: Number, default: null },
  idunit: { type: Number, default: null },
  id: { type: String, default: null },
});

// Configuración inicial
const router = useRouter();
const listNotes = ref([]); // Lista de notas de estudiantes
const isSubmitting = ref(false); // Estado de envío

// Configuración de la URL según el ID
const url = props.id === "657870657269656e636961"
  ? "/registrar_nota_experiencia"
  : "/registrar_notas_unidades";

const { store: createUnit, saving } = useHttpRequest(url);
const { showToast } = useModalToast();
const userStore = useStudentsStore();

// Función para cargar datos del grupo y estudiantes
const loadGroupData = async () => {
  await userStore.loadGroupStudent(props.idgroup);
  listNotes.value = userStore.student.estudiantes.map((element) => ({
    fullName: `${element.estudiante?.name} ${element.estudiante?.apellido_paterno} ${element.estudiante?.apellido_materno}`,
    nota: null,
    [props.id === "657870657269656e636961" ? "id_experiencia" : "id_unidad_didactica"]: props.id === "657870657269656e636961" ? props.idexp : props.idunit ,
    id_estudiante: element.estudiante?.id,
    id_grupo: props.idgroup,
  }));
};

// Validación y envío de las notas
const submitNote = async () => {
  if (isSubmitting.value) return; // Evitar múltiples envíos
  isSubmitting.value = true; // Bloquear mientras se envían los datos

  // Validación de notas
  for (const note of listNotes.value) {
    const parsedNote = parseFloat(note.nota);
    if (note.nota !== null && (isNaN(parsedNote) || parsedNote < 0 || parsedNote > 20)) {
      showToast(
        `La nota para ${note.fullName} debe ser un número entre 0 y 20.`,
        "error"
      );
      isSubmitting.value = false; // Desbloquear en caso de error
      return;
    }
  }

  // Enviar notas al servidor
  try {
    const response = await createUnit({ notas: listNotes.value });
    if (response.status === 201) {
      showToast("Notas guardadas exitosamente", "success");
      router.push({ name: props.id === "657870657269656e636961"? "notasExperience": "notasUnits", params: { id: props.idgroup } });
    } else {
      throw new Error("Error al guardar");
    }
  } catch (error) {
    showToast("Error al guardar notas. Inténtalo de nuevo.", "error");
  } finally {
    isSubmitting.value = false; // Desbloquear después de la solicitud
  }
};

// Cargar datos iniciales
onMounted(loadGroupData);

// Observar cambios en `idgroup` y recargar datos
watch(() => props.idgroup, loadGroupData);
</script>

<template>
  <AuthorizationFallback :permissions="['groups-all', 'groups-view']">
    <div class="w-full space-y-4 py-6">
      <header class="flex justify-between">
        <h2 class="text-black font-bold text-2xl">Estudiantes</h2>
      </header>

      <section class="w-full">
        <!-- Tabla de estudiantes -->
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
              <Td class="py-2 px-4 border-0 text-black ">{{ user?.fullName }}</Td>
              <Td class="py-2 w-[100px] border-0">
                <CustomInput
                  v-model="user.nota"
                  input-class="w-[50px] m-auto text-center"
                  :style="{ color: user.nota !== null && parseFloat(user.nota) <= 10 ? 'red' : 'black' }"
                />
              </Td>
            </Tr>
          </TBody>
        </Table>

        <!-- Botón para guardar -->
        <div class="flex justify-end w-[120px] mt-4">
          <Button
            :title="isSubmitting ? 'Guardando..' : 'Guardar'"
            :loading="isSubmitting"
            :disabled="isSubmitting"
            class="!w-full"
            @click="submitNote"
          />
        </div>
      </section>
    </div>
  </AuthorizationFallback>
</template>

<style scoped>
/* Todo el diseño se maneja con Tailwind */
</style>
