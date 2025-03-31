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
import useIndicatorsStore from "../../../store/Especialidad/useIndicaroLogroStore";

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
  Indicator: {
    type: [Object, null],
    default: () => null,
  },
  UnidadId: {
    type: Number,
    default: null,
  },
});

// Emitir eventos
const emit = defineEmits(["hide"]);

// Stores
const userStore = useUserStore();
const roleStore = useRoleStore();
const IndicatorStore = useIndicatorsStore();

// Composables
const {
  store: createIndicator,
  saving,
  update: updateIndicator,
  updating,
} = useHttpRequest("/indicador_logro");
const { runYupValidation } = useValidation();
const { showToast } = useModalToast();
const { isUserAuthenticated } = useAuth();

// Computed para manejar permisos
const requiredIndicators = computed(() => {
  if (!props.Indicator?.id_indicador) return ["indicators-all", "indicators-create"];
  else return ["indicators-all", "indicators-edit"];
});

// Computed para el título
const title = computed(() =>
  props.Indicator
    ? `Actualizar indicador de logro "${props.Indicator?.descripcion}"`
    : "Agregar indicador de logro"
);

// Inicialización del formulario
const initialFormData = () => ({
  descripcion: null,
  id_unidad_didactica: props.UnidadId,
});

// Variables reactivas para los datos del formulario y los errores
const formData = ref(initialFormData());
const formErrors = ref({});

//console.log("props LLEgado ed form ", props.UnidadId);

// Función para restablecer el formulario al abrir el modal
watch(
  () => props.show,
  (newValue) => {
    if (newValue) {
      if (props.Indicator?.id_indicador) {
        formData.value = { descripcion: props.Indicator.descripcion };
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
  descripcion: yup
    .string()
    .nullable()
    .required("El nombre del indicador de logro es obligatorio"),
});

// Función para manejar el envío del formulario
const onSubmit = async () => {
  // Evitar múltiples envíos
  if (saving.value || updating.value) return;

  try {
    // Activamos el flag de saving
    saving.value = true;

    const data = { ...formData.value };

    // Validar el formulario con Yup
    const { validated, errors } = await runYupValidation(schema, data);
    if (!validated) {
      formErrors.value = errors; // Mostrar los errores de validación
      return;
    }
    formErrors.value = {}; // Limpiar errores previos

    // Crear o actualizar el indicador
    const response = props.Indicator?.id_indicador
      ? await updateIndicator(props.Indicator?.id_indicador, data)
      : await createIndicator(data);

    console.log("response Indi: ", response.indicador?.id_indicador);

    // Si la respuesta es exitosa
    if (response.indicador?.id_indicador) {
      showToast(
        `Indicador de logro ${props.Indicator?.id_indicador ? "actualizado" : "creado"} correctamente.`
      );

      // Recargar datos en las stores
      IndicatorStore.loadIndicators(props.UnidadId);
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();

      // Cerrar el modal o formulario
      emit("hide");
    } else {
      showToast("Error al guardar el indicador. Inténtalo de nuevo.", "error");
    }

  } catch (error) {
    console.error("Error en onSubmit Indicator:", error);
    showToast("Ocurrió un error inesperado al guardar el indicador.", "error");

  } finally {
    // Desactivar flags de carga
    saving.value = false;
    updating.value = false;
  }
};

</script>

<template>
  <Slider :show="show" :title="title" @hide="emit('hide')">
    <AuthorizationFallback :permissions="requiredIndicators">
      <div class="mt-4 space-y-4">
        <FormInput
          v-model="formData.descripcion"
          :focus="show"
          label="Nombre del indicador de logro"
          :error="formErrors?.descripcion"
          required
        />

        <Button
          :title="Indicator?.id_indicador ? 'Actualizar' : 'Crear'"
          :loading-title="Indicator?.id_indicador ? 'Actualizando...' : 'Creando...'"
          class="!w-full"
          :loading="saving || updating"
          key="submit-btn"
          @click="onSubmit"
        />
      </div>
    </AuthorizationFallback>
  </Slider>
</template>
