import { ref } from 'vue';
import { defineStore } from 'pinia';
// useHttpRequest para interactuar con la API
import useHttpRequest from '../../composables/useHttpRequest';

const useExperienciasStore = defineStore('experiencia', () => {
    // Importamos las funciones necesarias de useHttpRequest
    const {
        //index: getUnits,
        show: getExperienciaById, // Añadimos el método show para obtener una especialidad por ID
        //show: getunits,
        loading: experienciasLoading,
        initialLoading: ExperienciasFirstTimeLoading,
    } = useHttpRequest('/experiencia_formativa');

    const experiencia = ref(null); // Para almacenar una sola especialidad
    const experiencias = ref([]);  // Para almacenar la lista de especialidades

    // Función para establecer una especialidad específica
    const setExperiencia = (authExperiencia) => {
        experiencia.value = authExperiencia;
    };

    // Función para cargar todas las especialidades
    const loadExperiencias = async (id) => {
        const response = await getExperienciaById(id);
        experiencias.value = response;
    };

    // Nueva función para cargar una especialidad por su ID
    const loadExperienciaById = async (id) => {
        const response = await getExperienciaById(id); // Usamos el método show
        experiencia.value = response;
    };

    return {
        experiencia,
        setExperiencia,
        experiencias,
        experienciasLoading,
        ExperienciasFirstTimeLoading,
        loadExperiencias,
        loadExperienciaById, // Retornamos la nueva función para obtener una especialidad por ID
    };
});

export default useExperienciasStore;
