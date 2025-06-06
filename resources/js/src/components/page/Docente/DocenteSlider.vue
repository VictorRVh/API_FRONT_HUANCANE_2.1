<script setup>
import { computed, ref, watch } from "vue";
import Slider from "../../ui/Slider.vue";
import FormInput from "../../ui/FormInput.vue";
import FormLabelError from "../../ui/FormLabelError.vue";
import VSelect from "vue-select";
import Button from "../../ui/Button.vue";
import AuthorizationFallback from "../../../components/page/AuthorizationFallback.vue";

import useRoleStore from "../../../store/useRoleStore";

import useStudentsStore from "../../../store/Estudiante/useStudentStore";

import useValidation from "../../../composables/useValidation";
import useHttpRequest from "../../../composables/useHttpRequest";
import useUtils from "../../../composables/useUtils";
import useModalToast from "../../../composables/useModalToast";

import * as yup from "yup";

const props = defineProps({
  show: {
    type: Boolean,
    default: () => false,
  },
  user: {
    type: [Object, null],
    default: () => null,
  },
  
});
const emit = defineEmits(["hide"]);

const userStore = useStudentsStore();
//const roleStore = useRoleStore();
const { store: createUser, saving, update: updateUser, updating } = useHttpRequest(
  "/users"
);
const { runYupValidation } = useValidation();
const { omitPropsFromObject } = useUtils();
const { showToast } = useModalToast();

const requiredPermissions = computed(() => {
  if (!props.user?.id) return ["teachers-all", "teachers-create"];
  else return ["teachers-all", "teachers-edit"];
});

const title = computed(() =>
  props.user ? `Actualizar Docente "${props.user?.name}"` : "Agregar Nuevo Docente"
);

const initialFormData = () => {
  return {
    name: null,
    apellido_paterno: null,
    apellido_materno: null,
    dni: null,
    sexo: null,
    celular: null,
    fecha_nacimiento: null,
    email: null,
    password: null,
    confirm_password: null,
    roles: [7],
  };
};

const formData = ref(initialFormData());
const formErrors = ref({});

watch(
  () => props.show,
  () => {
    if (props.show) {
      if (props.user?.id) {
        formData.value = Object.entries(initialFormData()).reduce((r, [key, val]) => {
          if (props.user[key]) return { ...r, [key]: props.user[key] };
          return { ...r, [key]: val };
        }, {});
      } else {
        formData.value = initialFormData();
        formErrors.value = {};
      }
    }
  }
);

const schema = yup.object().shape({
  name: yup.string().nullable().required(),
  apellido_paterno: yup.string().nullable().required(),
  apellido_materno: yup.string().nullable().required(),
  dni: yup.string().nullable().required(),
  sexo: yup.string().nullable().required(),
  celular: yup.string().nullable().required(),
  fecha_nacimiento: yup.date().nullable().required(),
  email: yup.string().email().nullable().required(),
  password: yup
    .string()
    .nullable()
    .test("password-test", "", (value, { createError }) => {
      if (props.user?.id) return true;

      if (!value) return createError({ message: "Password is a required field" });
      if (value !== formData.value.confirm_password)
        return createError({ message: "Password doesn't match" });

      return true;
    }),
});

const onSubmit = async () => {
  // Evitar múltiples envíos
  if (saving.value || updating.value) return;

  try {
    saving.value = true;

    let data = {
      ...formData.value,
      dni: String(formData.value.dni),
    };

    // Validar el formulario con Yup
    const { validated, errors } = await runYupValidation(schema, data);
    if (!validated) {
      formErrors.value = errors; // Mostrar los errores
      return;
    }

    formErrors.value = {}; // Limpiar los errores previos

    // Omitir campos innecesarios antes de enviar
    const fieldsToBeOmitted = ["confirm_password"];
    if (props.user?.id) fieldsToBeOmitted.push("password");
    data = omitPropsFromObject(data, fieldsToBeOmitted);

    // Crear o actualizar usuario
    const response = props.user?.id
      ? await updateUser(props.user?.id, data)
      : await createUser(data);

    console.log("response usuario/docente: ", response);

    if (response?.id) {
      showToast(
        `Docente ${props.user?.id ? "actualizado" : "creado"} satisfactoriamente`
      );

      // Recargar datos
      userStore.loadTeacher();

      // Cerrar modal
      emit("hide");

    } else {
      showToast("Error al guardar el docente. Inténtalo de nuevo.", "error");
    }

  } catch (error) {
    console.error("Error en onSubmit Docente:", error);
    showToast("Ocurrió un error inesperado al guardar el docente.", "error");

  } finally {
    saving.value = false;
    updating.value = false;
  }
};

</script>

<template>
  <!--  -->
  <Slider :show="show" :title="title" @hide="emit('hide')">
    <AuthorizationFallback :permissions="requiredPermissions">
      <div class="mt-4 space-y-4 m-2">
        <FormInput
          v-model="formData.name"
          :focus="show"
          label="Nombres"
          type="text"
          :error="formErrors?.name"
          required
        />

        <!-- Apellidos en la misma fila -->
        <div class="grid grid-cols-2 gap-4">
          <FormInput
            v-model="formData.apellido_paterno"
            :focus="show"
            label="Apellido Paterno"
            :error="formErrors?.apellido_paterno"
            required
          />
          <FormInput
            v-model="formData.apellido_materno"
            :focus="show"
            label="Apellido Materno"
            :error="formErrors?.apellido_materno"
            required
          />
        </div>

        <!-- Fila: DNI, Celular, Fecha de Nacimiento, Sexo -->
        <div class="grid grid-cols-4 gap-4">
          <FormInput
            v-model="formData.dni"
            :focus="show"
            type="number"
            label="DNI"
            :error="formErrors?.dni"
            required
          />
          <FormInput
            v-model="formData.celular"
            :focus="show"
            type="number"
            label="Celular"
            :error="formErrors?.celular"
            required
          />
          <FormInput
            v-model="formData.fecha_nacimiento"
            :focus="show"
            label="Fecha de Nacimiento"
            type="date"
            :error="formErrors?.fecha_nacimiento"
            required
          />
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Sexo <span class="text-red-500">*</span></label>
            <div class="flex items-center space-x-4">
              <label class="flex items-center space-x-1">
                <input
                  type="radio"
                  value="M"
                  v-model="formData.sexo"
                  class="form-radio text-blue-600 dark:bg-gray-700 dark:border-gray-600"
                />
                <span class="text-sm text-gray-700 dark:text-gray-300">Masculino</span>
              </label>
              <label class="flex items-center space-x-1">
                <input
                  type="radio"
                  value="F"
                  v-model="formData.sexo"
                  class="form-radio text-blue-600 dark:bg-gray-700 dark:border-gray-600"
                />
                <span class="text-sm text-gray-700 dark:text-gray-300">Femenino</span>
              </label>
            </div>
            <FormLabelError :message="formErrors?.sexo" />
          </div>
        </div>

        <FormInput
          v-model="formData.email"
          label="Correo electrónico"
          type="email"
          :error="formErrors?.email"
          required
        />

        <template v-if="!user?.id">
          <FormInput
            v-model="formData.password"
            label="Contraseña"
            type="password"
            :error="formErrors?.password"
            required
          />

          <FormInput
            v-model="formData.confirm_password"
            type="password"
            label="Confirmar contraseña"
            required
          />
        </template>

        <div class="w-full space-y-3">
          <TransitionGroup tag="ul" name="edit-list" class="relative space-y-3">
            <Button
              :title="user?.id ? 'Actulizar' : 'Guardar'"
              key="submit-btn"
              :loading-title="user?.id ? 'Actualizando...' : 'Guardando...'"
              class="!w-full"
              :loading="saving || updating"
              @click="onSubmit"
            />
          </TransitionGroup>
        </div>
      </div>
    </AuthorizationFallback>
  </Slider>
</template>