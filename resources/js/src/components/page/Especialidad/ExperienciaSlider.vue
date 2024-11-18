<script setup>
import { computed, ref, watch } from "vue";
import Slider from "../../ui/Slider.vue";
import FormInput from "../../ui/FormInput.vue";
import FormLabelError from "../../ui/FormLabelError.vue";
import VSelect from "vue-select";
import Button from "../../ui/Button.vue";
import AuthorizationFallback from "../../../components/page/AuthorizationFallback.vue";

import useUserStore from "../../../store/useUserStore";
import useRoleStore from "../../../store/useRoleStore";
import useExperienciasStore from "../../../store/Especialidad/useExperienciaFormativa";

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
  experiencia: {
    type: [Object, null],
    default: () => null,
  },
  ProgramId: {
    type: Number,
    default: null,
  },
});

// Emitir eventos
const emit = defineEmits(["hide"]);

// Stores
const userStore = useUserStore();
const roleStore = useRoleStore();
const experienciaStore = useExperienciasStore();

// Composables
const { store: createExperiencia, saving, update: updateExperiencia, updating } = useHttpRequest(
  "/experiencia_formativa"
);
const { runYupValidation } = useValidation();
const { showToast } = useModalToast();
const { isUserAuthenticated } = useAuth();

// Computed para manejar permisos
const requiredExperiencias = computed(() => {
  if (!props.experiencia?.id_unidad_didactica) return ["units-all", "units-create"];
  else return ["units-all", "units-edit"];
});

// Computed para el título
const title = computed(() =>
  props.experiencia ? `Update experiencia "${props.experiencia?.nombre_experiencia}"` : "Add new Experiencia"
);

// Inicialización del formulario
const initialFormData = () => ({
  nombre_experiencia: null,
  id_programa: props.ProgramId,
});

// Variables reactivas para los datos del formulario y los errores
const formData = ref(initialFormData());
const formErrors = ref({});

// Función para restablecer el formulario al abrir el modal
watch(
  () => props.show,
  (newValue) => {
    if (newValue) {
      console.log(props)
      if (props.experiencia?.id_experiencia_formativa) {
        formData.value = { nombre_experiencia: props.experiencia.nombre_experiencia };
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
  nombre_experiencia: yup
    .string()
    .nullable()
    .required("El nombre de la unidad_didactica es obligatorio"),
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

  // Crear o actualizar la unidad_didactica
  const response = props.experiencia?.id_experiencia_formativa
    ? await updateExperiencia(props.experiencia?.id_experiencia_formativa, data)
    : await createExperiencia(data);

  // Si la respuesta es exitosa

  console.log("response: ", response);

  if (response.experiencia?.id_experiencia) {
    showToast(
      `Experiencia ${props.experiencia?.id_experiencia_formativa ? "updated" : "created"} successfully`
    );

    // Cargar datos actualizados en las tiendas
    experienciaStore.loadExperiencias(props.ProgramId);
    userStore.loadUsers();
    roleStore.loadRoles();
    isUserAuthenticated();

    // Cerrar el modal
    emit("hide");
  } else {
    showToast("Error al guardar la unidad_didactica. Inténtalo de nuevo.", "error");
  }
};
</script>

<template>
  <Slider :show="show" :title="title" @hide="emit('hide')">
    <AuthorizationFallback :permissions="requiredExperiencias">
      <div class="mt-4 space-y-4">
        <FormInput
          v-model="formData.nombre_experiencia"
          :focus="show"
          label="Nombre de la experiencia formativa"
          :error="formErrors?.nombre_experiencia"
          required
        />

        <Button
          :title="experiencia?.id_experiencia ? 'Save' : 'Create'"
          :loading-title="experiencia?.id_experiencia ? 'Saving...' : 'Creating...'"
          class="!w-full"
          :loading="saving || updating"
          key="submit-btn"
          @click="onSubmit"
        />
      </div>
    </AuthorizationFallback>
  </Slider>
</template>
