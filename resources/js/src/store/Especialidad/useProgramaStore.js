import { ref } from 'vue';
import { defineStore } from 'pinia';
// useHttpRequest para interactuar con la API
import useHttpRequest from '../../composables/useHttpRequest';

const useProgramsStore = defineStore('Programs', () => {
    // Importamos las funciones necesarias de useHttpRequest
    const {
        index: getProgramsAll,
        show: getProgramById, // Añadimos el método show para obtener una Programa por ID
        showTwo: getPrograms,
        loading: ProgramsLoading,
        initialLoading: ProgramsFirstTimeLoading,
    } = useHttpRequest('/programa');

    const program = ref(null); // Para almacenar una sola Programa
    const Programs = ref([]);  // Para almacenar la lista de Programa es

    // Función para establecer una Programa específica
    const setProgram = (authProgram) => {
       program.value = authProgram;
    };

    // Función para cargar todas las Programaes
    const loadPrograms = async (idOne,idTwo) => {
        const response = await getPrograms(idOne,idTwo);
        Programs.value = response;
    };

    // Nueva función para cargar una Programa por su ID
    const loadProgramById = async (id) => {
        const response = await getProgramById(id); // Usamos el método show
       program.value = response;
    };

    return {
        program,
        setProgram,
        Programs,
        ProgramsLoading,
        ProgramsFirstTimeLoading,
        loadPrograms,
        loadProgramById, // Retornamos la nueva función para obtener una Programa por ID
    };
});

export default useProgramsStore;
