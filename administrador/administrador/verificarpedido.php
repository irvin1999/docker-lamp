<?php
// Establecer la conexión a la base de datos
include('../permisos/conexion.php');

// Realizar la consulta para verificar el total de pedidos según su estado
$query = $pdo->prepare("SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE estado IN ('PENDIENTE', 'COMPLETADO', 'ELIMINADO')");
$query->execute();
$resultado = $query->fetch(PDO::FETCH_ASSOC);
$total_pedidos = $resultado['total_pedidos'];

// Devolver el total de pedidos como respuesta
echo json_encode(['total' => $total_pedidos]);
?>
