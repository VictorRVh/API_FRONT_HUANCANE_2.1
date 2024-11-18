<script setup>
// archivo unidad_didacticaSlider.vue
import { computed, ref, watch } from "vue";
import Slider from "../../ui/Slider.vue";
import FormInput from "../../ui/FormInput.vue";
import FormLabelError from "../../ui/FormLabelError.vue";
import VSelect from "vue-select";
import Button from "../../ui/Button.vue";
import AuthorizationFallback from "../../../components/page/AuthorizationFallback.vue";

import useUserStore from "../../../store/useUserStore";
import useRoleStore from "../../../store/useRoleStore";
import useUnitsStore from "../../../store/Especialidad/useUnidadesStore";

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
  Unit: {
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
const UnitStore = useUnitsStore();

// Composables
const { store: createUnit, saving, update: updateUnit, updating } = useHttpRequest(
  "/unidad_didactica"
);
const { runYupValidation } = useValidation();
const { showToast } = useModalToast();
const { isUserAuthenticated } = useAuth();

// Computed para manejar permisos
const requiredUnits = computed(() => {
  if (!props.Unit?.id_unidad_didactica) return ["units-all", "units-create"];
  else return ["units-all", "units-edit"];
});

// Computed para el título
const title = computed(() =>
  props.Unit ? `Update Unit "${props.Unit?.nombre_unidad}"` : "Add new Unit"
);

// Inicialización del formulario
const initialFormData = () => ({
  nombre_unidad: null,
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
      if (props.Unit?.id_unidad_didactica) {
        formData.value = { nombre_unidad: props.Unit.nombre_unidad };
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
  nombre_unidad: yup
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
  const response = props.Unit?.id_unidad_didactica
    ? await updateUnit(props.Unit?.id_unidad_didactica, data)
    : await createUnit(data);

  // Si la respuesta es exitosa

  //console.log("response: ", response.unidad.id_unidad_didactica);

  if (response.unidad?.id_unidad_didactica) {
    showToast(
      `Unit ${props.Unit?.id_unidad_didactica ? "updated" : "created"} successfully`
    );

    // Cargar datos actualizados en las tiendas
    UnitStore.loadUnits(props.ProgramId);
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
    <AuthorizationFallback :permissions="requiredUnits">
      <div class="mt-4 space-y-4">
        <FormInput
          v-model="formData.nombre_unidad"
          :focus="show"
          label="Nombre de la unidad_didactica"
          :error="formErrors?.nombre_unidad"
          required
        />

        <Button
          :title="Unit?.id_unidad_didactica ? 'Save' : 'Create'"
          :loading-title="Unit?.id_unidad_didactica ? 'Saving...' : 'Creating...'"
          class="!w-full"
          :loading="saving || updating"
          key="submit-btn"
          @click="onSubmit"
        />
      </div>
    </AuthorizationFallback>
  </Slider>
</template>
