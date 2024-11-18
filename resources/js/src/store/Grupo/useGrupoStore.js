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
        loading: GroupsLoading,
        initialLoading: GroupsFirstTimeLoading,
    } = useHttpRequest('/grupo');

    //obtenemos docentes :´) 
    const {
        showTwo: getGrupoDocente,
        show: getGrupoStudent
    } = useHttpRequest('/grupoDocente');
 

    const group = ref(null); // Para almacenar una sola Groupa
    const groups = ref([]);  // Para almacenar la lista de Groupa es
    const student = ref([]);


    // Función para establecer una Groupa específica
    const setGroup = (authGroup) => {
        group.value = authGroup;
    };

    const userStore = useUserStore();
    const user = userStore.user; // Accedemos al valor real del usuario
    const isDocente = user?.roles?.[0]?.name === 'docente';


    // Función para cargar todas las Groupaes
    const loadGroups = async (plan = 1, especialty) => {

        // // Obtiene los grupos según el rol del usuario
        // const response = await getGroups(plan, especialty);
        if (isDocente) {
            const response = await getGrupoDocente(user?.roles?.[0].pivot.user_id, plan);
            groups.value = response;
          //  console.log('Docentes store en ici: ',user?.roles?.[0].pivot.user_id);

        } else {
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
    const loadGroupStudent = async (id) => {
        const response = await getGrupoStudent(id); // Usamos el método show
        student.value = response;
    };

    return {
        group,
        student,
        setGroup,
        groups,
        GroupsLoading,
        GroupsFirstTimeLoading,
        loadGroups,
        loadGroupById, // Retornamos la nueva función para obtener una Groupa por ID
        loadGroupStudent
    };
});

export default useGroupsStore;
