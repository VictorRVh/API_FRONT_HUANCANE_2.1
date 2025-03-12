<script setup>
import { onMounted, ref, watch } from "vue";
import ApexCharts from "apexcharts";
import usePlaceStore from "../../store/Sede/useSedeStore";
import useReportStore from "../../store/Sede/useReporteStore";
import usePlanStore from "../../store/Especialidad/usePlanFormativoStore";

// Stores
const placesStore = usePlaceStore();
const planStore = usePlanStore();
const reportStore = useReportStore();

// Refs
const selectedPlace = ref(0);
const selectedPlan = ref(0);
const opcionesGrafico = ref(null);

// Nueva referencia para manejar la instancia del gráfico
let chartInstance = null;

// Carga inicial de datos (sede y plan)
onMounted(async () => {
  if (!placesStore.Places.length) await placesStore.loadPlaces();
  if (placesStore.Places.length > 0) {
    selectedPlace.value = placesStore.Places[placesStore.Places.length - 1]?.id_sede;
  }

  if (!planStore.plans.length) await planStore.loadPlans();
  if (planStore.plans.length > 0) {
    selectedPlan.value = planStore.plans[planStore.plans.length - 1]?.id_plan;
  }

  await cargarReporte();
});

// Watchers para detectar cambios
watch([selectedPlace, selectedPlan], async ([newPlace, newPlan]) => {
  if (newPlace && newPlan) {
    await cargarReporte();
  }
});

// Función que carga el reporte desde la API y genera el gráfico
const cargarReporte = async () => {
  await reportStore.loadReports(selectedPlan.value, selectedPlace.value);

  if (!reportStore.Reports.length) {
    console.warn("No se encontraron reportes.");
    renderizarGrafico([], []);
    return;
  }

  const categorias = reportStore.Reports.map(report => report.nombre_especialidad);
  const datosSeries = reportStore.Reports.map(report => report.total_alumnos);

  renderizarGrafico(categorias, datosSeries);
};

// Renderizar el gráfico asegurando que no se duplique
const renderizarGrafico = (categorias, datosSeries) => {
  opcionesGrafico.value = {
    chart: {
      type: "bar",
      height: "100%",
      stacked: true,
      fontFamily: "Inter, sans-serif",
      toolbar: { show: false },
    },
    series: [
      {
        name: "Total Alumnos",
        data: datosSeries,
        color: "#921733",
      }
    ],
    xaxis: {
      categorias,
      labels: { style: { fontSize: "14px" } },
    },
    yaxis: {
      labels: {
        formatter: (value) => `${value} estudiantes`,
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
    dataLabels: { enabled: true },
    noData: {
      text: 'No hay datos disponibles',
      align: 'center',
      verticalAlign: 'middle',
      offsetX: 0,
      offsetY: 0,
      style: {
        color: '#888',
        fontSize: '14px'
      }
    }
  };

  const contenedorGrafico = document.getElementById("grafico-matriculas");

  if (contenedorGrafico) {
    // Destruye el gráfico previo antes de crear uno nuevo
    if (chartInstance) {
      chartInstance.destroy();
      chartInstance = null;
    }

    chartInstance = new ApexCharts(contenedorGrafico, opcionesGrafico.value);
    chartInstance.render();
  }
};

// Métodos de cambio en los selects
const changePlace = async () => {
  await cargarReporte();
};

const changePlan = async () => {
  await cargarReporte();
};
</script>

<template>
  <div class="max-w-5xl mx-auto bg-white rounded-lg shadow dark:bg-gray-800 p-6">
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Ciclo Formativo</h2>

    <div class="flex justify-between mb-6">
      <!-- Select de Sede -->
      <select @change="changePlace" v-model="selectedPlace" class="border rounded-md p-2">
        <option value="" disabled>Seleccione una sede</option>
        <option v-for="place in placesStore.Places.sedes" :key="place.id_sede" :value="place.id_sede">
          {{ place.nombre_sede }}
        </option>
      </select>

      <!-- Select de Plan -->
      <select @change="changePlan" v-model="selectedPlan" class="border rounded-md p-2">
        <option value="" disabled>Seleccione un plan</option>
        <option v-for="plan in planStore.plans" :key="plan.id_plan" :value="plan.id_plan">
          {{ plan.nombre_plan }}
        </option>
      </select>
    </div>

    <!-- Contenedor del gráfico -->
    <div id="grafico-matriculas" class="w-full h-96"></div>
  </div>
</template>
