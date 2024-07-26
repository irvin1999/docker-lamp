<?php
session_start();
require '../permisos/conexion.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar si el ID de usuario está disponible en la sesión
    if (!isset($_SESSION['idusuario']) || empty($_SESSION['idusuario'])) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'No se ha encontrado el ID de usuario en la sesión.'
        ];
        header("Location: ../historialventa.php");
        exit();
    }

    $id_pedido = $_POST['id_pedido'];

    // Obtener los detalles del pedido para calcular el total
    $query = "SELECT detalle_pedidos.nombre, detalle_pedidos.cantidad, detalle_pedidos.precio 
              FROM detalle_pedidos 
              WHERE detalle_pedidos.id_pedido = :id_pedido";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $stmt->execute();
    $pedido_detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$pedido_detalle) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'No se pudo encontrar el pedido.'
        ];
        header("Location: ../historialventa.php");
        exit();
    }

    $total = 0;
    foreach ($pedido_detalle as $row) {
        $total += $row['cantidad'] * $row['precio'];
    }

    // Calcular el IVA y el subtotal
    $iva = $total * 0.13;
    $subtotal = $total - $iva;

    // Verificar si el pedido está en un estado que permite el cobro
    $query = "SELECT estado FROM pedidos WHERE id = :id_pedido";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result === false || $result === null) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'No se pudo encontrar el pedido.'
        ];
        header("Location: ../historialventa.php");
        exit();
    }

    $estado_pedido = $result['estado'];

    if ($estado_pedido !== 'COMPLETADO') {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'No se puede cobrar el pedido porque no está completado. Estado actual: ' . $estado_pedido
        ];
        header("Location: ../historialventa.php");
        exit();
    }

    // Procesar el cobro y actualizar el estado del pedido a 'COBRADO'
    $query = "UPDATE pedidos SET estado = 'COBRADO' WHERE id = :id_pedido";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $stmt->execute();

    // Crear una nueva entrada en la tabla facturas
    $query = "INSERT INTO facturas (id_pedido, id_usuario, total) VALUES (:id_pedido, :id_usuario, :total)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $stmt->bindParam(':id_usuario', $_SESSION['idusuario'], PDO::PARAM_INT);
    $stmt->bindParam(':total', $total, PDO::PARAM_STR);
    $stmt->execute();

    // Redirigir a ticket.php con parámetros
    header("Location: ../RECEIPT-main/ticket.php?id_pedido=$id_pedido&total=$total");
    exit();
}
?>
