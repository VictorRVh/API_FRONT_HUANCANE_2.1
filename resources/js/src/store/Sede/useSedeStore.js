import { ref } from 'vue';
import { defineStore } from 'pinia';
// useHttpRequest para interactuar con la API
import useHttpRequest from '../../composables/useHttpRequest';

const usePlacesStore = defineStore('places', () => {
    // Importamos las funciones necesarias de useHttpRequest
    const {
        index: getPlaces,
        show: getPlaceById, // Añadimos el método show para obtener una especialidad por ID
        loading: PlacesLoading,
        initialLoading: PlacesFirstTimeLoading,
    } = useHttpRequest('/sedes');

    const Place = ref(null); // Para almacenar una sola especialidad
    const Places = ref([]);  // Para almacenar la lista de especialidades

    // Función para establecer una especialidad específica
    const setPlace = (authPlace) => {
        Place.value = authPlace;
    };

    // Función para cargar todas las especialidades
    const loadPlaces = async () => {
        const response = await getPlaces();
        Places.value = response;
    };

    // Nueva función para cargar una especialidad por su ID
    const loadPlaceById = async (id) => {
        const response = await getPlaceById(id); // Usamos el método show
        Place.value = response;
    };

    return {
        Place,
        setPlace,
        Places,
        PlacesLoading,
        PlacesFirstTimeLoading,
        loadPlaces,
        loadPlaceById, // Retornamos la nueva función para obtener una especialidad por ID
    };
});

export default usePlacesStore;
