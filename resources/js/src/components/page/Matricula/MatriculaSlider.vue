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
import useGroupsStore from "../../../store/Grupo/useGrupoStore";
import useEnrollmentStore from "../../../store/Matricula/useMatriculaStore";

import useValidation from "../../../composables/useValidation";
import useHttpRequest from "../../../composables/useHttpRequest";
import useModalToast from "../../../composables/useModalToast";
import useAuth from "../../../composables/useAuth";
import * as yup from "yup";

const props = defineProps({
  show: Boolean,
  Enrollment: Object,
  specialtyId: Array,
  planId: Array,
  searchId: Array,
});

// Emitir eventos
const emit = defineEmits(["hide"]);

// Stores y variables
const groupStore = useGroupsStore();
const userStore = useUserStore();
const roleStore = useRoleStore();
const EnrollmentStore = useEnrollmentStore();

const idSpecialty = ref(0);
const idPlanRef = ref(0);
const searchQuery = ref("");
const studentOptions = ref([]);


// Define `initialFormData` antes de usarlo
const initialFormData = () => ({
  id_grupo: null,
  id_estudiante: null,
});

// Ahora puedes usar `initialFormData` para inicializar `formData`
const formData = ref(initialFormData());

const formErrors = ref({});


const { store: createEnrollment, saving, update: updateEnrollment, updating } = useHttpRequest(
  "/matricula"
);
const { runYupValidation } = useValidation();
const { showToast } = useModalToast();
const { isUserAuthenticated } = useAuth();

// Computed para permisos y título
const requiredEnrollment = computed(() =>
  props.Enrollment?.id_matricula
    ? ["enrollmentStudent-all", "enrollmentStudent-edit"]
    : ["enrollmentStudent-all", "enrollmentStudent-create"]
);
const title = computed(() =>
  props.Enrollment ? `Update Matrícula "${props.Enrollment?.nombre_grupo}"` : "Add new Matrícula"
);


// Inicialización del formulario y opciones
const specialtyOptions = computed(() =>
  props.specialtyId?.map(specialty => ({
    name: specialty.nombre_especialidad,
    id: specialty.id_especialidad,
  }))
);
const planOptions = computed(() =>
  props.planId?.map(plan => ({
    name: plan.nombre_plan,
    id: plan.id_plan,
  }))
);
const groupOptions = computed(() =>
  groupStore.groups?.map(group => ({
    name: group.nombre_grupo,
    id: group.id_grupo,
  }))
);

// Validación Yup
const schema = yup.object({
  id_grupo: yup.number().nullable().required("El grupo es obligatorio"),
  id_estudiante: yup.number().nullable().required("El estudiante es obligatorio"),
});

// Búsqueda de estudiante por DNI
const searchStudentByDNI = async () => {
  if (searchQuery.value.length === 7 || searchQuery.value.length === 8) {
    try {
      // Llamar a la función para cargar los datos del estudiante
      await EnrollmentStore.loadEnrollmentById(searchQuery.value);
      
      // Verificar si se encontraron estudiantes
      if (EnrollmentStore?.EnrollmentDni && EnrollmentStore.EnrollmentDni?.length > 0) {
        studentOptions.value = EnrollmentStore?.EnrollmentDni.map(student => ({
          id: student.id,
          name: `${student.name} ${student.apellido_paterno}`,
        }));
        showToast("Estudiante encontrado con éxito!");
      } else {
        // Si no se encontraron estudiantes
        studentOptions.value = [];
        showToast("No se encontraron estudiantes con ese DNI.", "error");
      }
    } catch (error) {
      // Manejo de errores en caso de fallo de la API
      console.error("Error al buscar estudiantes:", error);
      showToast("Ocurrió un error al buscar el estudiante. Inténtalo de nuevo.", "error");
    }
  }
};

// Envío del formulario
const onSubmit = async () => {
  if (saving.value || updating.value) return;

  const data = { ...formData.value };
  const { validated, errors } = await runYupValidation(schema, data);
  if (!validated) {
    formErrors.value = errors;
    return;
  }

  const response = props.Enrollment?.id_matricula
    ? await updateEnrollment(props.Enrollment?.id_matricula, data)
    : await createEnrollment(data);
  
    //console.log("hola a todos",response.matricula?.id_matricula)

  if (response.matricula?.id_matricula) {
    showToast(`Matrícula ${props.Enrollment?.id_matricula ? "actualizado" : "creado"} con éxito`);
    EnrollmentStore.loadEnrollmentBySpecialties(props.searchId[0], props.searchId[1]);
    //roleStore.loadRoles();
    isUserAuthenticated();
    emit("hide");
    consola.log("paso hide")
  } else {
    showToast("Error al guardar el matrícula. Inténtalo de nuevo.", "error");
  }
};

watch(
  () => props.show,
  (newValue) => {
    if (newValue) {
      formData.value = props.Enrollment ? { ...props.Enrollment } : initialFormData();
    } else {
      formData.value = initialFormData();
      formErrors.value = {};
    }
  }
);

watch(searchQuery, () => {
  if (searchQuery.value.length === 7 || searchQuery.value.length === 8) {
    searchStudentByDNI();
  }
});

watch([idPlanRef, idSpecialty], ([newPlan, newSpecialty]) => {
  groupStore?.loadGroups(newPlan, newSpecialty);
});

</script>

<template>
  <Slider :show="show" :title="title" @hide="emit('hide')">
    <AuthorizationFallback :permissions="requiredEnrollment">
      <div class="mt-4 space-y-4">
        <FormLabelError label="Buscar Estudiante por DNI">
          <FormInput v-model="searchQuery" placeholder="Ingresa DNI" :error="formErrors.id_estudiante" />
        </FormLabelError>

        <FormLabelError label="Selecciona el Estudiante">
          <VSelect
            v-model="formData.id_estudiante"
            :options="studentOptions"
            label="name"
            :reduce="(option) => option.id"
            :error="formErrors.id_estudiante"
          />
        </FormLabelError>

        <FormLabelError label="Selecciona la especialidad">
          <VSelect v-model="idSpecialty" :options="specialtyOptions" label="name" :reduce="(option) => option.id" />
        </FormLabelError>

        <FormLabelError label="Selecciona el plan">
          <VSelect v-model="idPlanRef" :options="planOptions" label="name" :reduce="(option) => option.id" />
        </FormLabelError>

        <FormLabelError label="Selecciona el Grupo">
          <VSelect v-model="formData.id_grupo" :options="groupOptions" label="name" :reduce="(option) => option.id" />
        </FormLabelError>

        <Button
          :title="props.Enrollment?.id_matricula ? 'Guardar' : 'Crear'"
          class="!w-full"
          @click="onSubmit"
          :loading="saving || updating"
        />
      </div>
    </AuthorizationFallback>
  </Slider>
</template>
