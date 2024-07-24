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

    // Obtener el estado del pedido para verificar si se puede cobrar
    $query = "SELECT estado, SUM(precio * cantidad) as total FROM detalle_pedidos 
              JOIN pedidos ON detalle_pedidos.id_pedido = pedidos.id 
              WHERE detalle_pedidos.id_pedido = :id_pedido";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró un resultado
    if ($result === false || $result === null) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'No se pudo encontrar el pedido.'
        ];
        header("Location: ../historialventa.php");
        exit();
    }

    $estado_pedido = $result['estado'];
    $subtotal = $result['total'];

    // Verificar si el pedido está en un estado que permite el cobro
    if ($estado_pedido !== 'COMPLETADO') {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'No se puede cobrar el pedido porque no está completado. Estado actual: ' . $estado_pedido
        ];
        header("Location: ../historialventa.php");
        exit();
    }

    // Calcular IVA y total
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
    $stmt->bindParam(':id_usuario', $_SESSION['idusuario'], PDO::PARAM_INT);
    $stmt->bindParam(':total', $total, PDO::PARAM_STR);
    $stmt->execute();

    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Pedido cobrado con éxito.'
    ];

    header("Location: ../historialventa.php");
    exit();
}
?>
