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
  idUser:{
    type: Number,
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
  if (!props.user?.id) return ["students-all", "students-create"];
  else return ["students-all", "students-edit"];
});

const title = computed(() =>
  props.user ? `Update user "${props.user?.name}"` : "Add new user"
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
    roles: [props.idUser],
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
  //console.log("jaaaaaaaa");

  if (saving.value || updating.value) return;

  let data = {
    ...formData.value,
  };

  const { validated, errors } = await runYupValidation(schema, data);
  if (!validated) {
    formErrors.value = errors;
    return;
  }
  formErrors.value = {};

  const fieldsToBeOmitted = ["confirm_password"];
  if (props.user?.id) fieldsToBeOmitted.push("password");
  data = omitPropsFromObject(data, fieldsToBeOmitted);

  const response = props.user?.id
    ? await updateUser(props.user?.id, data)
    : await createUser(data);

  if (response?.id) {
    showToast(`Student ${props.user?.id ? "updated" : "created"} successfully`);
    userStore.loadStudents(props.idUser);
    emit("hide");
  }
};
</script>

<template>
  <!--  -->
  <Slider :show="show" :title="title" @hide="emit('hide')">
    <AuthorizationFallback :permissions="requiredPermissions">
      <div class="mt-4 space-y-4">
        <FormInput
          v-model="formData.name"
          :focus="show"
          label="Name"
          :error="formErrors?.name"
          required
        />

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

        <FormInput
          v-model="formData.dni"
          :focus="show"
          label="Dni"
          :error="formErrors?.dni"
          required
        />

        <FormInput
          v-model="formData.sexo"
          :focus="show"
          label="Sexo"
          :error="formErrors?.sexo"
          required
        />

        <FormInput
          v-model="formData.celular"
          :focus="show"
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

        <FormInput
          v-model="formData.email"
          label="Email"
          :error="formErrors?.email"
          required
        />

        <template v-if="!user?.id">
          <FormInput
            v-model="formData.password"
            label="Password"
            type="password"
            :error="formErrors?.password"
            required
          />

          <FormInput
            v-model="formData.confirm_password"
            type="password"
            label="Confirm password"
            required
          />
        </template>

        <div class="w-full space-y-3">
          <TransitionGroup tag="ul" name="edit-list" class="relative space-y-3">
            <Button
              :title="user?.id ? 'Update' : 'Save'"
              key="submit-btn"
              :loading-title="user?.id ? 'Saving...' : 'Updating...'"
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
