import jsPDF from "jspdf";
import "jspdf-autotable";

export function generateCertificate(data) {
  // Crear el documento PDF con orientación horizontal
  const doc = new jsPDF("landscape", "mm", "a4");

  // Borde del certificado
  doc.setLineWidth(1);
  doc.setDrawColor(0, 0, 255); // Azul
  doc.rect(10, 10, 277, 190); // Margen exterior (ajustado para formato horizontal)

  const posX = 0;
  const posY = 20;
  // Encabezado
  doc.setFont("Arial", "bold");
  doc.setFontSize(16);
  doc.text("CENTRO DE EDUCACIÓN TÉCNICO PRODUCTIVA PÚBLICO ", 148.5, posY + 20, { align: "center" });

  doc.setFontSize(16);
  doc.text('"HUANCANÉ"', 148.5, posY + 30, { align: "center" });

  doc.setFontSize(23);
  doc.text("CERTIFICADO MODULAR", 148.5, posY + 40, { align: "center" });

  // Logos
  if (data.photoMinisterio) {
    try {
      doc.addImage(data.photoMinisterio, "PNG", 120, 20, 40, 10); // Ajusta la posición y tamaño
    } catch (error) {
      console.error("Error al agregar la foto:", error);
    }
  }

  if (data.logo) {
    try {
      doc.addImage(data.logo, "PNG", 20, 25, 30, 30); // Ajusta la posición y tamaño
    } catch (error) {
      console.error("Error al agregar el logo:", error);
    }
  }

  if (data.photo) {
    try {
      doc.addImage(data.photo, "PNG", 255, 25, 20, 25); // Ajusta la posición y tamaño
    } catch (error) {
      console.error("Error al agregar la foto:", error);
    }
  }

  // Texto principal
  doc.setFontSize(16);
  doc.setFont("times", "italic");
  doc.text("Otorgado a:", 20, posY + 60);

  doc.setFont("times", "bold");

  doc.text(data.name || "NOMBRE DEL BENEFICIARIO", 60, posY + 60);


  doc.setFont("times", "italic");
  doc.text("Por haber aprobado satisfactoriamente el módulo formativo:", 20, posY + 70);

  doc.setFont("times", "bold");
  doc.text(data.module || "NOMBRE DEL MÓDULO", 148.5, posY + 80, { align: "center" });

  doc.setFont("times", "italic");
  doc.text("Correspondiente al Programa de Estudios:", 20, posY + 90);

  doc.setFont("times", "bold");
  doc.text(data.especialidad || "NOMBRE DE ESPECIALIDAD", 148.5, posY + 100, { align: "center" });

  // Detalles adicionales

  // función de ñla fecha para convertir en fecha 
  function formatDateRangeFromSlash(startDate, endDate) {
    const months = [
      "enero", "febrero", "marzo", "abril", "mayo", "junio",
      "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
    ];

    const parseDate = (date) => {
      const [day, month, year] = date.split("/").map(Number);
      return { day, month: months[month - 1], year };
    };

    const start = parseDate(startDate);
    const end = parseDate(endDate);

    if (start.year === end.year) {
      if (start.month === end.month) {
        return `${start.day}  \t al  \t ${end.day} de ${start.month} del ${start.year}`;
      } else {
        return `${start.day} de ${start.month}  \t al  \t ${end.day} de ${end.month} del ${start.year}`;
      }
    } else {
      return `${start.day} de ${start.month} del ${start.year} al ${end.day} de ${end.month} del ${end.year}`;
    }
  }
  const formattedDateRange = formatDateRangeFromSlash(data.startDate, data.endDate);

  doc.setFont("times", "italic");
  doc.text(
    `desarrollado del \t ${formattedDateRange || "FECHA INICIO - FINAL"},  \t con un total de ${data.credits || "X"} créditos.`,
    20,
    posY + 110
  );
  /// ${ formatDateRange(data.startDate,data.endDate) || "FECHA INICIO - FINAL"}

  doc.text(`equivalente a ${data.hours || "X"} horas.`, 20, posY + 120);

  doc.setFontSize(13);
  doc.text(` ${data.location || "LOCALIDAD Y FECHA"}`, 240, posY + 140, { align: "center" });

  // Pie de página con firmas
  

  doc.text("(Firma, pos firma y sello)", 148.5, posY + 165, { align: "center" });


  doc.addPage();
  // Define los encabezados de la tabla
  doc.setFontSize(15);
  const headers = [
    "Unidad de competencia",
    "Unidad didáctica",
    "N° de Créditos",
    "N° de Horas",
    "Capacidad",
    "Calificación",
  ];

  // Define las filas de la tabla con `rowSpan` para la primera columna
  const rows = data.unidades.map((unidad, index) => {
    if (index === 0) {
      return [
        { content: "", rowSpan: data.unidades.length }, // Mostrar mensaje en lugar del contenido original
        unidad.unidad,   // La unidad real
        unidad.credito,  // El crédito
        unidad.hora,     // Las horas
        unidad.capacidad,// La capacidad
        "A", // Reemplazar "A" por el valor que necesites
      ];
    } else {
      return [
       
        unidad.unidad,   // La unidad real
        unidad.credito,  // El crédito
        unidad.hora,     // Las horas
        unidad.capacidad,// La capacidad
        "A",             // Puedes reemplazar "A" con otro valor si es necesario
      ];
    }
  });
  // Agregar fila extra para institución
  rows.push([
    { content: "Institución (es) en que realizo la experiencia:", colSpan: 3 },
    { content: 'CETPRO "HUANCANÉ"', colSpan: 3 },
  ]);

  function centerVertical(cellWidth, cellHeight, text, posXstart, posYend) {
    // Divide el texto en palabras
    const words = text.split(" ");
    let currentLine = "";
    let lines = [];

    // Divide las palabras en líneas según el ancho de la celda
    words.forEach((word) => {
      const testLine = currentLine + (currentLine ? " " : "") + word;
      const testWidth = doc.getTextWidth(testLine);
      if (testWidth < cellWidth - 4) { // Ajuste según el padding
        currentLine = testLine;
      } else {
        lines.push(currentLine);
        currentLine = word;
      }
    });

    if (currentLine) lines.push(currentLine);

    // Calcula la altura total del texto
    const lineHeight = 10; // Ajustar según el tamaño de fuente
    const totalTextHeight = lines.length * lineHeight;

    // Calcula la posición inicial y final para centrar verticalmente
    const verticalPadding = (cellHeight - totalTextHeight) / 2;

    // Genera el texto con saltos de línea
    const finalText = lines.join("\n");
    return finalText;
  }

  // Generar la tabla con texto rotado
  doc.autoTable({
    didDrawCell: function (data) {
      if (data.column.index === 0 && data.cell.raw && data.cell.raw.content) {
        const { x, y, height } = data.cell;
        const text = "Unidad 1: hola en la vida peroros la vida sin mi es laa hora loca";

        // Genera el texto centrado
        const textCentered = centerVertical(y-22, height, text, x, y);

        // Evitar que el texto horizontal predeterminado se dibuje
        data.cell.text = [];

        // Dibuja el texto rotado
        doc.saveGraphicsState();
        doc.text(textCentered, x + 10, y - 2, {
          align: "left",
          angle: 90, // Rota el texto 90 grados
        });
        doc.restoreGraphicsState();
      }
    },
    startY: 10,
    head: [headers],
    body: rows,
    styles: {
      fontSize: 13,
      cellPadding: 2,
      halign: "center",
      valign: "middle",
      lineWidth: 0.5,
      lineColor: [0, 0, 0],
    },
    columnStyles: {
      0: { cellWidth: 30 }, // Ancho ajustado para la primera columna
    },
  });


  // Descargar el PDF
  const pdfBlob = doc.output("blob");
  const pdfUrl = URL.createObjectURL(pdfBlob);
  window.open(pdfUrl, "_blank");
}
