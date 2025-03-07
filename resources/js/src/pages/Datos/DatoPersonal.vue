<script setup>
import { ref, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import useUserStore from "../../store/useUserStore";

const userStore = useUserStore();
const router = useRouter();
const route = useRoute();

// Reactive states para los datos del formulario
const form = ref({
  id: null,
  name: "",
  apellido_paterno: "",
  apellido_materno: "",
  email: "",
  celular: "",
  fecha_nacimiento: "",
  dni: "",
  sexo: "",
  direccion: "",
  imagen: "",
});

// Variables para manejar errores y el estado de carga
const errors = ref({});
const loading = ref(false);

// Función para cargar datos del usuario
const cargarDatosUsuario = async () => {
  const userId = route.params.id; // Asume que el ID del usuario viene desde la URL
  try {
    if (userStore.user?.id === Number(userId)) {
      // Si el usuario está cargado en el store, lo usamos
      form.value = {
        id: userStore.user.id,
        name: userStore.user.name,
        apellido_paterno: userStore.user.apellido_paterno,
        apellido_materno: userStore.user.apellido_materno,
        email: userStore.user.email,
        celular: userStore.user.celular,
        fecha_nacimiento: userStore.user.fecha_nacimiento,
        dni: userStore.user.dni,
        sexo: userStore.user.sexo,
        direccion: userStore.user.direccion || "",
        imagen: "", // Imagen no necesariamente está en el store
      };
    }
  } catch (error) {
    console.error("Error al cargar los datos del usuario:", error);
  }
};

// Función para cargar una nueva imagen
const onFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (event) => {
      form.value.imagen = event.target.result; // Guardamos la imagen como base64
    };
    reader.readAsDataURL(file);
  }
};

// Función para guardar los cambios
const guardarCambios = async () => {
  loading.value = true;
  errors.value = {};

  try {
    const payload = {
      name: form.value.name,
      apellido_paterno: form.value.apellido_paterno,
      apellido_materno: form.value.apellido_materno,
      email: form.value.email,
      celular: form.value.celular,
      fecha_nacimiento: form.value.fecha_nacimiento,
      dni: form.value.dni,
      sexo: form.value.sexo,
      direccion: form.value.direccion,
      imagen: form.value.imagen, // Enviar la imagen como base64 al backend
    };

    const userId = form.value.id;
    const { data } = await axios.put(`http://tu-backend-api.com/api/usuarios/${userId}`, payload);

    // Actualizamos el userStore si es el usuario actual
    if (userStore.user?.id === userId) {
      userStore.setUser(data);
    }

    alert("¡Cambios guardados exitosamente!");
    router.push({ name: "Dashboard" });
  } catch (error) {
    if (error.response && error.response.data.errors) {
      errors.value = error.response.data.errors;
    } else {
      console.error("Error al guardar los cambios:", error);
    }
  } finally {
    loading.value = false;
  }
};

// Función para cancelar la edición
const cancelarEdicion = () => {
  router.push({ name: "Dashboard" });
};

// Observa cambios en el userStore y sincroniza los datos si es necesario
watch(
  () => userStore.user,
  (newUser) => {
    if (newUser && form.value.id === newUser.id) {
      cargarDatosUsuario();
    }
  }
);

// Cargar datos al montar el componente
cargarDatosUsuario();
</script>

<template>
  <div class="max-w-4xl mx-auto mt-6 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
    <!-- Título -->
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Editar Información Personal</h2>

    <!-- Formulario -->
    <form class="space-y-6">
      <!-- Imagen de perfil -->
      <div class="flex items-center space-x-4">
        <!-- Imagen actual -->
        <div class="relative">
          <img
            :src="form.imagen || 'https://via.placeholder.com/150'"
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
      </div>

      <!-- Campos del formulario -->
      <div v-for="(label, key) in {
        name: 'Nombres',
        apellido_paterno: 'Apellido Paterno',
        apellido_materno: 'Apellido Materno',
        email: 'Correo Electrónico',
        celular: 'Celular',
        dni: 'DNI',
        direccion: 'Dirección',
      }" :key="key">
        <label :for="key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          {{ label }}
        </label>
        <input
          v-model="form[key]"
          :id="key"
          type="text"
          class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
          :placeholder="`Ingresa ${label.toLowerCase()}`"
        />
        <p v-if="errors[key]" class="text-red-500 text-sm">{{ errors[key][0] }}</p>
      </div>

      <!-- Botones -->
      <div class="flex justify-end space-x-4">
        <button
          type="button"
          @click="cancelarEdicion"
          class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500"
        >
          Cancelar
        </button>
        <button
          type="button"
          :disabled="loading"
          @click="guardarCambios"
          class="px-4 py-2 text-sm font-medium text-white bg-granate rounded-md hover:bg-granate-244 disabled:opacity-50"
        >
          Guardar Cambios
        </button>
      </div>
    </form>
  </div>
</template>
