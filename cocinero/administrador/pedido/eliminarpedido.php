<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si la variable de conexión no está definida e incluir el archivo de conexión
if (!isset ($pdo)) {
    include "../permisos/conexion.php";
}

if (!isset ($_SESSION['rol'])) {
    header('Location: ../../index.php');
    exit;
}

// Verificar si se recibieron los parámetros necesarios
if (isset ($_GET['id_pedido']) && isset ($_GET['id_sala']) && isset ($_GET['mesa'])) {
    try {
        $id_pedido = $_GET['id_pedido'];

        // Actualizar el estado del pedido a 'ELIMINADO'
        $stmtUpdatePedido = $pdo->prepare("UPDATE pedidos SET estado = 'ELIMINADO' WHERE id = :id_pedido AND estado = 'PENDIENTE'");
        $stmtUpdatePedido->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmtUpdatePedido->execute();

        // Verificar si se actualizó correctamente
        $rowsUpdated = $stmtUpdatePedido->rowCount();

        if ($rowsUpdated > 0) {
            $_SESSION['alert'] = array(
                'type' => 'success',
                'message' => 'Pedido actualizado a "ELIMINADO" correctamente'
            );
            // Redirigir a la página de mesas con un mensaje de éxito
            $redirectUrl = '../mesas.php?id_sala=' . $_GET['id_sala'] . '&mesa=' . $_GET['mesa'] . '&success=1';
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            // Si no se actualizó ningún registro o el pedido no existe
            $errorUrl = '../mesas.php?error=1&id_sala=' . $_GET['id_sala'] . '&mesa=' . $_GET['mesa'];
            header('Location: ' . $errorUrl);
            exit;
        }

    } catch (PDOException $e) {
        // Manejar error
        echo "Error en la actualización del estado del pedido: " . $e->getMessage();
        exit;
    }
} else {
    // Si no se proporcionaron los parámetros necesarios, redirigir a la página de mesas con un mensaje de error
    $errorUrl = '../mesas.php?error=1';
    header('Location: ' . $errorUrl);
    exit;
}
?>
