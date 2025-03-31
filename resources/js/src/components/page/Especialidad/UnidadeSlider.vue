<script setup>
import { computed, ref, watch, onMounted } from "vue";
import * as yup from "yup";

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

const props = defineProps({
  show: Boolean,
  Unit: Object,
  ProgramId: Number,
});

const emit = defineEmits(["hide"]);

const UnitStore = useUnitsStore();
const { store: createUnit, saving, update: updateUnit, updating } = useHttpRequest("/unidad_didactica");
const { runYupValidation } = useValidation();
const { showToast } = useModalToast();
const { isUserAuthenticated } = useAuth();

const requiredUnits = computed(() => (!props.Unit?.id_unidad_didactica ? ["units-all", "units-create"] : ["units-all", "units-edit"]));
const title = computed(() => (props.Unit ? `Actualizar unidad didáctica "${props.Unit?.nombre_unidad}"` : "Agregar Unidad Didáctica"));

const initialFormData = () => ({
  numero_unidad: null,
  nombre_unidad: null,
  id_programa: props.ProgramId, // Se asegura que tenga el `ProgramId`
  fecha_inicio: null,
  fecha_fin: null,
  creditos: null,
  dias: null,
  horas: null,
  capacidad: null,
});

const formData = ref(initialFormData());
const formErrors = ref({});
const unidadesOcupadas = ref([]);
const unidadesOcupadasUpdate = ref([]);
const UnitOptions = ref([]);


unidadesOcupadasUpdate.value = Array.from({ length: 10 }, (_, i) => ({
  id: i + 1,
  name: `Unidad ${i + 1}`,
  enable: true
}));

function recargarindexUnidades() {
  unidadesOcupadas.value = Array.isArray(UnitStore.indexAll?.unidades_didacticas) ? UnitStore.indexAll.unidades_didacticas : [];

  //console.log("Datos cargados:", unidadesOcupadas.value);

  if (!Array.isArray(unidadesOcupadas.value)) {
    console.error("Error: unidadesOcupadas no es un array", unidadesOcupadas.value);
    unidadesOcupadas.value = [];
  }


  UnitOptions.value = unidadesOcupadasUpdate.value.filter(unit => !unidadesOcupadas.value.some(u => parseInt(u.numero_unidad) === unit.id));
  // Seleccionar automáticamente la primera unidad disponible
  if (UnitOptions.value.length > 0) {
    formData.value.numero_unidad = UnitOptions.value[0].id;
    console.log("index Creado en fomr: ", formData.value.numero_unidad)
  }

}

onMounted(async () => {
  try {
    await UnitStore.loadUnitAllindex(props.ProgramId);
    // console.log("Datos cargados index:", UnitStore.indexAll?.unidades_didacticas);
    recargarindexUnidades();
  } catch (error) {
    console.error("Error cargando unidades didácticas:", error);
  }
});

watch(
  () => props.show,
  (newValue) => {
    formData.value = newValue && props.Unit
      ? {
        ...props.Unit,
        id_programa: props.ProgramId,
        numero_unidad: props.Unit.numero_unidad, // Asegurar que es solo el índice
      }
      : initialFormData();
    formErrors.value = {};
  }
);


const schema = yup.object().shape({
  numero_unidad: yup.string().nullable().required("El nivel de unidad didáctica es obligatorio"),
  nombre_unidad: yup.string().nullable().required("El nombre de la unidad didáctica es obligatorio"),
  fecha_inicio: yup.date().nullable().required("La fecha de inicio es obligatoria"),
  fecha_fin: yup.date().nullable().required("La fecha final es obligatoria").min(yup.ref("fecha_inicio"), "La fecha final debe ser posterior a la fecha de inicio"),
  creditos: yup.number().nullable().required("Los créditos son obligatorios"),
  dias: yup.number().nullable().required("Los días son obligatorios"),
  horas: yup.number().nullable().required("Las horas son obligatorias"),
  capacidad: yup.string().nullable().required("La capacidad es obligatoria"),
});

const onSubmit = async () => {
  if (saving.value || updating.value) return;

  try {
    saving.value = true;
    const { validated, errors } = await runYupValidation(schema, formData.value);
    if (!validated) {
      formErrors.value = errors;
      return;
    }
    formErrors.value = {};

    const response = props.Unit?.id_unidad_didactica
      ? await updateUnit(props.Unit?.id_unidad_didactica, formData.value)
      : await createUnit(formData.value);

    if (response?.unidad?.id_unidad_didactica) {
      showToast(`Unidad didáctica ${props.Unit?.id_unidad_didactica ? "actualizada" : "creada"} correctamente.`);
      UnitStore.loadUnits(props.ProgramId);

      isUserAuthenticated();
      emit("hide");
      await UnitStore.loadUnitAllindex(props.ProgramId);
      recargarindexUnidades();
    } else {
      showToast("Error al guardar la unidad didáctica. Inténtalo de nuevo.", "error");
    }
  } catch (error) {
    console.error("Error en onSubmit unidad_didactica:", error);
    showToast("Ocurrió un error inesperado al guardar la unidad didáctica.", "error");
  } finally {
    saving.value = false;
    updating.value = false;
  }
};
</script>
<template>
  <Slider :show="show" :title="title" @hide="emit('hide')">
    <AuthorizationFallback :permissions="requiredUnits">
      <div class="mt-4 grid grid-cols-2 gap-4">
        <FormLabelError v-if="!props.Unit" label="Selecciona la Unidad"
          :error="formErrors.numero_unidad">
          <VSelect v-model="formData.numero_unidad" :options="UnitOptions" label="name" :reduce="(option) => option.id"
            :class="formErrors.numero_unidad ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''" />
        </FormLabelError>

        <FormInput v-model="formData.nombre_unidad" label="Nombre de la unidad didáctica"
          :error="formErrors.nombre_unidad" required />
        <FormInput v-model="formData.fecha_inicio" type="date" label="Fecha Inicio" :error="formErrors.fecha_inicio"
          required />

        <FormInput v-model="formData.fecha_fin" type="date" label="Fecha Final" :error="formErrors.fecha_fin"
          required />
        <FormInput v-model="formData.creditos" type="number" label="Créditos" :error="formErrors.creditos" required />
        <FormInput v-model="formData.dias" type="number" label="Días" :error="formErrors.dias" required />
        <FormInput v-model="formData.horas" type="number" label="Horas" :error="formErrors.horas" required />
        <CustomTextarea v-model="formData.capacidad" label="Capacidad de unidad didáctica"
          placeholder="Escribe algo aquí..." :error="formErrors.capacidad" required />
        
        <div class="col-span-2 flex justify-center items-center">
          <Button 
          :title="props.Unit?.id_unidad_didactica ? 'Actualizar' : 'Crear'" 
          :loading-title="props.Unit?.id_unidad_didactica ? 'Actualizando...' : 'Guardando...'"
          :loading="saving || updating"
            @click="onSubmit" class="w-[100px] mx-auto text-center" />
        </div>
      </div>
    </AuthorizationFallback>
  </Slider>
</template>
