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


    // Para todas la rutas :::::::
    if (Object.keys(route.params).length === 0) {
      console.log("Ruta de vic:", route)
      breadcrumbList.push({
        text: route.meta.breadcrumb,
        path: route.path,
      });
    }
    // ESPECIALIDAD
    if (route.params.idEspecialidad) {
      breadcrumbList.push({
        text: 'Especialidad',
        path: `/especialidad`,
      });
    }
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
    if (route.name === "UnidadDidactica" || route.name === "IndicadorLogro" && route.params.idPrograma) {
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

    // GRUPOS
    if (route.params.idGroupAll) {
      breadcrumbList.push({
        text: 'Grupos',
        path: `/grupos`,
      });
    }

    if (route.params.idGroupAll) {
      breadcrumbList[0].text = "Grupo"; // Modifica el primer breadcrumb
      breadcrumbList.push({
        text: 'Estudiantes',
        path: `/grupos/${route.params.idGroupAll}`,
      });
    }

    if (route.params.certifyId) {
      breadcrumbList.push({
        text: 'Certificados',
        path: `/certificado`,
      });
    }

    if (route.params.certifyId) {
      //  breadcrumbList[0].text = "Grupo"; // Modifica el primer breadcrumb
      breadcrumbList.push({
        text: 'Estudiantes',
        path: `/certificado/${route.params.certifyId}`,
      });
    }

    if (route.params.idExperiencie || route.params.idUnitNote) {
      breadcrumbList.push({
        text: 'Notas',
        path: `/notas`,
      });
    }

    if (route.params.idExperiencie) {
      //  breadcrumbList[0].text = "Grupo"; // Modifica el primer breadcrumb
      breadcrumbList.push({
        text: 'Notas de Experiencias',
        path: `/notasExperience/${route.params.idExperiencie}`,
      });
    }

    if (route.params.idUnitNote) {
      //  breadcrumbList[0].text = "Grupo"; // Modifica el primer breadcrumb
      breadcrumbList.push({
        text: 'Notas de Unidades',
        path: `/notasUnit/${route.params.idUnitNote}`,
      });
    }
    
    if (route.params.idNoteStudent) {
      //  breadcrumbList[0].text = "Grupo"; // Modifica el primer breadcrumb
      breadcrumbList.push({
        text: 'Notas',
        path: `/notas_student`,
      });
    }
    if (route.params.idNoteStudent) {
      //  breadcrumbList[0].text = "Grupo"; // Modifica el primer breadcrumb
      breadcrumbList.push({
        text: 'Notas del Periodo',
        path: `/notas/${route.params.idNoteStudent}`,
      });
    }
    //  Actualizar breadcrumbs
    breadcrumbs.value = breadcrumbList;
  });



  return { breadcrumbs };
}
