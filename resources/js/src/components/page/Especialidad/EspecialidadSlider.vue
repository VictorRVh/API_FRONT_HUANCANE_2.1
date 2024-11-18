<script setup>
// archivo EspecialidadSlider.vue
import { computed, ref, watch } from "vue";
import Slider from "../../ui/Slider.vue";
import FormInput from "../../ui/FormInput.vue";
import FormLabelError from "../../ui/FormLabelError.vue";
import VSelect from "vue-select";
import Button from "../../ui/Button.vue";
import AuthorizationFallback from "../../../components/page/AuthorizationFallback.vue";

import useUserStore from "../../../store/useUserStore";
import useRoleStore from "../../../store/useRoleStore";
import useSpecialtiesStore from "../../../store/Especialidad/useEspecialidadStore";

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
  specialty: {
    type: [Object, null],
    default: () => null,
  },
});

// Emitir eventos
const emit = defineEmits(["hide"]);

// Stores
const userStore = useUserStore();
const roleStore = useRoleStore();
const specialtyStore = useSpecialtiesStore();

// Composables
const {
  store: createSpecialty,
  saving,
  update: updateSpecialty,
  updating,
} = useHttpRequest("/especialidad");
const { runYupValidation } = useValidation();
const { showToast } = useModalToast();
const { isUserAuthenticated } = useAuth();

// Computed para manejar permisos
const requiredSpecialties = computed(() => {
  if (!props.specialty?.id_especialidad) return ["specialties-all", "specialties-create"];
  else return ["specialties-all", "specialties-edit"];
});

// Computed para el título
const title = computed(() =>
  props.specialty
    ? `Update specialty "${props.specialty?.nombre_especialidad}"`
    : "Add new specialty"
);

// Inicialización del formulario
const initialFormData = () => ({
  nombre_especialidad: null,
});

// Variables reactivas para los datos del formulario y los errores
const formData = ref(initialFormData());
const formErrors = ref({});

// Función para restablecer el formulario al abrir el modal
watch(
  () => props.show,
  (newValue) => {
    if (newValue) {
      if (props.specialty?.id_especialidad) {
        formData.value = { nombre_especialidad: props.specialty.nombre_especialidad };
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
  nombre_especialidad: yup
    .string()
    .nullable()
    .required("El nombre de la especialidad es obligatorio"),
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

  // Crear o actualizar la especialidad
  const response = props.specialty?.id_especialidad
    ? await updateSpecialty(props.specialty?.id_especialidad, data)
    : await createSpecialty(data);

  // Si la respuesta es exitosa

  console.log("response: ", response.especialidad);

  if (response?.especialidad.id_especialidad) {
    showToast(
      `Specialty ${props.specialty?.id_especialidad ? "updated" : "created"} successfully`
    );

    // Cargar datos actualizados en las tiendas
    specialtyStore.loadSpecialties();
    userStore.loadUsers();
    roleStore.loadRoles();
    isUserAuthenticated();

    // Cerrar el modal
    emit("hide");
  } else {
    showToast("Error al guardar la especialidad. Inténtalo de nuevo.", "error");
  }
};
</script>

<template>
  <Slider :show="show" :title="title" @hide="emit('hide')">
    <AuthorizationFallback :permissions="requiredSpecialties">
      <div class="mt-4 space-y-4">
        <FormInput
          v-model="formData.nombre_especialidad"
          :focus="show"
          label="Nombre de la especialidad"
          :error="formErrors?.nombre_especialidad"
          required
        />

        <Button
          :title="specialty?.id_especialidad ? 'Save' : 'Create'"
          :loading-title="specialty?.id_especialidad ? 'Saving...' : 'Creating...'"
          class="!w-full"
          :loading="saving || updating"
          key="submit-btn"
          @click="onSubmit"
        />
      </div>
    </AuthorizationFallback>
  </Slider>
</template>
