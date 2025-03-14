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
  groupId: Array,
  searchId: Array,
});

// Emitir eventos
const emit = defineEmits(["hide"]);

// Stores y variables
const groupStore = useGroupsStore();
const userStore = useUserStore();
const roleStore = useRoleStore();
const EnrollmentStore = useEnrollmentStore();

const idSpecialty = ref(null);
const idPlanRef = ref(null);
const searchQuery = ref("");
const studentOptions = ref([]);

const selectedStudentName = ref("");

// Define `initialFormData` antes de usarlo
const initialFormData = () => ({
  id_grupo: null,
  id_estudiante: null,
  id_especialidad: null,
  id_plan: null
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
  props.Enrollment ? `Actualizar Matrícula de "${props.Enrollment?.estudiante.name}"` : "Agregar Matrícula"
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

  id_estudiante: yup
    .string()
    .nullable()
    .required('Selecciona un estudiante antes de continuar'),

  id_specialty: yup
    .number()
    .nullable()
    .required('La especialidad es obligatoria'),

  id_plan: yup
    .number()
    .nullable()
    .required('El plan es obligatorio'),

  id_grupo: yup
    .string()
    .nullable()
    .required('Debes seleccionar un grupo'),
});

// Búsqueda de estudiante por DNI
const searchStudentByDNI = async () => {
  if (searchQuery.value.length === 7 || searchQuery.value.length === 8) {
    try {
      //console.log("Buscando estudiante con DNI:", searchQuery.value);

      // Llamar a la función para cargar los datos del estudiante
      await EnrollmentStore.loadEnrollmentById(searchQuery.value);

      //console.log("Datos recibidos:", EnrollmentStore?.EnrollmentDni);

      // Verificar si se encontraron estudiantes
      if (EnrollmentStore?.EnrollmentDni && EnrollmentStore.EnrollmentDni.length > 0) {
        studentOptions.value = EnrollmentStore.EnrollmentDni.map(student => ({
          id: student.id,
          name: `${student.name} ${student.apellido_paterno}`,
        }));

        formData.value.id_estudiante = studentOptions.value[0].id;
        selectedStudentName.value = studentOptions.value[0].name;

        // console.log("Estudiante seleccionado:", formData.value.id_estudiante, selectedStudentName.value);

        showToast("Estudiante encontrado con éxito!");
      } else {
        studentOptions.value = [];
        formData.value.id_estudiante = null;
        selectedStudentName.value = "";
        showToast("No se encontraron estudiantes con ese DNI.", "error");
      }
    } catch (error) {
      //console.error("Error al buscar estudiantes:", error);
      showToast("Ocurrió un error al buscar el estudiante. Inténtalo de nuevo.", "error");
    }
  }
};


// Envío del formulario
const onSubmit = async () => {
  if (saving.value || updating.value) return;

  // Armas el payload
  const data = {
    ...formData.value,
    id_specialty: idSpecialty.value,
    id_plan: idPlanRef.value
  };

  try {
    // 1. VALIDACIÓN YUP
    const { validated, errors } = await runYupValidation(schema, data);

    if (!validated) {
      formErrors.value = errors;
      showToast("Completa los campos obligatorios antes de enviar.", "error");
      return;
    }

    // Limpias errores antiguos
    formErrors.value = {};

    // 2. INTENTAS LLAMAR A LA API
    let response;

    if (props.Enrollment?.id_matricula) {
      response = await updateEnrollment(props.Enrollment.id_matricula, data);
    } else {
      response = await createEnrollment(data);
    }

    console.log("Matrícula response:", response);

    // 3. VERIFICAS RESPUESTA EXITOSA
    if (response?.matricula?.id_matricula) {
      showToast(`Matrícula ${props.Enrollment?.name ? "actualizada" : "creada"} con éxito`, "success");

      // Cargas nuevamente el listado
      EnrollmentStore.loadEnrollmentBySpecialtiesAndGroup(
        props.searchId[0],
        props.searchId[1],
        props.groupId
      );

      isUserAuthenticated(); // Por si es necesario

      emit("hide"); // Cierra el modal
    } else {
      // RESPUESTA SIN MATRÍCULA → ERROR DE BACKEND
      showToast("Ocurrió un error al guardar la matrícula.", "error");

      console.log('EL VERDADERO RESPOMSE', errors)

      if (response?.errors) {
        formErrors.value = response.errors;
        console.log("Errores validados por la API:", response.errors);
      }
    }

  } catch (error) {
    // 4. MANEJAS ERRORES GENERALES (network, servidor caído, etc)
    console.error("Error en la petición:", error);

    showToast("Hubo un problema en la comunicación con el servidor.", "error");

    // Si la API devuelve un response con errores específicos
    if (error?.response?.data?.errors) {
      formErrors.value = error.response.data.errors;
      console.log("Errores de validación desde la API:", error.response.data.errors);
    }
  }
};


watch(
  () => props.show,
  (newValue) => {
    if (newValue) {
      if (props.Enrollment) {
        // Modo "editar matrícula"
        formData.value = {
          id_grupo: props.Enrollment.id_grupo,
          id_estudiante: props.Enrollment.id_estudiante,
        };

        idSpecialty.value = props.Enrollment.grupos?.id_especialidad || '';
        idPlanRef.value = props.Enrollment.grupos?.id_plan || '';

        groupStore.loadGroups(idPlanRef.value, idSpecialty.value);

        selectedStudentName.value = `${props.Enrollment?.estudiante?.name} ${props.Enrollment?.estudiante?.apellido_paterno}`;
        studentOptions.value = [
          {
            id: props.Enrollment.id_estudiante,
            name: selectedStudentName.value,
          },
        ];
      } else {
        // Modo "nueva matrícula"
        formData.value = initialFormData();

        // Aquí estás inicializando a 0, lo que probablemente es el valor que después muestra el select
        idSpecialty.value = null;
        idPlanRef.value = null;

        selectedStudentName.value = "";
        studentOptions.value = [];


        // Deberías limpiar o resetear el grupo también
        formData.value.id_grupo = null;
      }

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
  formData.value.id_grupo = null;
  console.log("enetera", formData.id_grupo)
});


</script>

<template>
  <Slider :show="show" :title="title" @hide="emit('hide')">
    <AuthorizationFallback :permissions="requiredEnrollment">
      <div class="mt-4 space-y-4">
        <FormLabelError v-if="!props.Enrollment" label="Buscar Estudiante por DNI">
          <FormInput v-model="searchQuery" placeholder="Ingresa DNI" :error="formErrors.id_estudiante" />
        </FormLabelError>

        <FormLabelError label="Selecciona el Estudiante" :error="formErrors.id_estudiante">
          <VSelect v-model="formData.id_estudiante" :options="studentOptions" label="name"
            :reduce="(option) => option.id"
            :class="formErrors.id_estudiante ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
            disabled />
        </FormLabelError>

        <FormLabelError label="Selecciona la especialidad" :error="formErrors.id_specialty">
          <VSelect v-model="idSpecialty" :options="specialtyOptions" label="name" :reduce="(option) => option.id" />
        </FormLabelError>

        <FormLabelError label="Selecciona el plan" :error="formErrors.id_plan">
          <VSelect v-model="idPlanRef" :options="planOptions" label="name" :reduce="(option) => option.id" />
        </FormLabelError>

        <FormLabelError label="Selecciona el Grupo" :error="formErrors.id_grupo">
          <VSelect v-model="formData.id_grupo" :options="groupOptions" label="name" :reduce="(option) => option.id"
            :class="formErrors.id_grupo ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''" />
        </FormLabelError>


        <Button :title="props.Enrollment?.id_matricula ? 'Actualizar' : 'Matricular'" class="!w-full" @click="onSubmit"
          :loading="saving || updating" />
      </div>
    </AuthorizationFallback>
  </Slider>
</template>
