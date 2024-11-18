<script setup>
// archivo programSlider.vue
import { computed, ref, watch } from "vue";
import Slider from "../../ui/Slider.vue";
import FormInput from "../../ui/FormInput.vue";
import FormLabelError from "../../ui/FormLabelError.vue";
import VSelect from "vue-select";
import Button from "../../ui/Button.vue";
import AuthorizationFallback from "../../../components/page/AuthorizationFallback.vue";

import useUserStore from "../../../store/useUserStore";
import useRoleStore from "../../../store/useRoleStore";
import useProgramStore from "../../../store/Especialidad/useProgramaStore";

import useValidation from "../../../composables/useValidation";
import useHttpRequest from "../../../composables/useHttpRequest";
import useModalToast from "../../../composables/useModalToast";
import useAuth from "../../../composables/useAuth";
import * as yup from "yup";

// Props que recibe el componente
const props = defineProps({
  show: {
    type: Boolean,
    default: () => false,
  },
  program: {
    type: [Object, null],
    default: () => null,
  },
  planId: {
    type: Number,
    default: null,
  },
  specialtyId: {
    type: Number,
    default: null,
  },
});

// Emitir eventos
const emit = defineEmits(["hide"]);

// Stores
const userStore = useUserStore();
const roleStore = useRoleStore();
const programStore = useProgramStore();

// Composables
const { store: createProgram, saving, update: updateProgram, updating } = useHttpRequest(
  "/programa"
);
const { runYupValidation } = useValidation();
const { showToast } = useModalToast();
const { isUserAuthenticated } = useAuth();

// Computed para manejar permisos
const requiredSpecialties = computed(() => {
  if (!props.program?.id_programa) return ["program-all", "program-create"];
  else return ["program-all", "program-edit"];
});

// Computed para el título
const title = computed(() =>
  props.program ? `Update program "${props.program?.nombre_programa}"` : "Add new program"
);

console.log("Los id: ", props.planId, props.specialtyId);
// Inicialización del formulario
const initialFormData = () => ({
  nombre_programa: null,
  id_plan: props.planId,
  id_especialidad: props.specialtyId,
});

// Variables reactivas para los datos del formulario y los errores
const formData = ref(initialFormData());
const formErrors = ref({});

// Función para restablecer el formulario al abrir el modal
watch(
  () => props.show,
  (newValue) => {
    if (newValue) {
      if (props.program?.id_programa) {
        formData.value = { nombre_programa: props.program.nombre_programa };
      } else {
        formData.value = initialFormData();
        formErrors.value = {};
      }
    } else {
      formData.value = initialFormData();
      formErrors.value = {};
    }
  }
);

// Esquema de validación de Yup
const schema = yup.object().shape({
  nombre_programa: yup
    .string()
    .nullable()
    .required("El nombre de la program es obligatorio"),
});

// Función para manejar el envío del formulario
const onSubmit = async () => {
  // Evitar múltiples envíos
  if (saving.value || updating.value) return;

  const data = { ...formData.value };

  // Validar el formulario con Yup
  const { validated, errors } = await runYupValidation(schema, data);
  if (!validated) {
    formErrors.value = errors; // Mostrar los errores
    return;
  }
  formErrors.value = {}; // Limpiar los errores

  // Crear o actualizar la program
  const response = props.program?.id_programa
    ? await updateProgram(props.program?.id_programa, data)
    : await createProgram(data);

  // Si la respuesta es exitosa

  console.log("response fro: ", response.programa?.id_programa);

  if (response.programa?.id_programa) {
    showToast(
      `program ${props.program?.id_programa ? "updated" : "created"} successfully`
    );

    // Cargar datos actualizados en las tiendas
    programStore.loadPrograms(props.specialtyId, props.planId);
    userStore.loadUsers();
    roleStore.loadRoles();
    isUserAuthenticated();

    // Cerrar el modal
    emit("hide");
  } else {
    showToast("Error al guardar la program. Inténtalo de nuevo.", "error");
  }
};
</script>

<template>
  <Slider :show="show" :title="title" @hide="emit('hide')">
    <AuthorizationFallback :permissions="requiredSpecialties">
      <div class="mt-4 space-y-4">
        <FormInput
          v-model="formData.nombre_programa"
          :focus="show"
          label="Nombre del program"
          :error="formErrors?.nombre_programa"
          required
        />

        <Button
          :title="program?.id_programa ? 'Save' : 'Create'"
          :loading-title="program?.id_programa ? 'Saving...' : 'Creating...'"
          class="!w-full"
          :loading="saving || updating"
          key="submit-btn"
          @click="onSubmit"
        />
      </div>
    </AuthorizationFallback>
  </Slider>
</template>
