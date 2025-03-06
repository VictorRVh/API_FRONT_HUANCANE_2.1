<template>
    <AuthorizationFallback :permissions="['note-student-all', 'note-student-view']">
        <div class="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-lg font-semibold text-center mb-4">
                079 - PROGRAMACIÓN PARA COMPETICIÓN AVANZADO
            </h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <p><strong>Sección:</strong> GRUPO A</p>
                <p><strong>Vez:</strong> 1</p>
                <p><strong>Créditos:</strong> 3.0</p>
                <p><strong>Fórmula:</strong> U1 * 45% + U2 * 55%</p>
            </div>
            <table class="w-full mt-4 border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-2">Promedio de Unidad</th>
                        <th class="border p-2">Porcentaje</th>
                        <th class="border p-2">Nota</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border p-2 font-semibold">UNIDAD 1</td>
                        <td class="border p-2">45%</td>
                        <td class="border p-2">17</td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-6">Evaluación 1ra ...</td>
                        <td class="border p-2">100%</td>
                        <td class="border p-2">17.00</td>
                    </tr>
                    <tr>
                        <td class="border p-2 font-semibold">UNIDAD 2</td>
                        <td class="border p-2">55%</td>
                        <td class="border p-2">15</td>
                    </tr>
                    <tr>
                        <td class="border p-2 pl-6">Evaluación 2da ...</td>
                        <td class="border p-2">100%</td>
                        <td class="border p-2">15.00</td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-4 flex justify-between items-center text-lg font-semibold">
                <p>Nota Oficial:</p>
                <p class="bg-gray-100 p-2 rounded-md">16</p>
            </div>
            <p class="mt-2 text-sm text-gray-600"><strong>Observaciones:</strong> -</p>
        </div>
    </AuthorizationFallback>
</template>

<style scoped>
/* No se requiere CSS adicional, todo está gestionado con Tailwind */
</style>
