<?php

require('./fpdf.php');

class PDF extends FPDF
{

   // Cabecera de página
   function Header()
   {
      $this->Image('../../images/logoempresa.png', 185, 5, 20); // Logo de la empresa, posición y tamaño
      $this->SetFont('Arial', 'B', 19);
      $this->Cell(45);
      $this->SetTextColor(0, 0, 0);
      $this->Cell(110, 15, utf8_decode('HOTELEASE'), 1, 1, 'C', 0);
      $this->Ln(3);
      $this->SetTextColor(103);

      /* Otras líneas de información como ubicación, teléfono, etc. */
      $this->Ln(10);

      // Título de la tabla
      $this->SetTextColor(228, 100, 0);
      $this->Cell(50);
      $this->SetFont('Arial', 'B', 15);
      $this->Cell(100, 10, utf8_decode("Historial de asistencia"), 0, 1, 'C', 0);
      $this->Ln(7);

      // Cabecera de la tabla
      $this->SetFillColor(228, 100, 0);
      $this->SetTextColor(255, 255, 255);
      $this->SetDrawColor(255); // Establecer el color de las líneas a blanco
      $this->SetFont('Arial', 'B', 11);
      $this->Cell(18, 10, utf8_decode('N°'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('Ingreso'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('Salida'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('Fecha'), 1, 0, 'C', 1);
      $this->Cell(60, 10, utf8_decode('Nombre y Apellido'), 1, 1, 'C', 1);
   }

   // Pie de página
   function Footer()
   {
      $this->SetY(-15);
      $this->SetFont('Arial', 'I', 8);
      $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
   }
}

session_start();
include ('../permisos/conexion.php');

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 10);

// Obtener registros de asistencia
$query = $pdo->prepare("SELECT a.*, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo
FROM asistencia a
INNER JOIN usuarios u ON a.id_usuario = u.id");
$query->execute();
$asistencias = $query->fetchAll(PDO::FETCH_ASSOC);

// Establecer la codificación del documento
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(0);
$pdf->SetFillColor(224, 235, 255);
$pdf->SetMargins(10, 10, 10);

// Agregar una página al principio
$pdf->AddPage();

// Datos de la tabla
foreach ($asistencias as $row) {
    $pdf->Cell(18, 10, $row['id'], 0, 0, 'C', 0); // 0 para eliminar los bordes
    $pdf->Cell(40, 10, $row['ingreso'], 0, 0, 'C', 0); // 0 para eliminar los bordes
    $pdf->Cell(40, 10, $row['salida'], 0, 0, 'C', 0); // 0 para eliminar los bordes
    $pdf->Cell(40, 10, $row['fecha'], 0, 0, 'C', 0); // 0 para eliminar los bordes
    $pdf->Cell(60, 10, utf8_decode($row['nombre_completo']), 0, 1, 'C', 0); // 0 para eliminar los bordes
}

$pdf->Output('Historial Asistencia.pdf', 'D');
?>
