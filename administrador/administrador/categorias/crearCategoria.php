<?php
session_start();
include '../permisos/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];

    // Insertar nueva categoría
    $sql = "INSERT INTO categorias (nombre) VALUES (:nombre)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);

    if ($stmt->execute()) {
        $_SESSION['alert'] = array(
            'type' => 'success',
            'message' => 'Categoría creada correctamente'
        );
        header("Location: ../RegistroCategoria.php"); // Redirigir después de registrar
        exit();
    } else {
        echo "Error al registrar la categoría.";
    }
}
?>
