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

import CustomTextarea from "../../ui/CustomTextarea.vue";

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
  props.Unit ? `Actualizar unidad didáctica "${props.Unit?.nombre_unidad}"` : "Agregar Unidad Didáctica"
);

// Inicialización del formulario
const initialFormData = () => ({
  nombre_unidad: null,
  id_programa: props.ProgramId,
  fecha_inicio:null,
  fecha_fin:null,
  creditos:null,
  dias:null,
  horas:null,
  capacidad:null,
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
        formData.value = { 
          nombre_unidad: props.Unit.nombre_unidad,
          fecha_inicio:props.Unit.fecha_inicio,
          fecha_fin:props.Unit.fecha_fin,
          creditos:props.Unit.creditos,
          dias:props.Unit.dias,
          horas:props.Unit.horas,
          capacidad:props.Unit.capacidad,
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
  nombre_unidad: yup
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
  capacidad:yup
    .string()
    .nullable()
    .required("La capacidad es obligatorio"),
});

// Función para manejar el envío del formulario
const onSubmit = async () => {
  // Evitar múltiples envíos
  if (saving.value || updating.value) return;

  try {
    // Puedes marcar que empieza el proceso, si no lo estás manejando fuera
    saving.value = true;

    const data = { ...formData.value };

    // Validar el formulario con Yup
    const { validated, errors } = await runYupValidation(schema, data);
    if (!validated) {
      formErrors.value = errors; // Mostrar los errores
      return;
    }
    formErrors.value = {}; // Limpiar los errores previos

    // Crear o actualizar la unidad_didactica
    const response = props.Unit?.id_unidad_didactica
      ? await updateUnit(props.Unit?.id_unidad_didactica, data)
      : await createUnit(data);

    // Verificar respuesta exitosa
    if (response?.unidad?.id_unidad_didactica) {
      showToast(
        `Unidad didáctica ${props.Unit?.id_unidad_didactica ? "actualizada" : "creada"} correctamente.`
      );

      // Recargar las stores necesarias
      UnitStore.loadUnits(props.ProgramId);
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();

      // Cerrar el modal
      emit("hide");
    } else {
      // Error controlado (por si el backend responde sin ID)
      showToast("Error al guardar la unidad didáctica. Inténtalo de nuevo.", "error");
    }

  } catch (error) {
    console.error("Error en onSubmit unidad_didactica:", error);
    showToast("Ocurrió un error inesperado al guardar la unidad didáctica.", "error");

  } finally {
    // Marcar fin del proceso (opcional, si manejas estos flags)
    saving.value = false;
    updating.value = false;
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
          label="Nombre de la unidad didactica"
          :error="formErrors?.nombre_unidad"
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
        <CustomTextarea
          v-model="formData.capacidad"
          label="Capacidad de unidad didáctica"
          placeholder="Escribe algo aquí..."
          :rows="5"
          :cols="50"
          :error="formErrors?.capacidad"
          required
          @blur="validateInput"
        />
        
        <Button
          :title="Unit?.id_unidad_didactica ? 'Actualizar' : 'Crear'"
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
