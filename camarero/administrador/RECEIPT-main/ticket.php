<?php
require "./code128.php";
require '../permisos/conexion.php'; // Conexión a la base de datos

// Asegúrate de que se reciban los parámetros
if (!isset($_GET['id_pedido']) || !isset($_GET['total'])) {
    die('Faltan parámetros');
}

$id_pedido = $_GET['id_pedido'];
$total = $_GET['total'];

// Consultar detalles del pedido para el ticket
$query = "SELECT
            p.num_mesa,
            p.fecha AS fecha_pedido,
            p.total AS total_pedido,
            f.fecha AS fecha_factura,
            f.total AS total_factura,
            u.nombre AS usuario_nombre,
            d.cantidad,
            d.precio AS precio_producto,
            pl.nombre AS nombre_producto
          FROM
            pedidos p
          JOIN
            facturas f ON p.id = f.id_pedido
          JOIN
            detalle_pedidos d ON p.id = d.id_pedido
          JOIN
            platos pl ON d.nombre = pl.nombre
          JOIN
            usuarios u ON p.id_usuario = u.id
          WHERE
            p.id = :id_pedido";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener el nombre del cajero (usuario)
$cajero = "";
if (!empty($productos)) {
    $cajero = $productos[0]['usuario_nombre'];
}

$pdf = new PDF_Code128('P', 'mm', array(80, 258));
$pdf->SetMargins(4, 10, 4);
$pdf->AddPage();

// Encabezado y datos de la empresa
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", strtoupper("Nombre de empresa")), 0, 'C', false);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "RUC: 0000000000"), 0, 'C', false);
$pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Direccion San Salvador, El Salvador"), 0, 'C', false);
$pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Teléfono: 00000000"), 0, 'C', false);
$pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Email: correo@ejemplo.com"), 0, 'C', false);

$pdf->Ln(1);
$pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "------------------------------------------------------"), 0, 0, 'C');
$pdf->Ln(5);

// Datos del pedido
$pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Fecha: " . date("d/m/Y") . " " . date("h:s A")), 0, 'C', false);
$pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Caja Nro: 1"), 0, 'C', false);
$pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Cajero: $cajero"), 0, 'C', false);  // Nombre del cajero
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Ticket Nro: $id_pedido"), 0, 'C', false);
$pdf->SetFont('Arial', '', 9);

$pdf->Ln(1);
$pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "------------------------------------------------------"), 0, 0, 'C');
$pdf->Ln(5);

// Detalles del pedido
$pdf->Cell(10, 5, iconv("UTF-8", "ISO-8859-1", "Cant."), 0, 0, 'C');
$pdf->Cell(19, 5, iconv("UTF-8", "ISO-8859-1", "Precio"), 0, 0, 'C');
$pdf->Cell(15, 5, iconv("UTF-8", "ISO-8859-1", "Desc."), 0, 0, 'C');
$pdf->Cell(28, 5, iconv("UTF-8", "ISO-8859-1", "Total"), 0, 0, 'C');

$pdf->Ln(3);
$pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');
$pdf->Ln(3);

$total_subtotal = 0;

// Iterar sobre los productos y calcular el total subtotal
foreach ($productos as $producto) {
    $total_producto = $producto['precio_producto'] * $producto['cantidad'];
    $total_subtotal += $total_producto;

    $pdf->MultiCell(0, 4, iconv("UTF-8", "ISO-8859-1", $producto['nombre_producto']), 0, 'C', false);
    $pdf->Cell(10, 4, iconv("UTF-8", "ISO-8859-1", $producto['cantidad']), 0, 0, 'C');
    $pdf->Cell(19, 4, iconv("UTF-8", "ISO-8859-1", "$" . number_format($producto['precio_producto'], 2) . " USD"), 0, 0, 'C');
    $pdf->Cell(15, 4, iconv("UTF-8", "ISO-8859-1", "$0.00 USD"), 0, 0, 'C'); // Asumimos sin descuento
    $pdf->Cell(28, 4, iconv("UTF-8", "ISO-8859-1", "$" . number_format($total_producto, 2) . " USD"), 0, 0, 'C');
    $pdf->Ln(4);
}

// Calcular IVA y total
$iva = $total_subtotal * 0.13;
$subtotal = $total_subtotal - $iva;

$pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');
$pdf->Ln(5);

$pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
$pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "SUBTOTAL"), 0, 0, 'C');
$pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", "+ $" . number_format($subtotal, 2) . " USD"), 0, 0, 'C');

$pdf->Ln(5);

$pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
$pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "IVA (13%)"), 0, 0, 'C');
$pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", "+ $" . number_format($iva, 2) . " USD"), 0, 0, 'C');

$pdf->Ln(5);

$pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');
$pdf->Ln(5);

$pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
$pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "TOTAL A PAGAR"), 0, 0, 'C');
$pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", "$" . number_format($total_subtotal, 2) . " USD"), 0, 0, 'C');

$pdf->Ln(10);
$pdf->Cell(0, 10, iconv("UTF-8", "ISO-8859-1", "Gracias por su compra"), 0, 0, 'C');

// Cerrar y mostrar el PDF
$pdf->Output();
?>
