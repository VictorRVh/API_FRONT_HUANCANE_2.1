import { ref, watchEffect } from 'vue';
import { useRoute } from 'vue-router';
import useHttpRequest from './useHttpRequest';

export function useBreadcrumb() {
  const route = useRoute();
  const breadcrumbs = ref([]);

  watchEffect(async () => {
    const breadcrumbList = [];

    // Métodos para obtener datos de la API
    const { show: getSpecialtyById } = useHttpRequest('/especialidad');

    // Siempre agregamos la primera ruta "Especialidades"
    breadcrumbList.push({
      text: 'Especialidades',
      path: '/especialidad',
    });

    // Si hay una especialidad seleccionada
  /*  if (route.params.idEspecialidad) {
      try {
        const response = await getSpecialtyById(route.params.idEspecialidad);
        breadcrumbList.push({
          text: response.nombre_especialidad,
          path: `/especialidad/${route.params.idEspecialidad}`,
        });
      } catch (error) {
        breadcrumbList.push({
          text: 'Especialidad Desconocida',
          path: '/especialidad',
        });
      }
    }
    */

    // Si hay un programa formativo dentro de la especialidad
    if (route.name === 'programaFormativo' && route.params.idEspecialidad) {
      breadcrumbList.push({
        text: 'Programa Formativo',
        path: `/especialidad/${route.params.idEspecialidad}`,
      });
    }

    // Si hay una unidad didáctica dentro del programa
    if (route.name === 'UnidadDidactica' && route.params.idPrograma) {
      breadcrumbList.push({
        text: 'Unidad Didáctica',
        path: `/UnidadDidactica/${route.params.idEspecialidad}/${route.params.idPrograma}`,
      });
    }

    breadcrumbs.value = breadcrumbList;
  });

  return { breadcrumbs };
}
