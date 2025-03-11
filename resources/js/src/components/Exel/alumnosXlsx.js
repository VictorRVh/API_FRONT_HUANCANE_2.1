
import * as XLSX from 'xlsx';
import { saveAs } from 'file-saver';

/**
 * Exporta datos a un archivo Excel (.xlsx)
 * @param {Array} data - Los datos en formato JSON (Array de objetos)
 * @param {String} filename - Nombre del archivo (ej: 'alumnos.xlsx')
 * @param {String} sheetName - Nombre de la hoja (opcional)
 */
export const exportToExcel = (data, filename = 'archivo.xlsx', sheetName = 'Hoja1') => {
  if (!data || data.length === 0) {
    console.warn('No hay datos para exportar.');
    return;
  }

  // Crea la hoja con los datos
  const worksheet = XLSX.utils.json_to_sheet(data);
  const workbook = XLSX.utils.book_new();

  // Adjunta la hoja al libro
  XLSX.utils.book_append_sheet(workbook, worksheet, sheetName);

  // Escribe el archivo en formato array
  const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });

  // Crea un Blob y guarda el archivo
  const blob = new Blob([excelBuffer], { type: 'application/octet-stream' });
  saveAs(blob, filename);

  console.log(`Archivo ${filename} exportado correctamente.`);
};
