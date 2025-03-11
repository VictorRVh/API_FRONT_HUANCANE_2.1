<script setup>
import { onMounted, ref, watch } from "vue";
import axios from "axios";
import ApexCharts from "apexcharts";

// Opciones dinámicas para sedes y periodos
const sedes = ref([]);
const periodos = ref(["2023-I", "2023-II", "2024-I", "2024-II"]); // Periodos disponibles
const sedeSeleccionada = ref(null);
const periodoSeleccionado = ref(null); // Nuevo input para el periodo académico

// Datos reactivos de estudiantes
const datosEstudiantes = ref({
  categorias: [],
  series: [],
  total: 0,
});

// Función para obtener las sedes desde el backend
const obtenerSedes = async () => {
  try {
    const response = await axios.get("/api/sedes");
    sedes.value = response.data.sedes;
    if (sedes.value.length > 0) {
      sedeSeleccionada.value = sedes.value[0].id_sede;
    }
  } catch (error) {
    console.error("Error al obtener sedes:", error);
  }
};

// Función para obtener datos de estudiantes desde el backend
const obtenerAlumnos = async () => {
  if (!sedeSeleccionada.value || !periodoSeleccionado.value) {
    console.warn("Debe seleccionar un periodo y una sede.");
    return;
  }

  try {
    const response = await axios.get(
      `/api/reporte-alumnos/${periodoSeleccionado.value}/${sedeSeleccionada.value}`
    );

    procesarDatosEstudiantes(response.data.alumnos || []);
  } catch (error) {
    console.error("Error al obtener alumnos:", error);
  }
};

// Función para procesar los datos de estudiantes recibidos del backend
const procesarDatosEstudiantes = (data) => {
  const categorias = [];
  const primerPlan = [];
  const segundoPlan = [];
  let totalEstudiantes = 0;

  // Agrupar datos por especialidad y plan de estudios
  const agrupados = {};
  data.forEach((item) => {
    if (!agrupados[item.id_especialidad]) {
      agrupados[item.id_especialidad] = {
        nombre: item.nombre_especialidad,
        primerPlan: 0,
        segundoPlan: 0,
      };
    }
    if (item.id_plan === 1) {
      agrupados[item.id_especialidad].primerPlan = item.total_alumnos;
    } else if (item.id_plan === 2) {
      agrupados[item.id_especialidad].segundoPlan = item.total_alumnos;
    }
  });

  // Convertir datos agrupados en formato para ApexCharts
  Object.values(agrupados).forEach((esp) => {
    categorias.push(esp.nombre);
    primerPlan.push(esp.primerPlan);
    segundoPlan.push(esp.segundoPlan);
    totalEstudiantes += esp.primerPlan + esp.segundoPlan;
  });

  datosEstudiantes.value = {
    categorias,
    series: [
      { name: "Primer Plan de Estudios", data: primerPlan, color: "#921733" },
      { name: "Segundo Plan de Estudios", data: segundoPlan, color: "#701261" },
    ],
    total: totalEstudiantes,
  };

  renderizarGrafico();
};

// Función para renderizar el gráfico con ApexCharts
const renderizarGrafico = () => {
  const opcionesGrafico = {
    chart: {
      type: "bar",
      height: "100%",
      stacked: true,
      fontFamily: "Inter, sans-serif",
      toolbar: { show: false },
    },
    series: datosEstudiantes.value.series,
    xaxis: {
      categories: datosEstudiantes.value.categorias,
      labels: { style: { fontSize: "14px" } },
    },
    yaxis: {
      labels: {
        formatter: (value) => value + " estudiantes",
      },
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: "60%",
        borderRadius: 4,
      },
    },
    fill: { opacity: 1 },
    legend: { position: "top" },
    dataLabels: { enabled: false },
  };

  const contenedorGrafico = document.getElementById("grafico-matriculas");
  if (contenedorGrafico) {
    contenedorGrafico.innerHTML = ""; // Limpiar gráfico anterior
    const chart = new ApexCharts(contenedorGrafico, opcionesGrafico);
    chart.render();
  }
};

// Watch para actualizar los datos cuando cambian la sede o el período seleccionado
watch([sedeSeleccionada, periodoSeleccionado], () => {
  obtenerAlumnos();
});

// Cargar datos al montar el componente
onMounted(() => {
  obtenerSedes();
});
</script>

<template>
  <div class="max-w-5xl mx-auto bg-white rounded-lg shadow dark:bg-gray-800 p-6">
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Ciclo Formativo</h2>

    <div class="grid grid-cols-2 gap-4 mb-6">
      <!-- Selección de sede -->
      <div>
        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Seleccione una sede:
        </label>
        <select v-model="sedeSeleccionada" class="w-full border rounded-md p-2 dark:bg-gray-700 dark:text-white">
          <option disabled value="">Seleccione una sede</option>
          <option v-for="sede in sedes" :key="sede.id_sede" :value="sede.id_sede">
            {{ sede.nombre_sede }}
          </option>
        </select>
      </div>

      <!-- Selección de período académico -->
      <div>
        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Seleccione un período académico:
        </label>
        <select v-model="periodoSeleccionado" class="w-full border rounded-md p-2 dark:bg-gray-700 dark:text-white">
          <option disabled value="">Seleccione un período</option>
          <option v-for="periodo in periodos" :key="periodo" :value="periodo">
            {{ periodo }}
          </option>
        </select>
      </div>
    </div>

    <!-- Gráfico y resultados generales -->
    <div class="flex gap-6">
      <!-- Gráfico -->
      <div class="flex-1">
        <div id="grafico-matriculas" class="w-full h-96"></div>
      </div>

      <!-- Resultados generales -->
      <div class="w-64 bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
        <h5 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Resultados Generales</h5>
        <p class="text-base font-medium text-gray-600 dark:text-gray-300">
          Total de estudiantes matriculados:
        </p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">
          {{ datosEstudiantes.total }}
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Estilos personalizados (opcional) */
</style>
