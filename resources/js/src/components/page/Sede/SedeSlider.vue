<script setup>
// archivo sedeSlider.vue
import { computed, ref, watch } from "vue";
import Slider from "../../ui/Slider.vue";
import FormInput from "../../ui/FormInput.vue";
import FormLabelError from "../../ui/FormLabelError.vue";
import VSelect from "vue-select";
import Button from "../../ui/Button.vue";
import AuthorizationFallback from "../../../components/page/AuthorizationFallback.vue";

import useUserStore from "../../../store/useUserStore";
import useRoleStore from "../../../store/useRoleStore";
import usePlacesStore from "../../../store/Sede/useSedeStore";

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
  place: {
    type: [Object, null],
    default: () => null,
  },
});

// Emitir eventos
const emit = defineEmits(["hide"]);

// Stores
const userStore = useUserStore();
const roleStore = useRoleStore();
const placeStore = usePlacesStore();

// Composables
const {
  store: createPlace,
  saving,
  update: updatePlace,
  updating,
} = useHttpRequest("/sedes");
const { runYupValidation } = useValidation();
const { showToast } = useModalToast();
const { isUserAuthenticated } = useAuth();

// Computed para manejar permisos
const requiredPlaces = computed(() => {
  if (!props.place?.id_sede) return ["places-all", "places-create"];
  else return ["places-all", "places-edit"];
});

// Computed para el título
const title = computed(() =>
  props.place
    ? `Update place "${props.place?.nombre_sede}"`
    : "Add new place"
);

// Inicialización del formulario
const initialFormData = () => ({
  nombre_sede: null,
  ubicacion: null,
});

// Variables reactivas para los datos del formulario y los errores
const formData = ref(initialFormData());
const formErrors = ref({});

// Función para restablecer el formulario al abrir el modal
watch(
  () => props.show,
  (newValue) => {
    if (newValue) {
      if (props.place?.id_sede) {
        formData.value = { 
          nombre_sede: props.place.nombre_sede,
          ubicacion: props.place.ubicacion
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
  nombre_sede: yup
    .string()
    .nullable()
    .required("El nombre de la sede es obligatorio"),
    ubicacion: yup
    .string()
    .nullable()
    .required("El nombre de la sede es obligatorio"),
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

  // Crear o actualizar la sede
  const response = props.place?.id_sede
    ? await updatePlace(props.place?.id_sede, data)
    : await createPlace(data);

  // Si la respuesta es exitosa

  console.log("response: ", response.sede);

  if (response?.sede.id_sede) {
    showToast(
      `place ${props.place?.id_sede ? "updated" : "created"} successfully`
    );

    // Cargar datos actualizados en las tiendas
    placeStore.loadPlaces();
    userStore.loadUsers();
    roleStore.loadRoles();
    isUserAuthenticated();

    // Cerrar el modal
    emit("hide");
  } else {
    showToast("Error al guardar la sede. Inténtalo de nuevo.", "error");
  }
};
</script>

<template>
  <Slider :show="show" :title="title" @hide="emit('hide')">
    <AuthorizationFallback :permissions="requiredPlaces">
      <div class="mt-4 space-y-4">
        <FormInput
          v-model="formData.nombre_sede"
          :focus="show"
          label="Nombre de la sede"
          :error="formErrors?.nombre_sede"
          required
        />

        <FormInput
          v-model="formData.ubicacion"
          :focus="show"
          label="Ubicación"
          :error="formErrors?.ubicacion"
          required
        />

        <Button
          :title="place?.id_sede ? 'Save' : 'Create'"
          :loading-title="place?.id_sede ? 'Saving...' : 'Creating...'"
          class="!w-full"
          :loading="saving || updating"
          key="submit-btn"
          @click="onSubmit"
        />
      </div>
    </AuthorizationFallback>
  </Slider>
</template>
