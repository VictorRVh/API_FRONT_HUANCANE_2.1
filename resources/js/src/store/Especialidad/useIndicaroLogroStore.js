import { ref } from 'vue';
import { defineStore } from 'pinia';
// useHttpRequest para interactuar con la API
import useHttpRequest from '../../composables/useHttpRequest';

const useIndicatorsStore = defineStore('Indicators', () => {
    // Importamos las funciones necesarias de useHttpRequest
    const {
        //index: getIndicators,
        show: getIndicatorById, // Añadimos el método show para obtener una especialidad por ID
        show: getIndicators,
        loading: IndicatorsLoading,
        initialLoading: IndicatorsFirstTimeLoading,
    } = useHttpRequest('/indicador_logro');

    const Indicator = ref(null); // Para almacenar una sola especialidad
    const Indicators = ref([]);  // Para almacenar la lista de especialidades

    // Función para establecer una especialidad específica
    const setIndicator = (authIndicator) => {
        Indicator.value = authIndicator;
    };

    // Función para cargar todas las especialidades
    const loadIndicators = async (id) => {
        const response = await getIndicators(id);
        Indicators.value = response;
    };

    // Nueva función para cargar una especialidad por su ID
    const loadIndicatorById = async (id) => {
        const response = await getIndicatorById(id); // Usamos el método show
        Indicator.value = response;
    };

    return {
        Indicator,
        setIndicator,
        Indicators,
        IndicatorsLoading,
        IndicatorsFirstTimeLoading,
        loadIndicators,
        loadIndicatorById, // Retornamos la nueva función para obtener una especialidad por ID
    };
});

export default useIndicatorsStore;
