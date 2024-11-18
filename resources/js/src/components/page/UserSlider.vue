<script setup>
import { computed, ref, watch } from "vue";
import Slider from "../ui/Slider.vue";
import FormInput from "../ui/FormInput.vue";
import FormLabelError from "../ui/FormLabelError.vue";
import VSelect from "vue-select";
import Button from "../ui/Button.vue";
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";

import useRoleStore from "../../store/useRoleStore";
import useUserStore from "../../store/useUserStore";

import useStudentsStore from "../../store/Estudiante/useStudentStore";

import useValidation from "../../composables/useValidation";
import useHttpRequest from "../../composables/useHttpRequest";
import useUtils from "../../composables/useUtils";
import useModalToast from "../../composables/useModalToast";

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

const userStore = useUserStore();
const studentStore = useStudentsStore();
const roleStore = useRoleStore();
const { store: createUser, saving, update: updateUser, updating } = useHttpRequest(
  "/users"
);
const { runYupValidation } = useValidation();
const { omitPropsFromObject } = useUtils();
const { showToast } = useModalToast();

const requiredPermissions = computed(() => {
  if (!props.user?.id)
    return ["users-all", "users-create", "students-create", "students-all"];
  else return ["users-all", "users-edit"];
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
    roles: [],
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

const roleOptions = computed(() => {
  const formDataRoleIds = formData.value.roles.map((role) => role?.id?.toString());
  console.log(formDataRoleIds)
  return roleStore.roles.filter(
    (role) =>
      !formDataRoleIds.includes(role?.id?.toString()) && role?.name !== "super-admin"
  );
});


const selectedRole = ref(null);
const onRoleSelect = (role) => {
  formData.value = {
    ...formData.value,
    roles: [role].concat(formData.value.roles),
  };
  selectedRole.value = null;
};
const onRoleRemove = (role) => {
  const updatedRoles = formData.value.roles.filter(
    (fRole) => fRole?.id?.toString() !== role?.id?.toString()
  );

  formData.value = {
    ...formData.value,
    roles: updatedRoles,
  };
};

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
  if (saving.value || updating.value) return;

  let data = {
    ...formData.value,
    roles: formData.value.roles
      ?.map((role) => role?.id)
      ?.sort((a, b) => Number(a) - Number(b)),
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
    showToast(`User ${props.user?.id ? "updated" : "created"} successfully`);
    userStore.loadUsers();
    studentStore.loadStudents();
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

        <FormLabelError label="Add role">
          <VSelect
            v-model="selectedRole"
            :options="roleOptions"
            label="name"
            @update:model-value="(role) => onRoleSelect(role)"
          />
        </FormLabelError>

        <div class="w-full space-y-3">
          <FormLabelError v-if="formData.roles?.length" label="User roles" />

          <TransitionGroup tag="ul" name="edit-list" class="relative space-y-3">
            <li
              v-for="role in formData.roles"
              :key="role.id"
              class="shadow-google rounded-sm"
            >
              <div
                class="p-4 flex-between w-full dark:bg-gray-800/60 rounded-sm border border-[#e6e6e6] dark:border-gray-700"
              >
                <div class="flex-1">{{ role.name }}</div>
                <span
                  class="text-sm cursor-pointer text-red-500 dark:text-red-300"
                  @click="onRoleRemove(role)"
                >
                  <svg
                    viewBox="0 0 24 24"
                    width="24"
                    height="24"
                    stroke="currentColor"
                    stroke-width="2"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="css-i6dzq1"
                  >
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
                </span>
              </div>
            </li>

            <Button
              :title="user?.id ? 'Save' : 'Update'"
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
