<script setup>
import { ref } from "vue";
import FormInput from "../components/ui/FormInput.vue";
import Button from "../components/ui/Button.vue";

import useHttpRequest from "../composables/useHttpRequest";
import useValidation from "../composables/useValidation";
import useAppRouter from "../composables/useAppRouter";
import useUserStore from "../store/useUserStore";

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

// Estado para mostrar/ocultar contraseña
const showPassword = ref(false);

const schema = object().shape({
  email: string().email().nullable().required(),
  password: string().nullable().required(),
});

const onSignIn = async () => {
  if (loggingIn.value) return;

  const { validated, data, errors } = await runYupValidation(
    schema,
    formData.value
  );

  if (!validated) {
    formErrors.value = errors;
    return;
  }

  formErrors.value = {};
  const user = await login(data);

  if (user?.id) {
    userStore.setUser(user);
    await pushToRoute({ name: "users" });
  }
};
</script>

<template>
  <div class="min-h-screen flex bg-[#F8F8FA]">
    <!-- Sección Izquierda -->
    <div class="w-full md:w-3/7 flex flex-col justify-between bg-[#F8F8FA] p-8">
      <div class="flex justify-start">
        <h2 class="text-3xl font-regular text-[#49454F] p-5">Intranet</h2>
      </div>
      <div class="flex flex-col justify-center items-center flex-grow">
        <h1 class="text-5xl font-regular text-[#49454F] mb-10">CEPRO HUANCANÉ</h1>
        <img
          class="h-48 w-auto mb-6"
          src="../assets/img/logoTwo.png"
          alt="CEPRO HUANCANÉ Logo"
        />
      </div>
    </div>

    <!-- Sección Derecha -->
    <div class="flex flex-col justify-center w-full md:w-2/5 p-8 bg-white">
      <div class="w-full mx-auto">
        <h2 class="text-3xl text-center text-[#49454F]">Bienvenido</h2>
        <p class="mt-4 text-center text-base text-[#49454F]">
          Ingresa tu cuenta para acceder al sistema
        </p>

        <!-- Formulario -->
        <form @submit.prevent="onSignIn" class="mt-8 space-y-6">
          <!-- Input para Usuario -->
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
              @keyup.enter="onSignIn"
              class="block w-full px-1 rounded-md focus:outline-none focus:ring-0 sm:text-sm"
            />
          </div>

          <!-- Input para Contraseña con ícono pegado -->
          <div class="relative">
            <!-- Input para contraseña -->
            <FormInput
              v-model="formData.password"
              label="Contraseña"
              :error="formErrors?.password"
              id="password"
              name="password"
              :type="showPassword ? 'text' : 'password'"
              autocomplete="current-password"
              required
              @keyup.enter="onSignIn"
              class="block w-full px-1 rounded-md shadow-sm focus:outline-none sm:text-sm"
            />
            <!-- Ícono de mostrar/ocultar contraseña -->
            <button
              type="button"
              class="absolute inset-y-0 right-2 flex items-center text-gray-500 "
              @click="showPassword = !showPassword"
            >
              <svg
                v-if="!showPassword"
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
                  d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"
                />
              </svg>
              <svg
                v-else
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
                  d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21"
                />
              </svg>
            </button>
          </div>

          <!-- Botón de Ingreso -->
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

        <!-- Enlaces de ayuda -->
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
