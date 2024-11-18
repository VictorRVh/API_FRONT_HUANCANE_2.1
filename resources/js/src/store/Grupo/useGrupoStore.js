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

    const group = ref(null); // Para almacenar una sola Groupa
    const groups = ref([]);  // Para almacenar la lista de Groupa es


    // Función para establecer una Groupa específica
    const setGroup = (authGroup) => {
        group.value = authGroup;
    };

    // Función para cargar todas las Groupaes
    const loadGroups = async (plan = 1, especialty) => {

        const userStore = useUserStore();
        const user = userStore.user; // Accedemos al valor real del usuario

        console.log('DEDEHDE', userStore.user.roles[0].name); // Verificamos que tenemos el objeto de usuario

        // Determina si el usuario es docente
        const isDocente = user?.roles?.[0]?.name === 'docente';

        // // Obtiene los grupos según el rol del usuario
        // const response = await getGroups(plan, especialty);

        if (isDocente) {
            console.log('DOCENTE');
            console.log('PLAN', plan);
            
            // Si el usuario es docente, realiza la llamada específica a la ruta `grupoDocente`
            try {
                const docenteResponse = await fetch(`/api/grupoDocente/${user.id}/${plan}`);
                if (docenteResponse.ok) {
                    const gruposDocente = await docenteResponse.json();
                    console.log('Grupos específicos del docente para el plan:', gruposDocente);
    
                    // Asigna directamente la respuesta a `groups.value`
                    groups.value = gruposDocente;
                } else {
                    console.error('Error al obtener los grupos específicos del docente');
                }
            } catch (error) {
                console.error('Error de red al obtener los grupos específicos del docente:', error);
            }
        } else {
            // Si es administrativo, carga todos los grupos con el método original `getGroups`
            const response = await getGroups(plan, especialty);
            groups.value = response;
            console.log('Administrativo');
        }
    };

    // Nueva función para cargar una Groupa por su ID
    const loadGroupById = async (id) => {
        const response = await getGroupById(id); // Usamos el método show
        group.value = response;
    };

    return {
        group,
        setGroup,
        groups,
        GroupsLoading,
        GroupsFirstTimeLoading,
        loadGroups,
        loadGroupById, // Retornamos la nueva función para obtener una Groupa por ID
    };
});

export default useGroupsStore;
