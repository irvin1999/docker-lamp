<?php
session_start();
date_default_timezone_set('Europe/Madrid');
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    try {
        $idUsuario = $_POST['id'];

        // Verificar el estado actual del usuario
        $sqlEstadoActual = "SELECT activo FROM usuarios WHERE id = :id";
        $stmtEstadoActual = $pdo->prepare($sqlEstadoActual);
        $stmtEstadoActual->bindParam(':id', $idUsuario, PDO::PARAM_INT);

        if ($stmtEstadoActual->execute()) {
            $estadoActual = $stmtEstadoActual->fetchColumn();

            // Cambiar el estado del usuario en la base de datos
            $nuevoEstado = ($estadoActual == 1) ? 0 : 1;
            $sqlActualizarEstado = "UPDATE usuarios SET activo = :nuevoEstado WHERE id = :id";
            $stmtActualizarEstado = $pdo->prepare($sqlActualizarEstado);
            $stmtActualizarEstado->bindParam(':nuevoEstado', $nuevoEstado, PDO::PARAM_INT);
            $stmtActualizarEstado->bindParam(':id', $idUsuario, PDO::PARAM_INT);

            if ($stmtActualizarEstado->execute()) {
                echo "success"; // Devuelve "success" si la actualización es exitosa
            } else {
                echo "Error al cambiar el estado del usuario: " . implode(", ", $stmtActualizarEstado->errorInfo());
            }
        } else {
            echo "Error al obtener el estado actual del usuario.";
        }
    } catch (PDOException $e) {
        echo "Error en la conexión PDO: " . $e->getMessage();
    }
} else {
    echo "ID de usuario no proporcionado en la solicitud.";
}
?>