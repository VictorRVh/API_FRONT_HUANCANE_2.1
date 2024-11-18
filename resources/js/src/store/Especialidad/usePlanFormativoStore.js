import { ref } from 'vue';
import { defineStore } from 'pinia';
// useEspecialidadStore
import useHttpRequest from '../../composables/useHttpRequest';

const usePlansStore = defineStore('plans', () => {
    const {
        index: getPlans,
       // show:getPlansByIdEspeciality,
        loading: plansLoading,
        initialLoading: plansFirstTimeLoading,
    } = useHttpRequest('/plan');

    const plan = ref(null);
    const plans = ref([]);

    const setPlan = (authplan) => {
        plan.value = authplan;
    };

    const loadPlans = async () => {
        const response = await getPlans();
        plans.value = response;
    };
    // Nueva función para cargar una especialidad por su ID
    /* const loadPlanByIdEsp = async (id) => {
        const response = await getPlansByIdEspeciality(id); // Usamos el método show
        plans.value = response;
    };
    */

    return {
        plan,
        setPlan,
        plans,
        plansLoading,
        plansFirstTimeLoading,
        loadPlans,
       // loadPlanByIdEsp,
    };
});

export default usePlansStore;
