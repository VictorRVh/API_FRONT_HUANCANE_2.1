import { ref, watchEffect } from 'vue';
import { useRoute } from 'vue-router';
import useHttpRequest from './useHttpRequest';

export function useBreadcrumb() {
  const route = useRoute();
  const breadcrumbs = ref([]);

  watchEffect(async () => {
    const breadcrumbList = [];

    // Obtenemos la función para consultar especialidades por ID
    const { show: getSpecialtyById } = useHttpRequest('/especialidad');

    // Agregamos la ruta principal de especialidades
    breadcrumbList.push({
      text: 'Especialidades',
      path: '/especialidad',
    });

    //  Si hay una especialidad seleccionada
    if (route.params.idEspecialidad) {
      try {
        const response = await getSpecialtyById(route.params.idEspecialidad);
        breadcrumbList[0].text = "Especialidad de " + response.nombre_especialidad; // Modifica el primer breadcrumb

        breadcrumbList.push({
          text: 'Programa Formativo',
          path: `/especialidad/${route.params.idEspecialidad}`,
        });
      } catch (error) {
        console.error("Error obteniendo especialidad:", error);
        breadcrumbList.push({
          text: 'Especialidad Desconocida',
          path: `/especialidad/${route.params.idEspecialidad}`,
        });
      }
    }

    //  Si hay un programa formativo dentro de la especialidad
    if (route.name === "UnidadDidactica" ||route.name === "IndicadorLogro" &&route.params.idPrograma) {
      breadcrumbList.push({
        text: 'Unidad Didáctica',
        path: `/UnidadDidactica/${route.params.idEspecialidad}/${route.params.idPrograma}`,
      });
    }

    //  Si la ruta es "Experiencia Formativa"
    if (route.name === "ExperienciaFormativa" && route.params.idPrograma) {
      breadcrumbList.push({
        text: 'Experiencia Formativa',
        path: `/ExperienciaFormativa/${route.params.idEspecialidad}/${route.params.idPrograma}`,
      });
    }

    //  Si hay una unidad seleccionada
    if (route.params.idUnidad) {
      breadcrumbList.push({
        text: 'Indicador de Logro',
        path: `/IndicadorLogro/${route.params.idEspecialidad}/${route.params.idPrograma}/${route.params.idUnidad}`,
      });
    }

    //  Actualizar breadcrumbs
    breadcrumbs.value = breadcrumbList;
  });

  return { breadcrumbs };
}
