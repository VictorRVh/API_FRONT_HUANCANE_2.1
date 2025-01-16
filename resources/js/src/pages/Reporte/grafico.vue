<script setup>
import { onMounted, ref, watch } from "vue";
import ApexCharts from "apexcharts";

// Opciones dinámicas para las sedes
const sedes = ref([
  { id: 1, nombre: "HUANCANÉ" },
  { id: 2, nombre: "ROSASPATA" },
  { id: 3, nombre: "PUSY" },
]);

const especialidades = ref([]);
const sedeSeleccionada = ref(null);

// Datos para el gráfico
const opcionesGrafico = ref(null);

// Datos reactivos de estudiantes
const datosEstudiantes = ref({
  categorias: [],
  series: [],
  total: 0,
});

// Datos simulados con 6 o 7 especialidades por sede
const datosSimulados = {
  1: {
    especialidades: [
      { id: 1, nombre: "Ingeniería" },
      { id: 2, nombre: "Artes" },
      { id: 3, nombre: "Negocios" },
      { id: 4, nombre: "Computación" },
      { id: 5, nombre: "Agronomía" },
      { id: 6, nombre: "Enfermería" },
    ],
    estudiantes: {
      1: { primerCiclo: 120, segundoCiclo: 80 },
      2: { primerCiclo: 70, segundoCiclo: 60 },
      3: { primerCiclo: 90, segundoCiclo: 110 },
      4: { primerCiclo: 100, segundoCiclo: 120 },
      5: { primerCiclo: 130, segundoCiclo: 140 },
      6: { primerCiclo: 80, segundoCiclo: 90 },
    },
  },
  2: {
    especialidades: [
      { id: 7, nombre: "Diseño" },
      { id: 8, nombre: "Derecho" },
      { id: 9, nombre: "Arquitectura" },
      { id: 10, nombre: "Psicología" },
      { id: 11, nombre: "Contabilidad" },
      { id: 12, nombre: "Turismo" },
    ],
    estudiantes: {
      7: { primerCiclo: 50, segundoCiclo: 30 },
      8: { primerCiclo: 90, segundoCiclo: 70 },
      9: { primerCiclo: 70, segundoCiclo: 50 },
      10: { primerCiclo: 60, segundoCiclo: 80 },
      11: { primerCiclo: 110, segundoCiclo: 90 },
      12: { primerCiclo: 40, segundoCiclo: 60 },
    },
  },
  3: {
    especialidades: [
      { id: 13, nombre: "Medicina" },
      { id: 14, nombre: "Enfermería" },
      { id: 15, nombre: "Veterinaria" },
      { id: 16, nombre: "Biología" },
      { id: 17, nombre: "Matemáticas" },
      { id: 18, nombre: "Física" },
    ],
    estudiantes: {
      13: { primerCiclo: 200, segundoCiclo: 150 },
      14: { primerCiclo: 110, segundoCiclo: 90 },
      15: { primerCiclo: 120, segundoCiclo: 100 },
      16: { primerCiclo: 130, segundoCiclo: 110 },
      17: { primerCiclo: 90, segundoCiclo: 80 },
      18: { primerCiclo: 100, segundoCiclo: 70 },
    },
  },
};

// Función para cargar especialidades según la sede seleccionada
const cargarEspecialidades = (idSede) => {
  especialidades.value = datosSimulados[idSede]?.especialidades || [];
  cargarDatosEstudiantes(idSede);
};

// Función para cargar los datos de estudiantes de todas las especialidades de una sede
const cargarDatosEstudiantes = (idSede) => {
  const especialidadesSede = datosSimulados[idSede]?.especialidades || [];
  const estudiantesSede = datosSimulados[idSede]?.estudiantes || {};

  datosEstudiantes.value.categorias = especialidadesSede.map((esp) => esp.nombre);
  datosEstudiantes.value.series = [
    {
      name: "Primer Plan de Estudios",
      data: especialidadesSede.map((esp) => estudiantesSede[esp.id]?.primerCiclo || 0),
      color: "#921733", // Granate
    },
    {
      name: "Segundo Plan de Estudios",
      data: especialidadesSede.map((esp) => estudiantesSede[esp.id]?.segundoCiclo || 0),
      color: "#701261", // Violeta
    },
  ];
  datosEstudiantes.value.total = especialidadesSede.reduce(
    (total, esp) =>
      total +
      (estudiantesSede[esp.id]?.primerCiclo || 0) +
      (estudiantesSede[esp.id]?.segundoCiclo || 0),
    0
  );

  renderizarGrafico();
};

// Función para renderizar el gráfico con ApexCharts
const renderizarGrafico = () => {
  opcionesGrafico.value = {
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
        dataLabels: {
          total: {
            enabled: true,
            offsetY: -20,
            style: {
              fontSize: "14px",
              colors: ["#000000"],
            },
            formatter: function (val, opts) {
              const primerCiclo = opts.w.config.series[0].data[opts.dataPointIndex];
              const segundoCiclo = opts.w.config.series[1].data[opts.dataPointIndex];
              return primerCiclo + segundoCiclo + "*";
            },
          },
        },
      },
    },
    fill: { opacity: 1 },
    legend: { position: "top" },
    dataLabels: { enabled: false },
  };

  const contenedorGrafico = document.getElementById("grafico-matriculas");
  if (contenedorGrafico) {
    contenedorGrafico.innerHTML = ""; // Limpiar gráfico anterior
    const chart = new ApexCharts(contenedorGrafico, opcionesGrafico.value);
    chart.render();
  }
};

// Watch para actualizar especialidades y datos al cambiar de sede
watch(sedeSeleccionada, (idSede) => {
  if (idSede) {
    cargarEspecialidades(idSede);
  }
});

onMounted(() => {
  sedeSeleccionada.value = 1; // Seleccionar la primera sede por defecto
});
</script>

<template>
  <div class="max-w-5xl mx-auto bg-white rounded-lg shadow dark:bg-gray-800 p-6">
    <!-- Selección de sede -->
    <div class="mb-6">
      <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
        Seleccione una sede:
      </label>
      <select
        v-model="sedeSeleccionada"
        class="w-full border rounded-md p-2 dark:bg-gray-700 dark:text-white"
      >
        <option disabled value="">Seleccione una sede</option>
        <option v-for="sede in sedes" :key="sede.id" :value="sede.id">
          {{ sede.nombre }}
        </option>
      </select>
    </div>

    <!-- Gráfico y resultados generales -->
    <div class="flex gap-6">
      <!-- Gráfico -->
      <div class="flex-1">
        <div id="grafico-matriculas" class="w-full h-96"></div>
      </div>

      <!-- Resultados generales -->
      <div class="w-64 bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
        <h5 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
          Resultados Generales
        </h5>
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
