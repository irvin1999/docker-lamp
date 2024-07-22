<?php
session_start();
print_r($_SESSION); // Esto te mostrará el contenido actual de la sesión
require '../permisos/conexion.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['id_usuario']) || empty($_SESSION['id_usuario'])) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'No se ha encontrado el ID de usuario en la sesión.'
        ];
        header("Location: ../historialventa.php");
        exit();
    }

    $id_pedido = $_POST['id_pedido'];

    // Obtener el total del pedido desde la tabla detalle_pedidos
    $query = "SELECT SUM(precio * cantidad) as total FROM detalle_pedidos WHERE id_pedido = :id_pedido";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $subtotal = $result['total'];
    $iva = $subtotal * 0.13;
    $total = $subtotal + $iva;

    // Procesar el cobro y actualizar el estado del pedido a 'COBRADO'
    $query = "UPDATE pedidos SET estado = 'COBRADO' WHERE id = :id_pedido";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $stmt->execute();

    // Crear una nueva entrada en la tabla facturas
    $query = "INSERT INTO facturas (id_pedido, id_usuario, total) VALUES (:id_pedido, :id_usuario, :total)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $stmt->bindParam(':id_usuario', $_SESSION['id_usuario'], PDO::PARAM_INT);
    $stmt->bindParam(':total', $total, PDO::PARAM_STR);
    $stmt->execute();

    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Pedido cobrado con éxito.'
    ];

    header("Location: ../historialventa.php");
    exit();
}
