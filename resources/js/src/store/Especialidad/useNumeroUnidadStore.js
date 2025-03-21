import { ref } from 'vue';
import { defineStore } from 'pinia';

const useNumeroUnidadStore = defineStore('numero_unidad', () => {
    const numeroUnidad = ref([]);
    const role = ref(null);

    const loadNumeroUnidad = async () => {
        const unidadesDidacticas = [
            'Unidad-1', 'Unidad-2', 'Unidad-3', 
            'Unidad-4', 'Unidad-5', 'Unidad-6', 
            'Unidad-7', 'Unidad-8', 'Unidad-9', 'Unidad-10'
        ];
        numeroUnidad.value = unidadesDidacticas;
    };

    const setRole = (authRole) => {
        role.value = authRole;
    };

    return {
        role,
        numeroUnidad,
        loadNumeroUnidad, 
        setRole,
    };
});

export default useNumeroUnidadStore;
