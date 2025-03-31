import { ref } from 'vue';
import { defineStore } from 'pinia';
import useUserStore from "../../store/useUserStore";

// useHttpRequest para interactuar con la API
import useHttpRequest from '../../composables/useHttpRequest';
//import useRolStore from "../useRoleStore"


const userStore = useUserStore();


const useGroupsStore = defineStore('Groups', () => {
    // Importamos las funciones necesarias de useHttpRequest
    const {
        index: getGroupsAll,
        show: getGroupById, // Añadimos el método show para obtener una Groupa por ID
        showTwo: getGroups,
        loading: GroupsLoading0,
        initialLoading: GroupsFirstTimeLoading0,
    } = useHttpRequest('/grupo');

    //obtenemos docentes :´) 
    const {
        show: getGrupoStudent,
        loading: GroupsLoading1,
        initialLoading: GroupsFirstTimeLoading1,
    } = useHttpRequest('/grupoDocenteTwo');

    const {
        show: getGrupoStudentExp,
        loading: GroupsLoading2,
        initialLoading: GroupsFirstTimeLoading2,
    } = useHttpRequest('/grupoNotasExperiencia');

    const {
        showTwo: getGrupoEstudiante,
        //show: getGrupoStudentNote
        loading: GroupsLoading3,
        initialLoading: GroupsFirstTimeLoading3,
    } = useHttpRequest('/grupoEstudiante');
    const {
        showTwo: getGrupoDocente,
        show: getGrupoStudentNote,
        loading: GroupsLoading4,
        initialLoading: GroupsFirstTimeLoading4,
    } = useHttpRequest('/grupoDocente');
    const {
        showTwo: certificadoStudent,
        loading: GroupsLoading5,
        initialLoading: GroupsFirstTimeLoading5,
    } = useHttpRequest('/certificado');

    const group = ref(null); // Para almacenar una sola Groupa
    const groups = ref([]);  // Para almacenar la lista de Groupa es
    const student = ref([]);
    const certificate = ref([]);


    // tiempo de carga 
    const GroupsLoading = computed(() =>
        GroupsLoading0.value ||
        GroupsLoading1.value ||
        GroupsLoading2.value ||
        GroupsLoading3.value ||
        GroupsLoading4.value ||
        GroupsLoading5.value
    );

    const GroupsFirstTimeLoading = computed(() =>
        GroupsFirstTimeLoading0.value ||
        GroupsFirstTimeLoading1.value ||
        GroupsFirstTimeLoading2.value ||
        GroupsFirstTimeLoading3.value ||
        GroupsFirstTimeLoading4.value ||
        GroupsFirstTimeLoading5.value
    );

    // Función para establecer una Groupa específica
    const setGroup = (authGroup) => {
        group.value = authGroup;
    };

    const resetStore = () => {
        group.value = null;
        groups.value = [];
        student.value = [];
        certificate.value = [];
    };


    const userStore = useUserStore();
    const user = userStore.user; // Accedemos al valor real del usuario
    const isDocente = user?.roles?.[0]?.name === 'docente';
    const isStudent = user?.roles?.[0]?.name === 'estudiante';

    // Función para cargar todas las Groupaes
    const loadGroups = async (plan = 1, especialty) => {

        // // Obtiene los grupos según el rol del usuario
        // const response = await getGroups(plan, especialty);
        if (isDocente) {
            const response = await getGrupoDocente(user?.roles?.[0].pivot.user_id, plan);
            groups.value = response;
            //  console.log('Docentes store en ici: ',user?.roles?.[0].pivot.user_id);

        }
        else if (isStudent) {
            const response = await getGrupoEstudiante(user?.roles?.[0].pivot.user_id, plan);
            groups.value = response;
            //  console.log('Docentes store en ici: ',user?.roles?.[0].pivot.user_id);
        }
        else {
            // Si es administrativo, carga todos los grupos con el método original `getGroups`
            const response = await getGroups(plan, especialty);
            groups.value = response;
            //  console.log('Administrativo');
        }
    };

    // Nueva función para cargar una Groupa por su ID
    const loadGroupById = async (id) => {
        const response = await getGroupById(id); // Usamos el método show
        group.value = response;
    };
    const loadGroupStudentNote = async (id) => {
        const response = await getGrupoStudentNote(id); // Usamos el método show
        student.value = response;
    };
    const loadGroupStudent = async (id) => {
        const response = await getGrupoStudent(id); // Usamos el método show
        student.value = response;
    };

    const loadGroupStudentExperience = async (id) => {
        const response = await getGrupoStudentExp(id); // Usamos el método show
        student.value = response;
    };

    const loadNotas = async (id_group) => {
        const response = await certificadoStudent(user?.roles?.[0].pivot.user_id, id_group);
        certificate.value = response;
        //console.log("Store certificado: ", response
    };

    const loadCertificate = async (id, id_group) => {
        const response = await certificadoStudent(id, id_group);
        certificate.value = response;
        //console.log("Store certificado: ", response
    };

    return {
        group,
        student,
        certificate,
        setGroup,
        groups,
        GroupsLoading,
        GroupsFirstTimeLoading,
        loadNotas,
        loadGroups,
        loadGroupById, // Retornamos la nueva función para obtener una Groupa por ID
        loadGroupStudentNote,
        loadGroupStudentExperience,
        loadGroupStudent,
        loadCertificate,
        resetStore,
    };
});

export default useGroupsStore;
