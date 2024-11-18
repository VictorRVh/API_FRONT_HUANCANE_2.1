import { ref } from 'vue';
import { defineStore } from 'pinia';
// useHttpRequest para interactuar con la API
import useHttpRequest from '../../composables/useHttpRequest';

const useEnrollmentStudentsStore = defineStore('EnrollmentStudents', () => {
    // Importamos las funciones necesarias de useHttpRequest
    const {
        index: getEnrollmentStudentsAll,
        show: getEnrollmentById, // Añadimos el método show para obtener una especialidad por ID
        showTwo: getEnrollmentStudents,
        showThree: getEnrollmentSudentsByGroup,
        loading: EnrollmentStudentsLoading,
        initialLoading: EnrollmentStudentsFirstTimeLoading,
    } = useHttpRequest('/matricula');

    const Enrollment = ref(null); // Para almacenar una sola especialidad
    const EnrollmentDni = ref(null); // Para almacenar un estudiante matriculado
    const EnrollmentStudents = ref([]);  // Para almacenar la lista de especialidades
    const EnrollmentGroup = ref(null);

    // Función para establecer una especialidad específica
    const setEnrollment = (authEnrollment) => {
        Enrollment.value = authEnrollment;
    };

    // Función para cargar todas las especialidades
    const loadEnrollmentStudents = async () => {
        const response = await getEnrollmentStudentsAll();
        EnrollmentStudents.value = response;
    };

    // Nueva función para cargar una especialidad por su ID
    const loadEnrollmentById = async (id) => {
        const response = await getEnrollmentById(id); // Usamos el método show
        EnrollmentDni.value = response;
       // console.log("Store: ",response)
    };
    const loadEnrollmentBySpecialties = async (plan,specialty) => {
        const response = await getEnrollmentStudents(plan,specialty); // Usamos el método show
        Enrollment.value = response;
       // console.log("Store: ",response)
    };
    const loadEnrollmentBySpecialtiesAndGroup = async (plan,specialty,group) => {
        const response = await getEnrollmentSudentsByGroup(plan,specialty,group); // Usamos el método show
        EnrollmentGroup.value = response;
       // console.log("Store: ",response)
    };
    const loadUser = async () =>{
        const response = await getUserDni();
        users.value = response;
    }

    return {
        Enrollment,
        EnrollmentDni,
        setEnrollment,
        EnrollmentGroup,
        EnrollmentStudents,
        EnrollmentStudentsLoading,
        EnrollmentStudentsFirstTimeLoading,
        loadEnrollmentStudents,
        loadEnrollmentBySpecialties,
        loadEnrollmentBySpecialtiesAndGroup,
        loadEnrollmentById, // Retornamos la nueva función para obtener una especialidad por ID
    };
});

export default useEnrollmentStudentsStore;
