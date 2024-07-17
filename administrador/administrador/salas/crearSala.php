<?php
session_start();
date_default_timezone_set('Europe/Madrid');
include('../permisos/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreSala = $_POST['nombreSala'];
    $mesas = $_POST['mesas'];

    // Insertar en la base de datos
    $sql = "INSERT INTO salas (nombre, mesas) VALUES (:nombreSala, :mesas)";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':nombreSala', $nombreSala);
    $stmt->bindParam(':mesas', $mesas);

    if ($stmt->execute()) {
        // Después de ejecutar la consulta con éxito
        $_SESSION['alert'] = array(
            'type' => 'success',
            'message' => 'sala creada correctamente'
        );
        header("Location: ../registrosalas.php"); // Redireccionar después de registrar
        exit();
    } else {
        echo "Error al registrar la sala.";
    }
}
?>