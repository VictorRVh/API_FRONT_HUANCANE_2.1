<script setup>
import { ref, watch, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import useUserStore from "../../store/useUserStore";
import useHttpRequest from "../../composables/useHttpRequest";
import useRoleStore from "../../store/useRoleStore";
import FormInput from "../../components/ui/FormInput.vue";
import Button from "../../components/ui/Button.vue";
import useUtils from "../../composables/useUtils";
import useModalToast from "../../composables/useModalToast";

const props = defineProps({
  show: {
    type: Boolean,
    default: () => false,
  },
  user: {
    type: [Object, null],
    default: () => null,
  },
})

const emit = defineEmits(["hide"]);

const userStore = useUserStore();
const roleStore = useRoleStore();
const router = useRouter();
const route = useRoute();

const { omitPropsFromObject } = useUtils();
const { showToast } = useModalToast();

const { store: createUser, saving, update: updateUser, updating, index } = useHttpRequest(
  "/users"
);


// Reactive states para los datos del formulario
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

const userAuth = useUserStore()

console.log('datos usuario: ', userAuth.user)

watch(
  () => userAuth.user, // Observa cambios en userStore.user
  (newUser) => {
    if (newUser?.id) {
      formData.value = {
        ...initialFormData(),
        ...newUser,
      };
    } else {
      formData.value = initialFormData();
    }
  },
  { immediate: true } // Para que se ejecute al montar el componente
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

console.log("Usuario seleccionado:", userStore.selectedUser);
console.log("ID del usuario autenticado:", userAuth.user?.id);

const onSubmit = async () => {
  if (saving.value || updating.value) return;

  try {
    let data = {
      ...formData.value, // Se copian todos los datos del formulario, sin modificar roles
    };

    console.log("Datos editados", data);

    // Omitir campos innecesarios
    const fieldsToBeOmitted = ["confirm_password", "password", "roles"]; // También omitimos los roles para que no se actualicen
    data = omitPropsFromObject(data, fieldsToBeOmitted);

    // Verificar que el usuario autenticado tenga un ID válido
    const userId = userAuth.user?.id;
    if (!userId) {
      showToast("No se seleccionó un usuario para actualizar", "error");
      return;
    }

    // Ejecutar la actualización
    const response = await updateUser(userId, data);

    if (response?.id) {
      showToast("Usuario actualizado correctamente");
      await userStore.loadUsers(); // Recargar lista de usuarios
      userStore.selectedUser = null; // Limpiar usuario seleccionado después de actualizar
      emit("hide"); // Ocultar modal o formulario
    }
  } catch (error) {
    console.error("Error al actualizar usuario:", error);
    showToast("Ocurrió un error. Intenta de nuevo.", "error");
  }
};


</script>

<template>
  <div class="max-w-4xl mx-auto mt-6 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
    <!-- Título -->
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Editar Información Personal</h2>

    <!-- Formulario -->
      <!-- Imagen de perfil -->
      <!-- <div class="flex items-center space-x-4"> -->
        <!-- Imagen actual -->
        <!-- <div class="relative">
          <img
            :src="'https://via.placeholder.com/150'"
            alt="Imagen de perfil"
            class="w-24 h-24 rounded-full border border-gray-300 dark:border-gray-600 object-cover"
            
          />
          
          <label
            for="imagen"
            class="absolute bottom-0 right-0 bg-granate text-white rounded-full w-8 h-8 flex items-center justify-center cursor-pointer hover:bg-granate"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-5 h-5"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"
              />
            </svg>
          </label>
          <input
            type="file"
            id="imagen"
            accept="image/*"
            class="hidden"
            @change="onFileChange"
          />
        </div>
        <p class="text-gray-600 dark:text-gray-300 text-sm">
          Sube una imagen cuadrada para tu perfil.
        </p>
      </div>  -->

      <!-- Campos del formulario -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <FormInput
          v-model="formData.name"
          label="Nombres"
          :error="formErrors?.name"
          required
        />

        <FormInput
          v-model="formData.apellido_paterno"
          label="Apellido Paterno"
          :error="formErrors?.apellido_paterno"
          required
        />

        <FormInput
          v-model="formData.apellido_materno"
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
          label="Correo electrónico"
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

        <FormLabelError label="Asignar rol">
          <VSelect
            v-model="selectedRole"
            :options="roleOptions"
            label="name"
            @update:model-value="(role) => onRoleSelect(role)"
            class=" dark:text-gray-700  "
          />
        </FormLabelError>

        <!-- <div class="w-full space-y-3 dark:text-white"> -->
          <!-- <FormLabelError v-if="formData.roles?.length" label="User roles" /> -->

          <!-- <TransitionGroup tag="ul" name="edit-list" class="relative space-y-3">
            <li
              v-for="role in formData.roles"
              :key="role.id"
              class="shadow-google rounded-sm "
            >
              <div
                class="p-4 flex-between w-full dark:bg-gray-800/60 rounded-sm border border-[#e6e6e6] dark:border-gray-700"
              >
                <div class="flex-1 dark:text-white">{{ role.name }}</div>
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
                    class="css-i6dzq1 cursor-pointer"
                  >
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
                </span>
              </div>
            </li>

            
          </TransitionGroup> -->

          <!-- <Button
              :title="user?.id ? 'Actualizar' : 'Guardar'"
              key="submit-btn"
              :loading-title="user?.id ? 'Saving...' : 'Updating...'"
              class="!w-full"
              :loading="saving || updating"
              @click="onSubmit"
            /> -->
        <!-- </div> -->
      </div>


      <div class="w-full space-y-3 dark:text-white">
          <Button
              :title="user?.id ? 'Actualizar' : 'Guardar'"
              key="submit-btn"
              :loading-title="user?.id ? 'Saving...' : 'Updating...'"
              class="!w-full"
              :loading="saving || updating"
              @click="onSubmit"
            />
      </div>
  </div>
</template>
