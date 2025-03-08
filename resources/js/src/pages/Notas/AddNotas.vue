<template>
  <div class="w-full space-y-4 py-6">
    <!-- Título -->
    <header class="flex justify-between">
      <h2 class="text-black font-bold text-2xl">Estudiantes</h2>
    </header>

    <!-- Tabla de estudiantes -->
    <section class="w-full">
      <div class="border border-gray-300 rounded-lg shadow-md overflow-hidden">
        <table class="w-full border-collapse divide-y divide-gray-300">
          <!-- Encabezado -->
          <thead class="bg-gray-200">
            <tr>
              <th class="py-3 px-4 text-left text-black">Id</th>
              <th class="py-3 px-4 text-left text-black">Nombre Completo</th>
              <th class="py-3 px-4 text-left text-black">Nota</th>
            </tr>
          </thead>

          <!-- Cuerpo de la tabla (Datos de ejemplo) -->
          <tbody class="divide-y divide-gray-300">
            <tr class="bg-white">
              <td class="py-3 px-4 text-black">101</td>
              <td class="py-3 px-4 text-black">Juan Pérez</td>
              <td class="py-3 px-4 text-black">
                <input type="text" class="w-[50px] text-center border border-gray-400 rounded-md" value="18">
              </td>
            </tr>
            <tr class="bg-gray-50">
              <td class="py-3 px-4 text-black">102</td>
              <td class="py-3 px-4 text-black">María Gómez</td>
              <td class="py-3 px-4 text-black">
                <input type="text" class="w-[50px] text-center border border-gray-400 rounded-md text-red-500" value="9">
              </td>
            </tr>
            <tr class="bg-white">
              <td class="py-3 px-4 text-black">103</td>
              <td class="py-3 px-4 text-black">Carlos López</td>
              <td class="py-3 px-4 text-black">
                <input type="text" class="w-[50px] text-center border border-gray-400 rounded-md" value="15">
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Botón para guardar -->
      <div class="flex justify-end mt-4">
        <button class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 transition">
          Guardar
        </button>
      </div>
    </section>
  </div>
</template>
