<?php
// sse_handler.php

// Incluir la conexión a la base de datos
include('../permisos/conexion.php');
include('see.php');

$sse = new SSE();

// Obtener el ID de sesión actual
$sessionID = $_GET['PHPSESSID'];

// Iniciar la conexión SSE
$sse->start($sessionID);

// Función para enviar eventos SSE
function sendSSE($event, $data)
{
    global $sse;
    $sse->sendEvent($event, $data);
}

// Establecer un límite de ejecución
set_time_limit(0);

while (true) {
    // Obtener la lista de pedidos actualizada
    $query = $pdo->prepare("SELECT p.*, s.nombre AS sala, u.nombre FROM pedidos p INNER JOIN salas s ON p.id_sala = s.id INNER JOIN usuarios u ON p.id_usuario = u.id");
    $query->execute();
    $pedidos = $query->fetchAll(PDO::FETCH_ASSOC);

    // Contar la cantidad de pedidos pendientes
    $pendientes = 0;
    foreach ($pedidos as $row) {
        if ($row['estado'] == 'PENDIENTE') {
            $pendientes++;
        }
    }

    // Enviar eventos SSE con la lista de pedidos y la cantidad de pendientes
    sendSSE('actualizarPedidos', array('html' => $pedidos, 'pendientes' => $pendientes));

    // Esperar antes de realizar la siguiente actualización
    sleep(5); // Actualiza cada 5 segundos, puedes ajustar esto según tus necesidades
}
?>
