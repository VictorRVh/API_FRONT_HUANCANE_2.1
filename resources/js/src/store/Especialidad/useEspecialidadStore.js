import { ref } from 'vue';
import { defineStore } from 'pinia';
// useHttpRequest para interactuar con la API
import useHttpRequest from '../../composables/useHttpRequest';

const useSpecialtiesStore = defineStore('specialties', () => {
    // Importamos las funciones necesarias de useHttpRequest
    const {
        index: getSpecialties,
        show: getSpecialtyById, // Añadimos el método show para obtener una especialidad por ID
        loading: specialtiesLoading,
        initialLoading: specialtiesFirstTimeLoading,
    } = useHttpRequest('/especialidad');

    const specialty = ref(null); // Para almacenar una sola especialidad
    const specialties = ref([]);  // Para almacenar la lista de especialidades

    // Función para establecer una especialidad específica
    const setSpecialty = (authSpecialty) => {
        specialty.value = authSpecialty;
    };

    // Función para cargar todas las especialidades
    const loadSpecialties = async () => {
        const response = await getSpecialties();
        specialties.value = response;
    };

    // Nueva función para cargar una especialidad por su ID
    const loadSpecialtyById = async (id) => {
        const response = await getSpecialtyById(id); // Usamos el método show
        specialty.value = response;
    };

    return {
        specialty,
        setSpecialty,
        specialties,
        specialtiesLoading,
        specialtiesFirstTimeLoading,
        loadSpecialties,
        loadSpecialtyById, // Retornamos la nueva función para obtener una especialidad por ID
    };
});

export default useSpecialtiesStore;
