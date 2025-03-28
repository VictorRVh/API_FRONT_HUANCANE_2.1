import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import useHttpRequest from '../../composables/useHttpRequest';

const useUnitsStore = defineStore('units', () => {
    // Petición para la primera API
    const {
        show: getUnitById, 
        loading: UnitsLoading1,
        initialLoading: UnitsFirstTimeLoading1,
    } = useHttpRequest('/unidad_didactica');

    // Petición para la segunda API
    const {
        show: getUnitIndex, 
        loading: UnitsLoading2,
        initialLoading: UnitsFirstTimeLoading2,
    } = useHttpRequest('/unidad_didactica_index');

    const Unit = ref(null); 
    const Units = ref([]);
    const indexAll = ref([]);  

    // Estado combinado de carga
    const UnitsLoading = computed(() => UnitsLoading1.value || UnitsLoading2.value);
    const UnitsFirstTimeLoading = computed(() => UnitsFirstTimeLoading1.value || UnitsFirstTimeLoading2.value);

    // Cargar datos de unidad
    const loadUnits = async (id) => {
        const response = await getUnitById(id);
        Units.value = response;
    };

    // Cargar unidad específica
    const loadUnitById = async (id) => {
        const response = await getUnitById(id);
        Unit.value = response;
    };
    const loadUnitAllindex = async (programId) => {
        const response = await getUnitIndex(programId);
        indexAll.value = response;
    };

    return {
        Unit,
        Units,
        indexAll,
        setUnit: (authUnit) => Unit.value = authUnit,
        UnitsLoading,
        UnitsFirstTimeLoading,
        loadUnits,
        loadUnitAllindex,
        loadUnitById,
    };
});

export default useUnitsStore;
