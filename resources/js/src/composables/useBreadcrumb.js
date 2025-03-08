import { computed, ref, watchEffect } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { defineStore } from 'pinia';
// useHttpRequest para interactuar con la API
import useHttpRequest from './useHttpRequest';

export function useBreadcrumb() {
  const route = useRoute();
  const breadcrumbs = ref([]);

  watchEffect(async () => {
    const matchedRoutes = route.matched;
    const breadcrumbList = [];

    const {
      index: getSpecialties,
      show: getSpecialtyById, // Añadimos el método show para obtener una especialidad por ID
      loading: specialtiesLoading,
      initialLoading: specialtiesFirstTimeLoading,
  } = useHttpRequest('/especialidad');

    for (const r of matchedRoutes) {
      let text = r.meta.breadcrumb;
      let path = r.path;
      // Reemplazar los parámetros dinámicos en la URL
      if (r.name === 'programaFormativo' && route.params.idEspecialidad) {
        try {
          const response = await getSpecialtyById(route.params.idEspecialidad); // Usamos el método show
          console.log(" useBeradCrum ",response.nombre_especialidad)
          text = response.nombre_especialidad; // Ejemplo: "Ingeniería de Software"
          path = `/especialidades/${route.params.idEspecialidad}`;
        } catch (error) {
          text = 'Especialidad Desconocida';
        }
      }

      if (r.name === 'UnidadDidactica' && route.params.idPrograma) {
        try {
          const response = await axios.get(`/api/subespecialidad/${route.params.subid}`);
          text = response.data.nombre; // Ejemplo: "Desarrollo Web"
          path = `/especialidades/${route.params.id}/${route.params.subid}`;
        } catch (error) {
          text = 'Subespecialidad Desconocida';
        }
      }

      breadcrumbList.push({ text, path });
    }

    breadcrumbs.value = breadcrumbList;
  });

  return { breadcrumbs };
}
