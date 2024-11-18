<script setup>
import { ref } from "vue";
import FormInput from "../components/ui/FormInput.vue";
import Button from "../components/ui/Button.vue";

import useHttpRequest from "../composables/useHttpRequest";
import useValidation from "../composables/useValidation";
import useAppRouter from "../composables/useAppRouter";
import useUserStore from "../store/useUserStore";
//import useRoleStore from "../store/useRoleStore";

const { store: login, saving: loggingIn } = useHttpRequest("/login");
const { runYupValidation } = useValidation();
const { pushToRoute } = useAppRouter();
const userStore = useUserStore();


import { string, object } from "yup";

const formData = ref({
  email: null,
  password: null,
});
const formErrors = ref({});

const schema = object().shape({
  email: string().email().nullable().required(),
  password: string().nullable().required(),
});

const onSignIn = async () => {
  if (loggingIn.value) return;

  const { validated, data, errors } = await runYupValidation(schema, formData.value);

  if (!validated) {
    formErrors.value = errors;
    return;
  }

  formErrors.value = {};
  const user = await login(data);

  if (user?.id) {
    userStore.setUser(user);
    //console.log("estamosen login: ",user)
    await pushToRoute({ name: "users" });
  }
};
</script>

<template>
  <div class="min-h-screen flex bg-[#F8F8FA]">
    <!-- Sección Izquierda (60%) con Logo y Título -->
    <div class="w-full md:w-3/7 flex flex-col justify-between bg-[#F8F8FA] p-8">
      <!-- Título "Intranet" en la parte superior izquierda -->
      <div class="flex justify-start">
        <h2 class="text-3xl font-regular text-[#49454F] p-5">Intranet</h2>
      </div>

      <!-- Logo y Título Centrados -->
      <div class="flex flex-col justify-center items-center flex-grow">
        <h1 class="text-5xl font-regular text-[#49454F] mb-10">CEPRO HUANCANÉ</h1>
        <img
          class="h-48 w-auto mb-6"
          src="../assets/img/logoTwo.png"
          alt="CEPRO HUANCANÉ Logo"
        />
      </div>
    </div>

    <!-- Sección Derecha (40%) con Formulario -->
    <div class="flex flex-col justify-center w-full md:w-2/5 p-8 bg-white">
      <div class="w-full mx-auto">
        <h2 class="text-3xl text-center text-[#49454F]">Bienvenido</h2>
        <p class="mt-4 text-center text-base text-[#49454F]">
          Ingresa tu cuenta para acceder al sistema
        </p>

        <form @submit.prevent="onSignIn" class="mt-8 space-y-6">
          <div>
            <FormInput
              v-model="formData.email"
              label="Usuario"
              :error="formErrors?.email"
              id="email"
              name="email"
              type="email"
              autocomplete="email"
              required
              class="block w-full px-1 rounded-md focus:outline-none focus:ring-0 sm:text-sm"
            />
          </div>

          <div>
            <FormInput
              v-model="formData.password"
              label="Contraseña"
              :error="formErrors?.password"
              id="password"
              name="password"
              type="password"
              autocomplete="current-password"
              required
              class="block w-full px-1 rounded-md shadow-sm focus:outline-none sm:text-sm"
            />
          </div>

          <div>
            <Button
              title="Ingresar"
              class="w-full flex justify-center px-4 py-2 text-white font-medium"
              :loading="loggingIn"
              loading-title="Ingresando..."
              @click="onSignIn"
            />
          </div>
        </form>

        <div class="mt-6 text-center">
          <p class="text-sm text-[#49454F]">
            <strong
              >¿Olvidaste Tu Cuenta?
              <a href="#" class="font-bold text-[#921733]">Recupera Aquí</a></strong
            >
          </p>
        </div>

        <p class="mt-6 text-center text-sm text-[#49454F]">
          Al hacer clic, usted acepta nuestros
          <strong>
            <a href="#" class="font-bold text-[#921733] underline"
              >Términos de Servicio</a
            >
            y
            <a href="#" class="font-bold text-[#921733] underline"
              >Políticas de Privacidad</a
            >.
          </strong>
        </p>
      </div>
    </div>
  </div>
</template>
