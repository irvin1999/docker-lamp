<?php
session_start();
date_default_timezone_set('Europe/Madrid');
// Incluye el archivo de conexión a la base de datos con PDO
include('../administrador/permisos/conexion.php');

// Verifica si se recibió el ID del usuario a eliminar
if (isset($_GET['id'])) {
    $idUsuario = $_GET['id'];

    try {
        // Comienza una transacción
        $pdo->beginTransaction();

        // Consulta SQL para eliminar los registros de detalle_pedidos asociados a los pedidos del usuario
        $sqlDeleteDetallePedidos = "DELETE dp FROM detalle_pedidos dp INNER JOIN pedidos p ON dp.id_pedido = p.id WHERE p.id_usuario = :id";

        // Prepara y ejecuta la consulta para eliminar los registros de detalle_pedidos
        $stmtDeleteDetallePedidos = $pdo->prepare($sqlDeleteDetallePedidos);
        $stmtDeleteDetallePedidos->bindParam(':id', $idUsuario, PDO::PARAM_INT);
        $stmtDeleteDetallePedidos->execute();

        // Consulta SQL para eliminar los pedidos asociados al usuario por su ID
        $sqlDeletePedidos = "DELETE FROM pedidos WHERE id_usuario = :id";

        // Prepara y ejecuta la consulta para eliminar los pedidos
        $stmtDeletePedidos = $pdo->prepare($sqlDeletePedidos);
        $stmtDeletePedidos->bindParam(':id', $idUsuario, PDO::PARAM_INT);
        $stmtDeletePedidos->execute();

        // Consulta SQL para eliminar el registro de asistencia del usuario por su ID
        $sqlDeleteAsistencia = "DELETE FROM asistencia WHERE id_usuario = :id";

        // Prepara y ejecuta la consulta para eliminar el registro de asistencia
        $stmtDeleteAsistencia = $pdo->prepare($sqlDeleteAsistencia);
        $stmtDeleteAsistencia->bindParam(':id', $idUsuario, PDO::PARAM_INT);
        $stmtDeleteAsistencia->execute();

        // Consulta SQL para eliminar al usuario por su ID
        $sqlDeleteUsuario = "DELETE FROM usuarios WHERE id = :id";

        // Prepara y ejecuta la consulta para eliminar al usuario
        $stmtDeleteUsuario = $pdo->prepare($sqlDeleteUsuario);
        $stmtDeleteUsuario->bindParam(':id', $idUsuario, PDO::PARAM_INT);
        $stmtDeleteUsuario->execute();

        // Confirma la transacción
        $pdo->commit();

        // Éxito: Usuario eliminado correctamente
        $_SESSION['alert'] = array(
            'type' => 'success',
            'message' => 'Usuario, sus pedidos y su registro de asistencia eliminados correctamente'
        );
        header("Location: ../administrador/agregar.php"); // Redirige de nuevo a la lista de usuarios
        exit;
    } catch (PDOException $e) {
        // Si ocurre algún error, se revierte la transacción
        $pdo->rollBack();

        // Manejo de excepciones en caso de error
        echo "Error en la conexión: " . $e->getMessage();
    }
} else {
    // Error: No se proporcionó el ID del usuario a eliminar
    echo "ID de usuario no proporcionado.";
}

// Cierra la conexión a la base de datos
$pdo = null;
?>
