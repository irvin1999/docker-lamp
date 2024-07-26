<?php
// Incluir el archivo de conexión a la base de datos
include('../permisos/conexion.php');

// Verificar si se recibió el id_pedido
if (isset($_POST['id_pedido'])) {
    $id_pedido = intval($_POST['id_pedido']);

    try {
        // Obtener el estado del pedido
        $stmt = $pdo->prepare("SELECT estado FROM pedidos WHERE id = :id_pedido");
        $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmt->execute();
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el pedido existe y su estado es 'COBRADO' o 'ELIMINADO'
        if ($pedido && ($pedido['estado'] === 'COBRADO' || $pedido['estado'] === 'ELIMINADO')) {
            // Iniciar una transacción
            $pdo->beginTransaction();

            // Eliminar primero las facturas relacionadas con el pedido
            $stmt = $pdo->prepare("DELETE FROM facturas WHERE id_pedido = :id_pedido");
            $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $stmt->execute();

            // Luego eliminar los detalles del pedido
            $stmt = $pdo->prepare("DELETE FROM detalle_pedidos WHERE id_pedido = :id_pedido");
            $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $stmt->execute();

            // Finalmente, eliminar el pedido en sí
            $stmt = $pdo->prepare("DELETE FROM pedidos WHERE id = :id_pedido");
            $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $stmt->execute();

            // Confirmar la transacción
            $pdo->commit();

            // Configurar mensaje de éxito en sesión
            session_start();
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Pedido eliminado correctamente'
            ];
        } else {
            // Configurar mensaje de advertencia si el pedido no puede ser eliminado
            session_start();
            $_SESSION['alert'] = [
                'type' => 'warning',
                'message' => 'Este pedido no se puede eliminar porque no está finalizado.'
            ];
        }

    } catch (PDOException $e) {
        // En caso de error, deshacer la transacción
        $pdo->rollBack();
        
        // Configurar mensaje de error en sesión
        session_start();
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Error al eliminar el pedido: ' . $e->getMessage()
        ];
    }
}

// Redirigir a la página principal u otra página
header("Location: ../historialventa.php");
exit;
?>
