<script setup>
import { onMounted, ref, watch, computed } from "vue";
import ApexCharts from "apexcharts";

import usePlaceStore from "../../store/Sede/useSedeStore";
import useReportStore from "../../store/Sede/useReporteStore";
import usePlanStore from "../../store/Especialidad/usePlanFormativoStore";

const placesStore = usePlaceStore();
const planStore = usePlanStore();
const reportStore = useReportStore();

const selectedPlace = ref(0);
const selectedPlan = ref(0);
const opcionesGrafico = ref(null);
let chartInstance = null;

const isDarkMode = ref(false);

// Computed: Nombre de la sede seleccionada
const nombreSedeSeleccionada = computed(() => {
  const sede = placesStore.Places?.sedes?.find(
    (s) => s.id_sede === selectedPlace.value
  );
  return sede ? sede.nombre_sede : "Ninguna";
});

// Computed: Total de alumnos
const totalAlumnos = computed(() => {
  return reportStore.Reports.reduce(
    (acc, report) => acc + report.total_alumnos,
    0
  );
});

// --- 🚀 Función principal para cargar los datos iniciales ---
onMounted(async () => {
  await cargarDatosIniciales();
  observarClaseDark(); // inicia observer de modo dark
});

// --- 🗂 Cargar datos iniciales (solo si no están cargados) ---
const cargarDatosIniciales = async () => {
  if (!placesStore.Places.sedes?.length) {
    await placesStore.loadPlaces();
  }

  selectedPlace.value = placesStore.Places.sedes.at(-1)?.id_sede || 0;

  if (!planStore.plans.length) {
    await planStore.loadPlans();
  }

  selectedPlan.value = planStore.plans.at(-1)?.id_plan || 0;

  if (selectedPlace.value && selectedPlan.value) {
    await cargarReporte();
  }
};

// --- 👀 Watchers para detectar cambios en sede/plan ---
watch([selectedPlace, selectedPlan], async ([newPlace, newPlan], [oldPlace, oldPlan]) => {
  if (!newPlace || !newPlan) return;

  // Solo carga si el cambio es distinto
  const cambioSede = newPlace !== oldPlace;
  const cambioPlan = newPlan !== oldPlan;

  if (cambioSede || cambioPlan) {
    await cargarReporte();
  }
});

// --- 📦 Cargar reporte según sede y plan ---
const cargarReporte = async () => {
  await reportStore.loadReports(selectedPlan.value, selectedPlace.value);

  const categorias = reportStore.Reports.map(report => report.nombre_especialidad);
  const datosSeries = reportStore.Reports.map(report => report.total_alumnos);

  renderizarGrafico(categorias, datosSeries);
};

// --- 🌗 Observar el cambio de modo dark/light solo para actualizar el gráfico ---
const observarClaseDark = () => {
  const observer = new MutationObserver(() => {
    const darkNow = document.documentElement.classList.contains('dark');

    if (darkNow !== isDarkMode.value) {
      isDarkMode.value = darkNow;
      console.log("Modo dark cambió:", isDarkMode.value);

      // Solo renderiza el gráfico con el modo actual, NO vuelve a pedir datos
      const categorias = reportStore.Reports.map(report => report.nombre_especialidad);
      const datosSeries = reportStore.Reports.map(report => report.total_alumnos);

      renderizarGrafico(categorias, datosSeries);
    }
  });

  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class'],
  });

  isDarkMode.value = document.documentElement.classList.contains('dark');
};

// --- 📊 Renderizar gráfico (si ya hay instancia, destruye y crea otra) ---
const renderizarGrafico = (categories, datosSeries) => {
  opcionesGrafico.value = {
    theme: {
      mode: isDarkMode.value ? 'dark' : 'light',
      palette: 'palette1',
    },
    chart: {
      type: "bar",
      height: "100%",
      stacked: true,
      fontFamily: "Inter, sans-serif",
      toolbar: { show: false },
      background: 'transparent',
    },
    series: [{
      name: "Total Alumnos",
      data: datosSeries,
      color: "#921733",
    }],
    xaxis: {
      categories,
      labels: {
        style: {
          fontSize: "14px",
          color: isDarkMode.value ? "#0f0" : "#333",
        },
      },
    },
    yaxis: {
      labels: {
        style: {
          color: isDarkMode.value ? "#00f" : "#333",
        },
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
      text: "No hay datos disponibles",
      align: "center",
      style: { color: "#888", fontSize: "14px" },
    },
  };

  const contenedorGrafico = document.getElementById("grafico-matriculas");

  if (contenedorGrafico) {
    if (chartInstance) {
      chartInstance.destroy();
      chartInstance = null;
    }

    chartInstance = new ApexCharts(contenedorGrafico, opcionesGrafico.value);
    chartInstance.render();
  }
};

// --- 🌐 Cambios de select (ya controlados por watchers, estos son opcionales) ---
const changePlace = async () => {
  // Si ya está controlado por watch, este método puede ser innecesario
  // await cargarReporte();
};

const changePlan = async () => {
  // Si ya está controlado por watch, este método puede ser innecesario
  // await cargarReporte();
};
</script>


<template>
  <div class="max-w-6xl mx-auto bg-white rounded-lg shadow dark:bg-gray-800 p-6">
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">
      Ciclo Formativo
    </h2>

    <!-- Filtros -->
    <div class="flex justify-between mb-6 flex-wrap gap-4">
      <!-- Sede -->
      <select @change="changePlace" v-model="selectedPlace"
        class="  border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md p-2 w-48 focus:ring-[#921733] focus:border-[#921733]">
        <option value="" disabled>Seleccione una sede</option>
        <option v-for="place in placesStore.Places.sedes" :key="place.id_sede" :value="place.id_sede">
          {{ place.nombre_sede }}
        </option>
      </select>

      <!-- Plan -->
      <select @change="changePlan" v-model="selectedPlan"
        class=" border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md p-2 w-48 focus:ring-[#921733] focus:border-[#921733]">
        <option value="" disabled>Seleccione un plan</option>
        <option v-for="plan in planStore.plans" :key="plan.id_plan" :value="plan.id_plan">
          {{ plan.nombre_plan }}
        </option>
      </select>
    </div>

    <div class="flex gap-6 flex-col lg:flex-row">
      <!-- Gráfico -->
      <div id="grafico-matriculas" class="flex-1 h-96 border border-gray-200 dark:border-gray-700 rounded-lg p-4"></div>

      <!-- Resumen -->
      <div class="w-full lg:w-1/4 bg-gray-100 dark:bg-gray-700 rounded-lg p-4 flex flex-col justify-center">
        <h3 class="text-lg font-bold text-gray-700 dark:text-white mb-4">
          Resumen
        </h3>

        <div class="mb-4">
          <p class="text-sm text-gray-600 dark:text-gray-300">Sede:</p>
          <p class="text-md font-semibold text-gray-800 dark:text-white break-words">
            {{ nombreSedeSeleccionada }}
          </p>
        </div>

        <div>
          <p class="text-sm text-gray-600 dark:text-gray-300">
            Total Alumnos:
          </p>
          <p class="text-3xl font-bold text-[#921733]">
            {{ totalAlumnos }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Aquí van estilos adicionales si necesitas */
</style>
