<script setup>
// archivo programSlider.vue
import { computed, ref, watch } from "vue";
import Slider from "../../ui/Slider.vue";
import FormInput from "../../ui/FormInput.vue";
import FormLabelError from "../../ui/FormLabelError.vue";
import VSelect from "vue-select";
import Button from "../../ui/Button.vue";
import CustomTextarea from "../../ui/CustomTextarea.vue";
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
  props.program ? `Actualizar programa "${props.program?.nombre_programa}"` : "Agregar nuevo programa"
);

console.log("Los id: ", props.planId, props.specialtyId);
// Inicialización del formulario
const initialFormData = () => ({
  nombre_programa: null,
  id_plan: props.planId,
  id_especialidad: props.specialtyId,
  horas_semanales:null,
  unidades_competencia:null
});

// Variables reactivas para los datos del formulario y los errores
const formData = ref(initialFormData());
const formErrors = ref({});

// Función para restablecer el formulario al abrir el modal
// Función para restablecer el formulario al abrir el modal
watch(
  () => props.show,
  (newValue) => {
    if (newValue) {
      if (props.program?.id_programa) {
        formData.value = Object.entries(initialFormData()).reduce((r, [key, val]) => {
          if (props.program[key]) return { ...r, [key]: props.program[key] };
          return { ...r, [key]: val };
        }, {});
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
    .required("El nombre del programa es obligatorio"),
  
  horas_semanales: yup
    .number()
    .nullable()
    .required("Las horas semanales son obligatorias"),
  
  unidades_competencia: yup
    .string()
    .nullable()
    .required("Las unidades de competencia son obligatorias"),
});

// Función para manejar el envío del formulario
const onSubmit = async () => {
  // Evitar múltiples envíos
  if (saving.value || updating.value) return;

  try {
    // Activamos el flag de saving (opcional si lo manejas en el botón)
    saving.value = true;

    const data = { ...formData.value };

    // Validar el formulario con Yup
    const { validated, errors } = await runYupValidation(schema, data);
    if (!validated) {
      formErrors.value = errors; // Mostrar los errores en el formulario
      return;
    }
    formErrors.value = {}; // Limpiar errores previos

    // Crear o actualizar el programa
    const response = props.program?.id_programa
      ? await updateProgram(props.program?.id_programa, data)
      : await createProgram(data);

    console.log("response fro: ", response.programa?.id_programa);

    // Verificar si la respuesta es exitosa
    if (response?.programa?.id_programa) {
      showToast(
        `Program ${props.program?.id_programa ? "Actualizando" : "Creando"} Exitosamente`
      );

      // Cargar datos actualizados en las tiendas necesarias
      programStore.loadPrograms(props.specialtyId, props.planId);
      userStore.loadUsers();
      roleStore.loadRoles();
      isUserAuthenticated();

      // Cerrar el modal o formulario
      emit("hide");
    } else {
      showToast("Error al guardar el programa. Inténtalo de nuevo.", "error");
    }

  } catch (error) {
    console.error("Error en onSubmit program:", error);
    showToast("Ocurrió un error inesperado al guardar el programa.", "error");

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
      <div class="mt-4 grid grid-cols-2 gap-4">
        <FormInput
          v-model="formData.nombre_programa"
          :focus="show"
          label="Nombre del programa"
          :error="formErrors?.nombre_programa"
          required
        />
        <FormInput
          v-model="formData.horas_semanales"
          label="Horas Semanales"
          :error="formErrors?.horas_semanales"
          required
        />

        <CustomTextarea
          v-model="formData.unidades_competencia"
          label="Unidades de competencia"
          placeholder="Escribe algo aquí..."
          :rows="5"
          :cols="50"
          :error="formErrors?.unidades_competencia"
          required
          @blur="validateInput"
        />
        
<div class="col-span-2 flex justify-center items-center">
  <Button
          :title="program?.id_programa ? 'Guardar' : 'Crear'"
          :loading-title="program?.id_programa ? 'Guardando...' : 'Creando...'"
          class="w-[200px] mx-auto text-center"
          :loading="saving || updating"
          key="submit-btn"
          @click="onSubmit"
        />
</div>
        
      </div>
    </AuthorizationFallback>
  </Slider>
</template>
