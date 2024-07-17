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
            echo $estadoActual; // Devuelve el estado actual del usuario
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
