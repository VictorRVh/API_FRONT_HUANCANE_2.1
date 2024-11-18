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

import useGroupsStore from "../../../store/Grupo/useGrupoStore";
import useProgramStore from "../../../store/Especialidad/useProgramaStore";

import useValidation from "../../../composables/useValidation";
import useHttpRequest from "../../../composables/useHttpRequest";
import useModalToast from "../../../composables/useModalToast";
import useAuth from "../../../composables/useAuth";
import * as yup from "yup";

const props = defineProps({
  show: {
    type: Boolean,
    default: () => false,
  },
  group: {
    type: Object,
    default: null,
  },
  sedeId: {
    type: Object,
    default: null,
  },
  turnoId: {
    type: Object,
    default: null,
  },
  specialtyId: {
    type: Object,
    default: null,
  },
  planId: {
    type: Object,
    default: null,
  },
  docenteId: {
    type: Object,
    default: null,
  },
  searchId: {
    type: Array,
    default: null,
  },
});

console.log("legada de los ID: ", props.searchId);

// Emitir eventos
const emit = defineEmits(["hide"]);

// Stores
const userStore = useUserStore();
const roleStore = useRoleStore();
const groupStore = useGroupsStore();

const ProgramStore = useProgramStore();

// ProgramStore.Programs.Program

// Composables
const { store: createGroup, saving, update: updateGroup, updating } = useHttpRequest(
  "/grupo"
);
const { runYupValidation } = useValidation();
const { showToast } = useModalToast();
const { isUserAuthenticated } = useAuth();

// Computed para manejar permisos
const requiredGroup = computed(() => {
  return props.group?.id_grupo
    ? ["groups-all", "groups-edit"]
    : ["groups-all", "groups-create"];
});

// Computed para el título
const title = computed(() =>
  props.group ? `Update group "${props.group?.nombre_grupo}"` : "Add new group"
);

// Inicialización del formulario
const initialFormData = () => ({
  nombre_grupo: null,
  id_sede: null,
  id_turno: null,
  id_especialidad: null,
  id_plan: null,
  id_docente: null,
  id_programa: null,
});

const formData = ref(initialFormData());
const formErrors = ref({});

const programOptions = ref([]);

// Función para restablecer el formulario al abrir el modal
watch(
  () => props.show,
  (newValue) => {
    if (newValue) {
      // Si `props.group` contiene datos, cargamos esos datos en `formData`
      formData.value = props.group ? { ...props.group } : initialFormData();
    } else {
      formData.value = initialFormData();
      formErrors.value = {};
    }
  }
);

const sedeOptions = computed(() =>
  props.sedeId.map((sede) => ({
    name: sede.nombre_sede,
    id: sede.id_sede,
  }))
);
//const sedeOptions =  ref(sedeO.value); // Ejemplo de opciones
const turnoOptions = ref([
  { id: 1, name: "Turno Mañana" },
  { id: 2, name: "Turno Tarde" },
]);
const specialtyOptions = computed(() =>
  props.specialtyId.map((specialty) => ({
    name: specialty.nombre_especialidad,
    id: specialty.id_especialidad,
  }))
);
const planOptions = computed(() =>
  props.planId.map((plan) => ({
    name: plan.nombre_plan,
    id: plan.id_plan,
  }))
);
const docenteOptions = computed(() =>
  props.docenteId.map((teacher) => ({
    name: teacher.name,
    id: teacher.id,
  }))
);

// Validación de Yup
const schema = yup.object().shape({
  nombre_grupo: yup.string().nullable().required("El nombre del grupo es obligatorio"),
  id_sede: yup.number().nullable().required("La sede es obligatoria"),
  id_turno: yup.number().nullable().required("El turno es obligatorio"),
  id_especialidad: yup.number().nullable().required("La especialidad es obligatoria"),
  id_plan: yup.number().nullable().required("El plan es obligatorio"),
  id_docente: yup.number().nullable().required("El docente es obligatorio"),
});

// Envío del formulario
const onSubmit = async () => {
  if (saving.value || updating.value) return;

  const data = { ...formData.value };
  const { validated, errors } = await runYupValidation(schema, data);
  if (!validated) {
    formErrors.value = errors;
    return;
  }

  const response = props.group?.id_grupo
    ? await updateGroup(props.group?.id_grupo, data)
    : await createGroup(data);

  if (response.grupo?.id_grupo) {
    showToast(`Grupo ${props.group?.id_grupo ? "actualizado" : "creado"} con éxito`);
    groupStore.loadGroups(props.searchId[0], props.searchId[1]);
    //roleStore.loadRoles();
    isUserAuthenticated();
    emit("hide");
  } else {
    showToast("Error al guardar el grupo. Inténtalo de nuevo.", "error");
  }
};

watch(
  [() => formData.value.id_especialidad, () => formData.value.id_plan],
  async ([especialidadId, planId]) => {
    // console.log("Datos actualizados:", especialidadId, planId);

    if (especialidadId && planId) {
      try {
        await ProgramStore.loadPrograms(especialidadId, planId);

        // Actualiza las opciones de programa
        programOptions.value = ProgramStore.Programs?.programas.map(programa => ({
          name: programa.nombre_programa,
          id: programa.id_programa
        })) || [];

        // Asigna automáticamente el primer programa si hay alguno disponible
        if (programOptions.value.length > 0) {
          formData.value.id_programa = programOptions.value[0].id;
          selectedProgramName.value = programOptions.value[0].name; // Guarda el nombre para mostrarlo
        } else {
          formData.value.id_programa = null;
          selectedProgramName.value = "";
        }

        console.log("Opciones de programas cargadas:", programOptions.value);
      } catch (error) {
        console.error("Error al cargar los programas:", error);
      }
    }
  }
);
</script>

<template>
  <Slider :show="show" :title="title" @hide="emit('hide')">
    <AuthorizationFallback :permissions="requiredGroup">
      <div class="mt-4 space-y-4">
        <FormInput
          v-model="formData.nombre_grupo"
          :focus="show"
          label="Nombre del grupo"
          :error="formErrors?.nombre_grupo"
          required
        />

        <FormLabelError label="Selecciona la sede">
          <VSelect
            v-model="formData.id_sede"
            :options="sedeOptions"
            label="name"
            :reduce="(option) => option.id"
            :error="formErrors?.id_sede"
          />
        </FormLabelError>

        <FormLabelError label="Selecciona el turno">
          <VSelect
            v-model="formData.id_turno"
            :options="turnoOptions"
            label="name"
            :reduce="(option) => option.id"
            :error="formErrors?.id_turno"
          />
        </FormLabelError>

        <FormLabelError label="Selecciona la especialidad">
          <VSelect
            v-model="formData.id_especialidad"
            :options="specialtyOptions"
            label="name"
            :reduce="(option) => option.id"
            :error="formErrors?.id_especialidad"
          />
        </FormLabelError>

        <FormLabelError label="Selecciona el plan">
          <VSelect
            v-model="formData.id_plan"
            :options="planOptions"
            label="name"
            :reduce="(option) => option.id"
            :error="formErrors?.id_plan"
          />
        </FormLabelError>

        <FormLabelError label="Selecciona el programa">
          <VSelect
            v-model="formData.id_programa"
            :options="programOptions"
            label="name"
            :reduce="(option) => option.id"
            :error="formErrors?.id_programa"
            disabled
          />
        </FormLabelError>

        <FormLabelError label="Selecciona el docente">
          <VSelect
            v-model="formData.id_docente"
            :options="docenteOptions"
            label="name"
            :reduce="(option) => option.id"
            :error="formErrors?.id_docente"
          />
        </FormLabelError>

        <Button
          :title="group?.id_grupo ? 'Save' : 'Create'"
          :loading-title="group?.id_grupo ? 'Saving...' : 'Creating...'"
          class="!w-full"
          :loading="saving || updating"
          key="submit-btn"
          @click="onSubmit"
        />
      </div>
    </AuthorizationFallback>
  </Slider>
</template>
