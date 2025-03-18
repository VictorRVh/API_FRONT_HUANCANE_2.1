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
  fecha_inicio:null,
  fecha_fin:null,
  creditos:null,
  dias:null,
  horas:null,
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
        formData.value = { 
          nombre_experiencia: props.experiencia.nombre_experiencia,
          fecha_inicio:props.experiencia.fecha_inicio,
          fecha_fin:props.experiencia.fecha_fin,
          creditos:props.experiencia.creditos,
          dias:props.experiencia.dias,
          horas:props.experiencia.horas,
        
        };
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
    fecha_inicio:yup
    .date()
    .nullable()
    .required("La fecha de incio es obligatorio"),
  fecha_fin:yup
    .date()
    .nullable()
    .required("La fecha final es obligatorio"),
  creditos:yup
    .number()
    .nullable()
    .required("Los creditos es obligatorio"), 
  dias:yup
    .number()
    .nullable()
    .required("Los días es obligatorio"),
  horas:yup
    .number()
    .nullable()
    .required("Las horas es obligatorio"),
});

// Función para manejar el envío del formulario
const onSubmit = async () => {
  // Evitar múltiples envíos
  if (saving.value || updating.value) return;

  try {
    saving.value = true;

    const data = { ...formData.value };

    // Validar el formulario con Yup
    const { validated, errors } = await runYupValidation(schema, data);
    if (!validated) {
      formErrors.value = errors; // Mostrar los errores de validación
      return;
    }

    formErrors.value = {}; // Limpiar errores previos

    // Crear o actualizar la experiencia formativa
    const response = props.experiencia?.id_experiencia_formativa
      ? await updateExperiencia(props.experiencia?.id_experiencia_formativa, data)
      : await createExperiencia(data);

    console.log("response: ", response);

    // Si la respuesta es exitosa
    if (response.experiencia?.id_experiencia) {
      showToast(
        `Experiencia ${
          props.experiencia?.id_experiencia_formativa ? "updated" : "created"
        } successfully`
      );

      // Cargar datos actualizados en las stores
      experienciaStore.loadExperiencias(props.ProgramId);
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();

      // Cerrar el modal
      emit("hide");
    } else {
      showToast(
        "Error al guardar la experiencia formativa. Inténtalo de nuevo.",
        "error"
      );
    }

  } catch (error) {
    console.error("Error en onSubmit Experiencia:", error);
    showToast("Ocurrió un error inesperado al guardar la experiencia.", "error");

  } finally {
    saving.value = false;
    updating.value = false;
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
        <FormInput
          v-model="formData.fecha_inicio"
          type="date"
          :focus="show"
          label="Fecha Inicio"
          :error="formErrors?.fecha_inicio"
          required
        />
        <FormInput
          v-model="formData.fecha_fin"
          type="date"
          :focus="show"
          label="Fecha Final"
          :error="formErrors?.fecha_fin"
          required
        />
        <FormInput
          v-model="formData.creditos"
          type="number"
          :focus="show"
          label="Créditos"
          :error="formErrors?.creditos"
          required
        />
        <FormInput
          v-model="formData.dias"
          type="number"
          :focus="show"
          label="Días"
          :error="formErrors?.dias"
          required
        />
        <FormInput
          v-model="formData.horas"
          type="number"
          :focus="show"
          label="Horas"
          :error="formErrors?.horas"
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
