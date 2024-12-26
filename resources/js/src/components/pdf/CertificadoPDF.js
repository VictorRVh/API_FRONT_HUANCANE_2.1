import jsPDF from "jspdf";
import "jspdf-autotable";

export function generateCertificate(data) {
  // Crear el documento PDF con orientación vertical
  const doc = new jsPDF("portrait", "mm", "a4");

  // Borde del certificado
 // doc.setLineWidth(1);
 // doc.setDrawColor(0, 0, 255); // Azul
 //  doc.rect(10, 10, 190, 277); // Margen exterior (ajustado para formato vertical)

  // Encabezado
  doc.setFont("times", "bold");
  doc.setFontSize(15);
  doc.text("CENTRO DE EDUCACIÓN TÉCNICO PRODUCTIVA", 105, 30, { align: "center" });

  doc.setFontSize(14);
  doc.text("PÚBLICO/PRIVADO", 105, 40, { align: "center" });

  doc.setFontSize(20);
  doc.text("CERTIFICADO MODULAR", 105, 50, { align: "center" });

  if (data.photoMinisterio) {
    try {
      doc.addImage(data.photoMinisterio, "PNG", 80, 10, 50, 10); // Ajusta la posición y tamaño
    } catch (error) {
      console.error("Error al agregar la foto:", error);
    }
  }
  
  if (data.logo) {
    try {
      doc.addImage(data.logo, "PNG", 10, 20, 30, 30); // Ajusta la posición y tamaño
    } catch (error) {
      console.error("Error al agregar la foto:", error);
    }
  }
  

  // Foto
  if (data.photo) {
    try {
      doc.addImage(data.photo, "PNG", 180, 25, 20, 25); // Ajusta la posición y tamaño
    } catch (error) {
      console.error("Error al agregar la foto:", error);
    }
  }

  // Texto principal
  doc.setFontSize(12);
  doc.setFont("times", "italic");
  doc.text("Otorgado a:", 20, 70);

  doc.setFont("times", "bold");
  doc.setFontSize(14);
  doc.text(data.name || "NOMBRE DEL BENEFICIARIO", 50, 70);

  doc.setFontSize(12);
  doc.setFont("times", "italic");
  doc.text("Por haber aprobado satisfactoriamente el módulo formativo:", 20, 80);

  doc.setFont("times", "bold");
  doc.text(data.module || "NOMBRE DEL MÓDULO", 105, 90, { align: "center" });

  doc.setFont("times", "italic");
  doc.text("Correspondiente al Programa de Estudios:", 20, 100);

  doc.setFont("times", "bold");
  doc.text(data.especialidad || "NOMBRE DE ESPECIALIDAD", 100, 100);
  // Detalles adicionales


  doc.setFontSize(12);
  doc.setFont("times", "italic");
  doc.text(
    `desarrollado del   ${data.startDate ||   "FECHA INICIO"}           al           ${data.endDate || "FECHA FIN"},      con un total de        ${
      data.credits || "X"
    }           créditos.`,
    20,
    110
  );

  doc.text(
    `equivalente a      ${data.hours ||   "FECHA INICIO"}       horas.`,
    20,
    115
  );

  doc.text(`Fecha y lugar: ${data.location || "LOCALIDAD Y FECHA"}`, 100, 130);

  // Pie de página con firmas
  doc.setFontSize(10);
  doc.text("DIRECCIÓN", 105, 160, { align: "center" });
  doc.text("(Firma, pos firma y sello)", 105, 165, { align: "center" });

// Datos de la tabla
const headers = [
  "Unidad de competencia",
  "Unidad didáctica",
  "N° de Créditos",
  "N° de Horas",
  "Capacidad",
  "Calificación",
];
const rows = Array.from({ length: 8 }, (_, i) => [
  ` ${i + 1}`,
  ` ${i + 1}`,
  ` ${i + 1}`,
  ` ${i + 1}`,
  ` ${i + 1}`,
  ` ${i + 1}`,
]);


// Generar la tabla con columnas específicas más anchas
doc.autoTable({
  startY: 180,
  head: [headers],
  body: rows,
  styles: {
    fontSize: 10,
    cellPadding: 1,
    lineWidth: 0.5,
    lineColor: [0, 0, 0], // Bordes negros
    halign: "center",
    valign: "middle",
  },
  headStyles: {
    fillColor: [200, 200, 200], // Color de fondo gris para los encabezados
    textColor: [0, 0, 0], // Texto negro para los encabezados
  },
  bodyStyles: {
    fillColor: [255, 255, 255], // Fondo blanco para las celdas
    textColor: [0, 0, 0], // Texto negro para las celdas
  },
  columnStyles: {
    0: { cellWidth: 30 }, // Columna 1 más ancha
    1: { cellWidth: 60 }, // Columna 2 más ancha
    4: { cellWidth: 50 }, // Columna 5 más ancha
  },
  didDrawCell: (data) => {
    // Asegúrate de que estás trabajando en la primera fila y primera columna
    if (data.section === "body" && data.column.index === 0 && data.row.index === 0) {
      const posX = data.cell.x; // Coordenada X inicial de la celda
      const posY = data.cell.y; // Coordenada Y inicial de la celda
      const cellWidth = data.cell.width; // Ancho de la celda
      const cellHeight = 20; // Altura fija para cada celda
      const rowsToCombine = 7; // Número de filas a combinar
      const combinedHeight = cellHeight * rowsToCombine; // Altura total combinada
  
      


  
      // Detener el dibujo de las celdas individuales de las filas combinadas
      if (data.row.index < rowsToCombine) {
        return false;
      }
    }
  },
  
  
});

// Dibujar el rectángulo que representa la celda combinada
doc.setFillColor(255, 255, 255); // Fondo blanco
doc.rect(14.4, 195, 29.5,47.5, "F");

// Añadir texto centrado dentro de la celda combinada
doc.setFont("helvetica", "bold");

  // Obtener el PDF como un Blob
  const pdfBlob = doc.output("blob");

  // Crear una URL para el Blob
  const pdfUrl = URL.createObjectURL(pdfBlob);

  // Abrir el PDF en una nueva pestaña
  window.open(pdfUrl, "_blank");
}
