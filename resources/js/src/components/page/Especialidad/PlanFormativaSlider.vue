<script setup>
// archivo planSlider.vue
import { computed, ref, watch } from "vue";
import Slider from "../../ui/Slider.vue";
import FormInput from "../../ui/FormInput.vue";
import FormLabelError from "../../ui/FormLabelError.vue";
import VSelect from "vue-select";
import Button from "../../ui/Button.vue";
import AuthorizationFallback from "../../../components/page/AuthorizationFallback.vue";

import useUserStore from "../../../store/useUserStore";
import useRoleStore from "../../../store/useRoleStore";
import usePlanStore from "../../../store/Especialidad/usePlanFormativoStore";

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
  plan: {
    type: [Object, null],
    default: () => null,
  },
});

// Emitir eventos
const emit = defineEmits(["hide"]);

// Stores
const userStore = useUserStore();
const roleStore = useRoleStore();
const planStore = usePlanStore();

// Composables
const { store: createPlan, saving, update: updatePlan, updating } = useHttpRequest(
  "/plan"
);
const { runYupValidation } = useValidation();
const { showToast } = useModalToast();
const { isUserAuthenticated } = useAuth();

// Computed para manejar permisos
const requiredSpecialties = computed(() => {
  if (!props.plan?.id_plan) return ["plan-all", "plan-create"];
  else return ["plan-all", "plan-edit"];
});

// Computed para el título
const title = computed(() =>
  props.plan ? `Actualizar plan "${props.plan?.nombre_plan}"` : "Agregar nuevo plan"
);

// Inicialización del formulario
const initialFormData = () => ({
  nombre_plan: null,
});

// Variables reactivas para los datos del formulario y los errores
const formData = ref(initialFormData());
const formErrors = ref({});

// Función para restablecer el formulario al abrir el modal
watch(
  () => props.show,
  (newValue) => {
    if (newValue) {
      if (props.plan?.id_plan) {
        formData.value = { nombre_plan: props.plan.nombre_plan };
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
  nombre_plan: yup.string().nullable().required("El nombre de la plan es obligatorio"),
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
      formErrors.value = errors; // Mostrar errores en el formulario
      return;
    }
    formErrors.value = {}; // Limpiar errores previos

    // Crear o actualizar el plan
    const response = props.plan?.id_plan
      ? await updatePlan(props.plan?.id_plan, data)
      : await createPlan(data);

    console.log("response: ", response.plan);

    // Verificar si la respuesta es exitosa
    if (response?.plan?.id_plan) {
      showToast(`Plan ${props.plan?.id_plan ? "actualizado" : "creado"} con exíto`);

      // Cargar datos actualizados en las stores necesarias
      planStore.loadPlans();
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();

      // Cerrar el modal o formulario
      emit("hide");
    } else {
      showToast("Error al guardar el plan. Inténtalo de nuevo.", "error");
    }

  } catch (error) {
    console.error("Error en onSubmit plan:", error);
    showToast("Ocurrió un error inesperado al guardar el plan.", "error");

  } finally {
    // Desactivamos los flags de loading
    saving.value = false;
    updating.value = false;
  }
};

</script>

<template>
  <Slider :show="show" :title="title" @hide="emit('hide')">
    <AuthorizationFallback :permissions="requiredSpecialties">
      <div class="mt-4 space-y-4">
        <FormInput v-model="formData.nombre_plan" :focus="show" label="Nombre de la plan"
          :error="formErrors?.nombre_plan" required />

        <Button :title="plan?.id_plan ? 'Actualizar' : 'Crear'"
          :loading-title="plan?.id_plan ? 'Actualizando...' : 'Creando...'" class="!w-full" :loading="saving || updating"
          key="submit-btn" @click="onSubmit" />
      </div>
    </AuthorizationFallback>
  </Slider>
</template>
