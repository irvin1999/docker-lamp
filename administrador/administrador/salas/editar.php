<?php
session_start();
date_default_timezone_set('Europe/Madrid');
include('../permisos/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recuperar los datos del formulario
    $idSala = $_POST['id_sala'];
    $nombreSala = $_POST['nombre'];
    $mesasSala = $_POST['mesas'];

    try {
        // Construir la consulta SQL base
        $sql = "UPDATE salas SET nombre = :nombre, mesas = :mesas WHERE id = :id";

        // Preparar la consulta
        $stmt = $pdo->prepare($sql);

        // Asignar los valores a los parámetros
        $stmt->bindParam(':nombre', $nombreSala, PDO::PARAM_STR);
        $stmt->bindParam(':mesas', $mesasSala, PDO::PARAM_INT);
        $stmt->bindParam(':id', $idSala, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();
        // Después de ejecutar la consulta con éxito
        $_SESSION['alert'] = array(
            'type' => 'success',
            'message' => 'sala editada correctamente'
        );
        // Redireccionar a la página de lista de salas después de la edición
        header("Location: ../registrosalas.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        echo " Código de error: " . $e->getCode();
    }
} else {
    // Si no se envió el formulario de edición, redirigir a la página de lista de salas
    header("Location: ../registrosalas.php");
    exit();
}
?>