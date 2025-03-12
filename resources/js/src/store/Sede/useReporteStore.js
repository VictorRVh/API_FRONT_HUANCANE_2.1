import { ref } from 'vue';
import { defineStore } from 'pinia';
// useHttpRequest para interactuar con la API
import useHttpRequest from '../../composables/useHttpRequest';

const useReportsStore = defineStore('reports', () => {
    // Importamos las funciones necesarias de useHttpRequest
    const {
        index: getReports,
        show: getReportById, // Añadimos el método show para obtener una especialidad por ID
        showTwo:getReportsPlanAndPlace,
        loading: ReportsLoading,
        initialLoading: ReportsFirstTimeLoading,
    } = useHttpRequest('/reporte-alumnos');

    const Report = ref(null); // Para almacenar una sola especialidad
    const Reports = ref([]);  // Para almacenar la lista de especialidades

    // Función para establecer una especialidad específica
    const setReport = (authReport) => {
        Report.value = authReport;
    };

    // Función para cargar todas las especialidades
    const loadReports = async (planId,placeId) => {
        const response = await getReportsPlanAndPlace(planId,placeId);
        Reports.value = response;
    };

    // Nueva función para cargar una especialidad por su ID
    const loadReportById = async (id) => {
        const response = await getReportById(id); // Usamos el método show
        Report.value = response;
    };

    return {
        Report,
        setReport,
        Reports,
        ReportsLoading,
        ReportsFirstTimeLoading,
        loadReports,
        loadReportById, // Retornamos la nueva función para obtener una especialidad por ID
    };
});

export default useReportsStore;
