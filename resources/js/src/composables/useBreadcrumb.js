import { computed, ref, watchEffect } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

export function useBreadcrumb() {
  const route = useRoute();
  const breadcrumbs = ref([]);

  watchEffect(async () => {
    const matchedRoutes = route.matched;
    const breadcrumbList = [];

    for (const r of matchedRoutes) {
      let text = r.meta.breadcrumb;
      let path = r.path;

      // Reemplazar los parámetros dinámicos en la URL
      if (r.name === 'especialidad' && route.params.id) {
        try {
          const response = await axios.get(`/api/especialidad/${route.params.id}`);
          text = response.data.nombre; // Ejemplo: "Ingeniería de Software"
          path = `/especialidades/${route.params.id}`;
        } catch (error) {
          text = 'Especialidad Desconocida';
        }
      }

      if (r.name === 'subespecialidad' && route.params.subid) {
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
