<?php
session_start();
date_default_timezone_set('Europe/Madrid');
include('../permisos/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id_sala = $_GET['id'];

    // Verificar que el ID sea un entero válido
    if (!ctype_digit($id_sala)) {
        echo "Error: ID de sala no válido";
        exit();
    }

    // Eliminar la sala de la base de datos
    $sql = "DELETE FROM salas WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_sala, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Después de ejecutar la consulta con éxito
        $_SESSION['alert'] = array(
            'type' => 'success',
            'message' => 'sala eliminado correctamente'
        );
        header("Location: ../registrosalas.php");
        exit();
    } else {
        echo "Error al eliminar la sala.";
    }
} else {
    header("Location: ../registrosalas.php");
}
?>